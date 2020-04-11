<?php

namespace App\Controller;

use App\Repository\RecipeImageRepository;
use App\Repository\RecipeRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * @Route("/recipes", name="recipes")
     */
    public function index(RecipeRepository $recipeRepo, RecipeImageRepository $recipeImageRepo)
    {
        $user = $this->getUser();

        /**
         * Check if user is connected
         */
        if (!$user) return $this->json([
            'code'      => 403,
            'message'   => 'Unauthorized'
        ], 403);

        $userId = $user->getId();

        /**
         * Get all ingredients for current user
         */
        $recipesResponse = $recipeRepo->findBy([
            'user' => (int) $userId
        ]);

        /**
         * Get all images about current recipe
         */
        $recipeImagesResponse = $recipeImageRepo->findBy([
            'recipe' => $recipesResponse
        ]);

        $recipes = [];

        /**
         * Put the relevant information in a array
         */
        foreach ($recipesResponse as $index => $recipeResponse) {
            array_push($recipes, [
                'id' => $recipeResponse->getId(),
                'name' => $recipeResponse->getName(),
                'instructions' => $recipeResponse->getInstructions(),
                'time' => $recipeResponse->getTime(),
                'difficulty' => $recipeResponse->getDifficulty(),
                'price' => $recipeResponse->getPrice(),
                'shared' => $recipeResponse->getShared(),
            ]);
            foreach ($recipeImagesResponse as $recipeImageResponse) {
                array_push($recipes[$index], [
                    'id' => $recipeImageResponse->getId(),
                    'url' => $recipeImageResponse->getUrl(),
                ]);
            }
        }

        return $this->render('recipe/recipes.html.twig', [
            'recipes' =>  $recipes
        ]);
    }
}
