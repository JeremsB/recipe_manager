<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use App\Controller\IngredientController;
use App\Entity\Mark;
use App\Entity\RecipeImage;
use App\Repository\IngredientRepository;
use App\Repository\MarkRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RecipeImageRepository;
use App\Repository\UserRepository;
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
         * Get all recipes for current user
         */
        $recipesResponse = $recipeRepo->findBy([
            'user' => (int) $userId
        ]);

        $recipes = [];

        /**
         * Put the relevant information in a array
         */
        foreach ($recipesResponse as $recipeResponse) {
            array_push($recipes, [
                'id' => $recipeResponse->getId(),
                'name' => $recipeResponse->getName(),
                'instructions' => $recipeResponse->getInstructions(),
                'time' => $recipeResponse->getTime(),
                'difficulty' => $recipeResponse->getDifficulty(),
                'price' => $recipeResponse->getPrice(),
                'shared' => $recipeResponse->getShared(),
                'images' => $recipeResponse->getImages()->getValues()
            ]);
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
    public function show($id, RecipeRepository $recipeRepo, IngredientRepository $ingredientRepo, RecipeImageRepository $recipeImageRepo) {
        $user = $this->getUser();

        /**
         * Check if user is connected
         */
        if (!$user) return $this->json([
            'code'      => 403,
            'message'   => 'Unauthorized'
        ], 403);

        /**
         * Get recipe
         */
        $recipeResponse = $recipeRepo->findOneBy([
            'id' => (int) $id
        ]);

        /**
         * Get all ingredients for current user
         */
        $ingredientsResponse = $recipeResponse->getIngredients()->getValues();


        /**
         * Get all images about current recipe
         */
        $recipeImagesResponse = $recipeImageRepo->findBy([
            'recipe' => $recipeResponse
        ]);

        /**
         * Get mark average
         */
        $rating = $recipeResponse->getMarks()->getValues();
        $average = 0;
        foreach ($rating as $object) {
            $average+= $object->getMark();
        }

        $average = $average / count($rating);

        /**
         * Get current user and creator of recipe
         */
        $currentUser = $this->getUser();
        $creatorUser = $recipeResponse->getUser();

        $sameUser = ($currentUser === $creatorUser) ? true : false;

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

        /**
         * Get ingredients of current recipe
         */
        $ingredients = [];
        foreach ($ingredientsResponse as $ingredientResponse) {
            array_push($ingredients, [
                'id' => $ingredientResponse->getId(),
                'name' => $ingredientResponse->getName(),
                'price' => $ingredientResponse->getPrice()
            ]);
        }

        /**
         * Get images of current recipe
         */
        $recipeImage = [];
        foreach ($recipeImagesResponse as $recipeImageResponse) {
            array_push($recipeImage, [
                'id' => $recipeImageResponse->getId(),
                'url' => $recipeImageResponse->getUrl(),
            ]);
        }

        return $this->render('recipe/recipe.html.twig', [
            'recipe' =>  $recipe,
            'ingredients' => $ingredients,
            'recipeImage' => $recipeImage,
            'sameUser' => $sameUser,
            'currentUserId' => $this->getUser()->getId(),
            'average' => $average
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
        foreach ($recipesResponse as $recipeResponse) {
            array_push($recipes, [
                'id' => $recipeResponse->getId(),
                'name' => $recipeResponse->getName(),
                'instructions' => $recipeResponse->getInstructions(),
                'time' => $recipeResponse->getTime(),
                'difficulty' => $recipeResponse->getDifficulty(),
                'price' => $recipeResponse->getPrice(),
                'shared' => $recipeResponse->getShared(),
                'images' => $recipeResponse->getImages()->getValues()
            ]);
        }

        /**
         * Get images of current recipe
         */
        $recipeImage = [];
        foreach ($recipeImagesResponse as $recipeImageResponse) {
            array_push($recipeImage, [
                'id' => $recipeImageResponse->getId(),
                'url' => $recipeImageResponse->getUrl(),
            ]);
        }

        return $this->render('recipe/shared-recipes.html.twig', [
            'recipes' =>  $recipes,
            'recipeImage' => $recipeImage
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
     * Update recipe
     * 
     * @Route("/recipe/update/{id}/{encodedIngredientsId}/{encodedData}/{encodedUrlsData}", name="update_recipe")
     *
     * @param [type] $id
     * @param [type] $encodedIngredientsId
     * @param [type] $encodedData
     * @param [type] $encodedUrlsData
     * @return Response
     */
    public function update($id, $encodedIngredientsId, $encodedData, $encodedUrlsData, EntityManagerInterface $manager, RecipeRepository $recipeRepo, IngredientRepository $ingredientRepo, RecipeImageRepository $recipeImageRepo) : Response {
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

        //Set new data for recipe
        $recipe = $recipeRepo->findOneBy(['id' => $id]);

        $recipe->setName($data->name);
        $recipe->setInstructions($data->instructions);
        $recipe->setTime($data->time);
        $recipe->setDifficulty($data->difficulty);
        $recipe->setPrice($data->price);
        $recipe->setShared($data->shared);
        $recipe->setUser($user);

        // Remove current ingredients on recipe
        $currentIngredients = $recipe->getIngredients();
        foreach ($currentIngredients as $ingredient) {
            $recipe->removeIngredient($ingredient);
        }

        // Recup ingredients by id and set them for recipe
        $ingredients = [];
        foreach ($ingredientsId as $id) {
            $ingredient = $ingredientRepo->findOneBy([ 'id' => (int) $id ]);
            $recipe->addIngredient($ingredient);
            $ingredients[] = $ingredient;
        }

        // Remove current image
        $currentImages = $recipe->getImages();
        foreach ($currentImages as $image) {
            $recipe->removeImage($image);
        }

        // Set images for recipe
        $images = [];
        foreach ($urlsData as $url) {
            $recipeImage = new RecipeImage();
            $recipeImage->setUrl($url);
            $recipeImage->setRecipe($recipe);

            $manager->persist($recipeImage);

            $recipe->addImage($recipeImage);

            $images[] = $url;
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

    /**
     * View update recipe
     * 
     * @Route("/update-recipe/{id}", name="update_recipe_view")
     *
     * @param integer $id
     * @param IngredientRepository $ingredientRepo
     * @return void
     */
    public function updateRecipe(int $id, RecipeRepository $recipeRepo, IngredientRepository $ingredientRepo, RecipeImageRepository $recipeImageRepo) {
        $user = $this->getUser();

        /**
         * Check if user is connected
         */
        if (!$user) return $this->json([
            'code'      => 403,
            'message'   => 'Unauthorized'
        ], 403);

        $recipe = $recipeRepo->findOneBy(['id' => (int) $id]);
        $ingredients = $recipe->getIngredients()->getValues();
        $allIngredientsResponse = $ingredientRepo->findBy(['user' => (int) $user->getId()]);
        $recipeImagesResponse = $recipeImageRepo->findBy([
            'recipe' => $recipe
        ]);

        $allIngredients = [];
        foreach ($allIngredientsResponse as $ingredientResponse) {
            array_push($allIngredients, [
                'id' => $ingredientResponse->getId(),
                'name' => $ingredientResponse->getName(),
                'price' => $ingredientResponse->getPrice(),
                'disabled' => false
            ]);
        }

        $flag = null;
        foreach ($allIngredients as $key => $oneIngredient) {
            $flag = false;
            foreach ($ingredients as $ingredient) {
                if($oneIngredient['id'] === $ingredient->getid()) {
                    $flag = true;
                    $allIngredients[$key]['disabled'] =  $flag;
                }
                if($flag) break;
            }   
        } 

        return $this->render('recipe/update-recipe.html.twig', [
            'recipe' => $recipe,
            'ingredients' => $ingredients,
            'allIngredients' => $allIngredients,
            'images' => $recipeImagesResponse
        ]);
    }
    
    /**
     * Set mark
     * 
     * @Route("recipe/set-mark/{idUser}/{idRecipe}/{markValue}", name="set_mark")
     *
     * @param integer $idUser
     * @param integer $idRecipe
     * @param integer $markValue
     * @param MarkRepository $markRepo
     * @return void
     */
    public function setMark(int $idUser, int $idRecipe, int $markValue, MarkRepository $markRepo, UserRepository $userRepo, RecipeRepository $recipeRepo, EntityManagerInterface $manager) {
        $user = $this->getUser();

        /**
         * Check if user is connected
         */
        if (!$user) return $this->json([
            'code'      => 403,
            'message'   => 'Unauthorized'
        ], 403);

        $user = $userRepo->findOneBy(["id" => $idUser]);
        $recipe = $recipeRepo->findOneBy(["id" => $idRecipe]);

        $mark = $markRepo->findOneBy([
            'user' => $user,
            'recipe' => $recipe
        ]);

        $alreadyRated = empty($mark) ? false : true;
        if($alreadyRated) {
            $mark->setMark($markValue);
        }else {
            $mark = new Mark();
            $mark->setUser($user);
            $mark->setRecipe($recipe);
            $mark->setMark($markValue);
        }

        $manager->persist($mark);
        $manager->flush();

        return $this->json([
            'message'        => 'Success'
        ], 200);
    }
}
