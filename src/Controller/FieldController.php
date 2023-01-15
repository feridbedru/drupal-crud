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
                $fieldRepository->save($field, true);
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
            $field->SetEntity($entity);;
            $fieldRepository->save($field, true);
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
