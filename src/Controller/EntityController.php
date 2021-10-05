<?php

namespace App\Controller;

use App\Entity\Entity;
use App\Entity\Field;
use App\Form\EntityType;
use App\Repository\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/entity')]
class EntityController extends AbstractController
{
    #[Route('/', name: 'entity_index', methods: ['GET', 'POST'])]
    public function index(EntityRepository $entityRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if ($request->request->get('edit')) {

            $id = $request->request->get('edit');
            $entity = $entityRepository->findOneBy(['id' => $id]);
            $form = $this->createForm(EntityType::class, $entity);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash("success", "Updated entity successfully.");

                return $this->redirectToRoute('entity_index');
            }

            $queryBuilder = $entityRepository->findAll();
            // $queryBuilder = $entityRepository->findBy([], ['id' => 'DESC']);
            $data = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 30);

            return $this->render('entity/index.html.twig', [
                'entities' => $data,
                'form' => $form->createView(),
                'edit' => $id
            ]);
        }


        $entity = new Entity();
        $form = $this->createForm(EntityType::class, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entity);
            $entityManager->flush();
            $this->addFlash("success", "Registered entity successfully.");

            return $this->redirectToRoute('entity_index');
        }

        $queryBuilder = $entityRepository->findAll();
        $data = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 10);

        return $this->render('entity/index.html.twig', [
            'entities' => $data,
            'form' => $form->createView(),
            'edit' => false
        ]);
    }

    #[Route('/{id}', name: 'entity_show', methods: ['GET'])]
    public function show(Entity $entity): Response
    {
        return $this->render('entity/show.html.twig', [
            'entity' => $entity,
            'fields' => $entity->getFields(),
        ]);
    }

    #[Route('/{id}', name: 'entity_delete', methods: ['POST'])]
    public function delete(Request $request, Entity $entity): Response
    {
        // $this->denyAccessUnlessGranted('entity_delete');
        if ($this->isCsrfTokenValid('delete' . $entity->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($entity);
            $entityManager->flush();
        }
        $this->addFlash("success", "Deleted entity successfully.");

        return $this->redirectToRoute('entity_index');
    }


    #[Route('/{entity}/listfields', name: 'fields_list', methods: ['GET', 'POST'])]
    public function list(Entity $entity): Response
    {
        $em = $this->getDoctrine()->getManager();
        $entity_id = $entity->getId();

        $fields_repo = $em->getRepository(Field::class);
        $fields = $fields_repo->findBy(['entity' => $entity_id]);
        $fields_list = array();
        foreach ($fields as $key => $value) {
            $fields_list[$value->getId()] = $value->getName();
        }
        return new Response(json_encode($fields_list));
    }

    #[Route('/{entity}/project', name: 'project_append', methods: ['GET', 'POST'])]
    public function project(Entity $entity, EntityRepository $entityRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $project = $entity->getNamespace();
        $entities = $entityRepository->findBy(['namespace' => $project]);

        return $this->render('entity/append.html.twig', [
            'entities' => $entities,
            'project' => $project,
        ]);
    }

    #[Route('/{entity}/export', name: 'entity_export', methods: ['GET', 'POST'])]
    public function export(Entity $entity, EntityRepository $entityRepository): Response
    {
        $name = ucwords($entity->getSingularName());
        $name = str_replace(' ', '', $name);
        $current_dir_path = getcwd() . "/drupal/";

        //generates controller
        $controller = $this->render('entity/controller.html.twig', ['entity' => $entity, 'fields' => $entity->getFields()]);
        $controller = $controller->getContent();
        $new_file_path = $current_dir_path . "Controller/" . $name . "Controller.php";
        $this->processFile($controller, $new_file_path);

        //generates delete form
        $delete = $this->render('entity/delete.html.twig', ['entity' => $entity, 'fields' => $entity->getFields()]);
        $delete = $delete->getContent();
        $new_file_path = $current_dir_path . "DeleteForm/" . $name . "DeleteForm.php";
        $this->processFile($delete, $new_file_path);

        //generates filter form
        $filter = $this->render('entity/filter.html.twig', ['entity' => $entity, 'fields' => $entity->getFields()]);
        $filter = $filter->getContent();
        $new_file_path = $current_dir_path . "FilterForm/" . $name . "SearchForm.php";
        $this->processFile($filter, $new_file_path);

        //generates main form
        $mainform = $this->render('entity/mainform.html.twig', ['entity' => $entity, 'fields' => $entity->getFields()]);
        $mainform = $mainform->getContent();
        $new_file_path = $current_dir_path . "Form/" . $name . "Form.php";
        $this->processFile($mainform, $new_file_path);

        //generates block plugin
        $block = $this->render('entity/block.html.twig', ['entity' => $entity, 'fields' => $entity->getFields()]);
        $block = $block->getContent();
        $new_file_path = $current_dir_path . "Plugin/Block/" . $name . "Block.php";
        $this->processFile($block, $new_file_path);

        return $this->redirectToRoute('entity_index');
    }

    public function processFile($data, $location){
        $filesystem = new Filesystem();
        try {
            $filesystem->touch($location);
            $filesystem->chmod($location, 0777);
            $filesystem->dumpFile($location, $data);
        } catch (IOExceptionInterface $exception) {
            echo $exception;
        }
    }
}
