<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RecipeImageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * Read recipes of curret user and set template
     * 
     * @Route("/recipes", name="recipes")
     *
     * @param RecipeRepository $recipeRepo
     * @param RecipeImageRepository $recipeImageRepo
     * @return void
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

    /**
     * Delete one recipe
     * 
     * @Route("/recipe/delete/{id}", name="delete_recipe")
     *
     * @param [type] $id
     * @param EntityManagerInterface $manager
     * @param RecipeRepository $recipeRepo
     * @return Response
     */
    public function delete($id, EntityManagerInterface $manager, RecipeRepository $recipeRepo) : Response {
        $user = $this->getUser();

        /**
         * Check if user is connected
         */
        if (!$user) return $this->json([
            'code'      => 403,
            'message'   => 'Unauthorized'
        ], 403);

        $recipe = $recipeRepo->findOneBy(['id' => (int) $id]);

        $manager->remove($recipe);
        $manager->flush();

        return $this->json([
            'message'        => 'Success'
        ], 200);
    }

    /**
     * Delete images of a recipe
     * 
     * @Route("/recipe/deleteImages/{id}", name="delete_images_recipe")
     *
     * @param [int] $idRecipe
     * @param EntityManagerInterface $manager
     * @param RecipeImageRepository $imageRecipesRepo
     * @return Response
     */
    public function deleteImages($idRecipe, EntityManagerInterface $manager, RecipeImageRepository $imageRecipesRepo) : Response {
        $recipeImages = $imageRecipesRepo->findBy(['id' => (int) $idRecipe]);

        foreach ($recipeImages as $recipeImage) {
            $manager->remove($recipeImage);
        }

        $manager->flush();

        return $this->json([
            'message'        => 'Success'
        ], 200);
    }
}
