<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Program;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
* @Route("/programs", name="program_")
*/
class ProgramController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @return Response 
     */
    
    public function index(): Response
    { 
        $programs = $this->getDoctrine()
        ->getRepository(Program::class)
        ->findAll();

    return $this->render('program/index.html.twig', [
        'programs' => $programs
    ]);
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, name="show", methods={"GET"})
     * @return Response
     */
    public function show(Program $program): Response
    {
        if(!$program){
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        $seasons = $program->getSeasons();

    return $this->render('program/show.html.twig', [
        'program' => $program,
        'seasons' => $seasons
    ]);
    }

    /**
     * @Route("/{program}/seasons/{season}", requirements={"season"="\d+"}, name="season_show", methods={"GET"})
     * @return Response
     */

    public function showSeason(Program $program, Season $season): Response
    {
        $episodes = $season->getEpisodes();

    return $this->render('program/season_show.html.twig', [
        'program' => $program,
        'season' => $season,
        'episodes' => $episodes
    ]);
    }
}
