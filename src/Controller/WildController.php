<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{
    /**
     * Show all rows from Program's entity
     * @Route("/wild", name="wild_index")
     * @return Response A response instance
     */
    public function index() :Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        return $this->render(
            'wild/index.html.twig', [
            'programs' => $programs,
        ]);
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("wild/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="show")
     * @return Response
     */
    public function show(?string $slug):Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
        ]);
    }

    /**
     * Getting a program with a formatted slug for categorie
     *
     * @param string $categoryName The Categorie
     * @Route("wild/category/{categoryName}", defaults={"categoryName" = null}, name="show_category")
     * @return Response
     */
    public function showByCategory(?string $categoryName):Response
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('Aucune catégorie fournie.');
        }
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneby(['name' => $categoryName]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category->getId()],
                    ['id' => 'desc'],
                    3
            );

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in category '.$categoryName.'.'
            );
        }

        return $this->render('wild/category.html.twig', [
            'programs' => $programs,
            'categoryName'  => $categoryName,
        ]);
    }
}