<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Season;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Category;
use App\Service\Slugify;
use App\Form\CommentType;
use App\Form\ProgramType;
use Symfony\Component\Mime\Email;
use App\Repository\CommentRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * The controller for the category add form
     *
     * @Route("/new", name="new")
     */
    public function new(Request $request, Slugify $slugify, MailerInterface $mailer) : Response
    {
        // Create a new Category Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            $programManager = $this->getDoctrine()->getManager();
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            // Persist Category Object
            $programManager->persist($program);
            // Flush the persisted object
            $programManager->flush();
            $email = (new TemplatedEmail())
                ->from($this->getParameter('mailer_from'))
                ->to('mendy.elvina@gmail.com')
                ->subject('A new program has just been published !')
                ->html($this->renderView('emails/newprogram.html.twig', ['program'=>$program]));
                $mailer->send($email);
            // Finally redirect to categories list
            return $this->redirectToRoute('program_index');
        }
        return $this->render('program/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="show", methods={"GET"})
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

        /**
     * @Route("/{programId}/season/{seasonId}/episode/{episodeId}", name="episode_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episodeId": "id"}})
     */

    public function showEpisode(Program $program, Season $season, Episode $episode, Request $request, CommentRepository $commentRepository): Response
    {
        $user = $this->getUser();
        $comment = new Comment();
        $comment->setAuthor($user);
        $comment->setEpisode($episode);
        // Create a new Comment Object
        // Create the associated Form
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            // Persist Category Object
            $entityManager->persist($comment);
            // Flush the persisted object
            $entityManager->flush();
        }
        $comments = $commentRepository->findBy(
            ['episode' => $episode], 
            ['id' => 'DESC'],
            5
        );


    return $this->render('program/episode_show.html.twig', [
        'program' => $program,
        'season' => $season,
        'episode' => $episode,
        'form' => $form->createView(),
        'comments' => $comments
        ]);
    }
}
