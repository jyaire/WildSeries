<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{
    /**
     * Show all rows from Program's entity
     * @Route("wild/", name="wild_index")
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
        };
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

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("wild/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="show")
     * @return Response
     */
    public function showByProgram(?string $slug):Response
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
        $id_program = $program->getId();
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program_id' => $id_program]);

        return $this->render('show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
            'seasons' => $seasons,
        ]);
    }

    /**
     * Getting a season with a id for season
     *
     * @param string|null $id
     * @return Response
     * @Route("show/season/{id}", name="show_season")
     */
    public function showBySeason(?string $id):Response
    {
        if (!$id) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a season.');
        }

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $id]);
        if (!$season) {
            throw $this->createNotFoundException(
                'No season with id = '.$id.', found.'
            );
        }
        $program = $season->getProgramId();
        $episodes = $season->getEpisodes();

        return $this->render('show_season.html.twig', [
            'season' => $season,
            'episodes' => $episodes,
            'program' => $program,
        ]);
    }

    /**
     * Getting a season with a id for season
     *
     * @param Episode $episode
     * @return Response
     * @Route("show/episode/{id}", name="show_episode")
     */
    public function showEpisode(Episode $episode):Response
    {
        $season = $episode->getSeasonId();
        $program = $season->getProgramId();
        $program_title = $program->getTitle();
        $program_title = strtolower(str_replace(' ', '-', $program_title));

        return $this->render('show_episode.html.twig', [
            'episode' => $episode,
            'program' => $program,
            'season' => $season,
            'program_title' => $program_title,
        ]);
    }
}