<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Entity\Comment;
use App\Form\CommentType;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="blog")
     */
    public function index(): Response
    {
        return $this->render('blog/home.html.twig');
    }

    /**
     * @Route("/recipe", name="home")
     */
    public function home()
    {
        $repo = $this->getDoctrine()->getRepository(Recipe::class);

        $recipes = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'recipes' => $recipes
        ]);
    }
    
    /**
     * @Route("/recipe/new" , name="add_recipe")
     * @Route("/recipe/{id}/edit" , name="recipe_edit")
     */
    public function form(Recipe $recipe = null,Request $request,EntityManagerInterface  $manager)
    {
        if(!$recipe){
            $recipe = new Recipe;
        }

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$recipe->getId()){
                $recipe->setCreatedAt(new \DateTime());
            }

            $manager->persist($recipe);
            $manager->flush();

            return $this->redirectToRoute('recipe' , ['id' => $recipe->getId()]);
        }

        return $this->render('blog/create.html.twig' ,[
            'formRecipe' => $form->createView(),
            'editMode' => $recipe->getId() !== null

        ]);
    }
    /**
     * @Route ("/recipe/{id}", name="recipe")
     */
    public function recipe(Recipe $recipe, Request $request, EntityManagerInterface $manager)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
           $comment->setCreatedAt(new \DateTime())
                    ->setRecipe($recipe);

           $manager->persist($comment);
           $manager->flush();

           return $this->redirectToRoute('recipe', ['id' => $recipe->getId()]);
        }

        return $this->render('blog/recipe.html.twig',[
            'recipe' => $recipe,
            'commentForm' => $form->createView()
            ]
        );
    }
    
}
