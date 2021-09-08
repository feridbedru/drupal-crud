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

#[Route('/entity')]
class EntityController extends AbstractController
{
    #[Route('/', name: 'entity_index', methods: ['GET', 'POST'])]
    public function index(EntityRepository $entityRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if($request->request->get('edit')){
            
            $id = $request->request->get('edit');
            $entity = $entityRepository->findOneBy(['id'=>$id]);
            $form = $this->createForm(EntityType::class, $entity);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash("success","Updated entity successfully.");

                return $this->redirectToRoute('entity_index');
            }

            $queryBuilder = $entityRepository->findAll();
            // $queryBuilder = $entityRepository->findBy([], ['id' => 'DESC']);
            $data = $paginator->paginate($queryBuilder, $request->query->getInt('page',1), 30 );

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
            $this->addFlash("success","Registered entity successfully.");

            return $this->redirectToRoute('entity_index');
        }

        $queryBuilder = $entityRepository->findAll();
        $data = $paginator->paginate($queryBuilder, $request->query->getInt('page',1), 10 );

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
        if ($this->isCsrfTokenValid('delete'.$entity->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($entity);
            $entityManager->flush();
        }
        $this->addFlash("success","Deleted entity successfully.");

        return $this->redirectToRoute('entity_index');
    }


    #[Route('/{entity}/listfields', name: 'fields_list', methods: ['GET', 'POST'])]
    public function list(Entity $entity): Response
    {
        $em=$this->getDoctrine()->getManager();
        $entity_id = $entity->getId();

        $fields_repo=$em->getRepository(Field::class);
        $fields=$fields_repo->findBy(['entity'=>$entity_id]);
        $fields_list=array();
        foreach($fields as $key=>$value)
        {
            $fields_list[$value->getId()] = $value->getName();
        }
        return new Response(json_encode($fields_list));
    }

    #[Route('/{entity}/project', name: 'project_append', methods: ['GET', 'POST'])]
    public function project(Entity $entity,EntityRepository $entityRepository): Response
    {
        $em=$this->getDoctrine()->getManager();
        $project = $entity->getNamespace();
        $entities = $entityRepository->findBy(['namespace' => $project]);

        return $this->render('entity/append.html.twig', [
            'entities' => $entities,
            'fields' => $entity->getFields(),
            'project' => $project,
        ]);
    }
}
