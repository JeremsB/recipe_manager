<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use App\Controller\IngredientController;
use App\Entity\RecipeImage;
use App\Repository\IngredientRepository;
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
     * Show one recipe
     * 
     * @Route("/recipe/{id}", name="recipe")
     *
     * @param [type] $id
     * @param RecipeRepository $recipeRepo
     * @param RecipeImageRepository $recipeImageRepo
     * @return void
     */
    public function show($id, RecipeRepository $recipeRepo, RecipeImageRepository $recipeImageRepo) {
        $user = $this->getUser();

        /**
         * Check if user is connected
         */
        if (!$user) return $this->json([
            'code'      => 403,
            'message'   => 'Unauthorized'
        ], 403);

        /**
         * Get all ingredients for current user
         */
        $recipeResponse = $recipeRepo->findOneBy([
            'id' => (int) $id
        ]);

        /**
         * Get all images about current recipe
         */
        $recipeImagesResponse = $recipeImageRepo->findBy([
            'recipe' => $recipeResponse
        ]);

        /**
         * Put the relevant information in a array
         */
        $recipe = [
            'id' => $recipeResponse->getId(),
            'name' => $recipeResponse->getName(),
            'instructions' => $recipeResponse->getInstructions(),
            'time' => $recipeResponse->getTime(),
            'difficulty' => $recipeResponse->getDifficulty(),
            'price' => $recipeResponse->getPrice(),
            'shared' => $recipeResponse->getShared(),
        ];

        $recipeImage = [];
        foreach ($recipeImagesResponse as $recipeImageResponse) {
            array_push($recipeImage, [
                'id' => $recipeImageResponse->getId(),
                'url' => $recipeImageResponse->getUrl(),
            ]);
        }

        return $this->render('recipe/recipe.html.twig', [
            'recipe' =>  $recipe,
            'recipeImage' => $recipeImage
        ]);
    }

    /**
     * Read shared recipes and set template
     * 
     * @Route("/shared-recipes", name="shared_recipes")
     *
     * @param RecipeRepository $recipeRepo
     * @param RecipeImageRepository $recipeImageRepo
     * @return void
     */
    public function sharedRecipes(RecipeRepository $recipeRepo, RecipeImageRepository $recipeImageRepo)
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
            'shared' => true
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

        return $this->render('recipe/shared-recipes.html.twig', [
            'recipes' =>  $recipes
        ]);
    }

    /**
     * Create new recipe
     * 
     * @Route("/recipe/create/{encodedIngredientsId}/{encodedData}/{encodedUrlsData}", name="create_recipe")
     *
     * @param [type] $encodedIngredientsId
     * @param [type] $encodedData
     * @param [type] $encodedUrlsData
     * @return Response
     */
    public function create($encodedIngredientsId, $encodedData, $encodedUrlsData, EntityManagerInterface $manager, IngredientRepository $ingredientRepo) : Response {
        $user = $this->getUser();

        /**
         * Check if user is connected
         */
        if (!$user) return $this->json([
            'code'      => 403,
            'message'   => 'Unauthorized'
        ], 403);

        // Decode ingredients id and data
        $ingredientsId = json_decode(IngredientController::decode($encodedIngredientsId));
        $data = json_decode(IngredientController::decode($encodedData));
        $urlsData = json_decode(IngredientController::decode($encodedUrlsData));

        //Create and set new recipe
        $recipe = new Recipe();

        $recipe->setName($data->name);
        $recipe->setInstructions($data->instructions);
        $recipe->setTime($data->time);
        $recipe->setDifficulty($data->difficulty);
        $recipe->setPrice($data->price);
        $recipe->setShared($data->shared);
        $recipe->setUser($user);
        
        // Recup ingredients by id and set them for recipe
        foreach ($ingredientsId as $id) {
            $ingredient = $ingredientRepo->findOneBy([ 'id' => (int) $id ]);
            $recipe->addIngredient($ingredient);
        }

        // Create new images and set them for recipe
        foreach ($urlsData as $urlData) {
            $recipeImage = new RecipeImage();
            $recipeImage->setUrl($urlData);

            $manager->persist($recipeImage);
            $manager->flush();
            
            $recipe->addImage($recipeImage);
        }

        $manager->persist($recipe);
        $manager->flush();

        return $this->json([
            'message'        => 'Success'
        ], 200);
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
     * View create recipe and get all ingredients
     * 
     * @Route("/create-recipe", name="create_recipe_view")
     *
     * @return void
     */
    public function createView(IngredientRepository $ingredientRepo) {
        $user = $this->getUser();

        /**
         * Check if user is connected
         */
        if (!$user) return $this->json([
            'code'      => 403,
            'message'   => 'Unauthorized'
        ], 403);

        $ingredientsResponse = $ingredientRepo->findBy(['user' => (int) $user->getId()]);

        $ingredients = [];
        /**
         * Put the relevant information in a array
         */
        foreach ($ingredientsResponse as $ingredientResponse) {
            array_push($ingredients, [
                'id' => $ingredientResponse->getId(),
                'name' => $ingredientResponse->getName(),
                'price' => $ingredientResponse->getPrice()
            ]);
        }

        return $this->render('recipe/create-recipe.html.twig', [
            'ingredients' => $ingredients
        ]);
    }
}
