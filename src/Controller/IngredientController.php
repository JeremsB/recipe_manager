<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * Delete an ingredient
     * 
     * @Route("/ingredient/delete/{id}", name="delete_ingredient")
     *
     * @param [int] $id
     * @param EntityManagerInterface $manager
     * @param IngredientRepository $ingredientRepo
     * @return Response
     */
    public function delete($id, EntityManagerInterface $manager, IngredientRepository $ingredientRepo) : Response {
        $user = $this->getUser();

        /**
         * Check if user is connected
         */
        if (!$user) return $this->json([
            'code'      => 403,
            'message'   => 'Unauthorized'
        ], 403);

        $ingredient = $ingredientRepo->findOneBy(['id' => $id]);

        $manager->remove($ingredient);
        $manager->flush();

        return $this->json([
            'message'        => 'Success'
        ], 200);
    }
}
