<?php
// src/Controller/CategoryController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;

/**
* @Route("/categories", name="category_")
*/
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @return Response 
     */
    public function index(): Response
    { 
        $categories = $this->getDoctrine()
        ->getRepository(Category::class)
        ->findAll();

    return $this->render('category/index.html.twig', [
        'categories' => $categories
    ]);
    }

    /**
     * The controller for the category add form
     *
     * @Route("/new", name="new")
     */
    public function new(Request $request) : Response
    {
        // Create a new Category Object
        $category = new Category();
        // Create the associated Form
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted()) {
            $categoryManager = $this->getDoctrine()->getManager();
            // Persist Category Object
            $categoryManager->persist($category);
            // Flush the persisted object
            $categoryManager->flush();
            // Finally redirect to categories list
            return $this->redirectToRoute('category_index');
        }
        return $this->render('category/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("/{categoryName}", name="show", methods={"GET"})
     * @return Response
     */
    public function show(string $categoryName): Response
    {
        $category = $this->getDoctrine()
        ->getRepository(Category::class)
        ->findBy(['name' => $categoryName]);

        if(!$category){
            throw $this->createNotFoundException(
                'No category' .$categoryName. 'found.'
            );
        } else {
            $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category],
            ['id'=> 'DESC'], 3
            );
        }

        return $this->render('category/show.html.twig', [
            'programs' => $programs,
            'category'=> $category
        ]);

    }
}