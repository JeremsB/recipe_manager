<?php

namespace App\Controller;

use App\Entity\Ingredient;
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
            'ingredients' => array_reverse($ingredients)
        ]);
    }

    /**
     * Create an ingredient
     * 
     * @Route("/ingredient/create/{name}/{price}", name="create_ingredient")
     *
     * @param [string] $name
     * @param [string] $price
     * @param EntityManagerInterface $manager
     * @param IngredientRepository $ingredientRepo
     * @return Response
     */
    public function create($name, $price, EntityManagerInterface $manager) : Response {
        $user = $this->getUser();

        /**
         * Check if user is connected
         */
        if (!$user) return $this->json([
            'code'      => 403,
            'message'   => 'Unauthorized'
        ], 403);

        $ingredient = new Ingredient();

        $ingredient->setUser($user);
        $ingredient->setName(IngredientController::decode($name));
        $ingredient->setPrice((float) IngredientController::decode($price));

        $manager->persist($ingredient);
        $manager->flush();

        return $this->json([
            'message'   => 'Success',
            'id'        => $ingredient->getId(),
            'name'      => $ingredient->getName(),
            'price'     => $ingredient->getPrice()
        ], 200);
    }

    /**
     * Update an ingredient
     * 
     * @Route("/ingredient/update/{id}/{name}/{price}", name="update_ingredient")
     *
     * @param [id] $id
     * @param [string] $name
     * @param [string] $price
     * @param EntityManagerInterface $manager
     * @param IngredientRepository $ingredientRepo
     * @return Response
     */
    public function update($id, $name, $price, EntityManagerInterface $manager, IngredientRepository $ingredientRepo) : Response {
        $user = $this->getUser();

        /**
         * Check if user is connected
         */
        if (!$user) return $this->json([
            'code'      => 403,
            'message'   => 'Unauthorized'
        ], 403);

        $ingredient = $ingredientRepo->findOneBy(['id' => $id]);

        $ingredient->setUser($user);
        $ingredient->setName(IngredientController::decode($name));
        $ingredient->setPrice((float) IngredientController::decode($price));

        $manager->persist($ingredient);
        $manager->flush();

        return $this->json([
            'message'   => 'Success',
            'id'        => $ingredient->getId(),
            'name'      => $ingredient->getName(),
            'price'     => $ingredient->getPrice()
        ], 200);
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

    public static function decode($str) {
        return base64_decode(urldecode($str));
    }
}
