<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;

class CategoryController extends AbstractController
{
    /**
     * Show all rows from Program's entity
     * @Route("wild/category/", name="add_category")
     * @param Request $request
     * @return Response A response instance
     */
    public function add(Request $request): Response
    {
        $category = new Category();

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $form = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($form);
            $entityManager->flush();

            return $this->redirectToRoute('add_success');
        }

        return $this->render(
            'wild/category/index.html.twig', [
            'category' => $category,
            'categories' => $categories,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Show all rows from Program's entity
     * @Route("wild/category/ok", name="add_success")
     * @return Response A response instance
     */
    public function success(): Response
    {
        return $this->render(
            'wild/category/add_success.html.twig');
    }
}