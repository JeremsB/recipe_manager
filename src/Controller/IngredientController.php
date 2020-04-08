<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    /**
     * Read ingredients of curret user and set template
     * 
     * @Route("/ingredients", name="ingredients")
     *
     * @param IngredientRepository $ingredientRepo
     * @return void
     */
    public function index(IngredientRepository $ingredientRepo)
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
        $ingredientsResponse = $ingredientRepo->findBy([
            'user' => (int) $userId
        ]);

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

        return $this->render('ingredient/ingredients.html.twig', [
            'ingredients' => $ingredients
        ]);
    }
}
