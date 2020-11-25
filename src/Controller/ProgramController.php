<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/programs", name="program_")
*/
class ProgramController extends AbstractController
{
    /**
     * @Route("/", name="program_index")
     */
    public function index(): Response
    {
    return $this->render('program/index.html.twig', [
        'website' => 'Wild Séries',
    ]);
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, name="show", methods={"GET"})
     */
    public function show(int $id): Response
    {
        //function to get the id of the program
    return $this->render('program/show.html.twig', [
        'id' => $id
    ]);
    }
}
