<?php

namespace App\Controller;

use App\Entity\Entity;
use App\Entity\Field;
use App\Form\FieldType;
use App\Repository\EntityRepository;
use App\Repository\FieldRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/entity/{entity}/field')]
class FieldController extends AbstractController
{
    #[Route('/', name: 'field_index', methods: ['GET', 'POST'])]
    public function index(FieldRepository $fieldRepository, PaginatorInterface $paginator, Request $request, EntityRepository $entityRepository): Response
    {
        $entity = $entityRepository->findOneBy(['id' => $request->attributes->get('entity')]);
        if($request->request->get('edit')){
            
            $id = $request->request->get('edit');
            $field = $fieldRepository->findOneBy(['id'=>$id]);
            $form = $this->createForm(FieldType::class, $field);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash("success","Updated field successfully.");

                return $this->redirectToRoute('field_index', ["entity" => $entity->getId()]);
            }

            $queryBuilder = $fieldRepository->findBy(['entity' => $entity]);
            $data = $paginator->paginate($queryBuilder, $request->query->getInt('page',1), 10 );

            return $this->render('field/index.html.twig', [
                'fields' => $data,
                'form' => $form->createView(),
                'edit' => $id,
                'entity' => $entity,
            ]);
        }

        
        $field = new Field();
        $form = $this->createForm(FieldType::class, $field);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $field->SetEntity($entity);
            $entityManager->persist($field);
            $entityManager->flush();
            $this->addFlash("success","Registered field successfully.");

            return $this->redirectToRoute('field_index', ["entity" => $entity->getId()]);
        }

        $queryBuilder = $fieldRepository->findBy(['entity' => $entity]);
        $data = $paginator->paginate($queryBuilder, $request->query->getInt('page',1), 10 );

        return $this->render('field/index.html.twig', [
            'fields' => $data,
            'form' => $form->createView(),
            'edit' => false,
            'entity' => $entity,
        ]);

    }

    #[Route('/listfields', name: 'fields_list', methods: ['GET'])]
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
        // return $this->render('entity/show.html.twig', [
        //     'entity' => $entity,
        //     'fields' => $entity->getFields(),
        // ]);
    }

    #[Route('/{id}', name: 'field_delete', methods: ['POST'])]
    public function delete(Request $request, Field $field, EntityRepository $entityRepository): Response
    {
        // $this->denyAccessUnlessGranted('field_delete');
        $entity = $entityRepository->findOneBy(['id' => $request->attributes->get('entity')]);
        if ($this->isCsrfTokenValid('delete'.$field->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($field);
            $entityManager->flush();
        }
        $this->addFlash("success","Deleted field successfully.");

        return $this->redirectToRoute('field_index', ["entity" => $entity->getId()]);
    }
}
