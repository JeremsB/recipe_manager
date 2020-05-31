<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Entity\Mark;
use App\Entity\RecipeImage;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    private $userRepo;

    public function __construct(UserPasswordEncoderInterface $encoder, UserRepository $userRepo) {
        $this->encoder = $encoder;
        $this->userRepo = $userRepo;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        // Mise en place des utilisateurs
        $users = [];

        // Création de l'utilisateur de test
        $user = new User();

        $user->setPseudo("Guillaume Balas");
        $user->setEmail("guillaume.balas@persistance.com");

        $hash = $this->encoder->encodePassword($user, 'password');
        $user->setHash($hash);

        $manager->persist($user);
        $users[] = $user; 

        // Création des autres utilisateurs
        for ($i=0; $i < 2; $i++) { 
            $user = new User();

            $user->setPseudo($faker->firstname);
            $user->setEmail($faker->email);

            $hash = $this->encoder->encodePassword($user, 'password');
            $user->setHash($hash);

            $manager->persist($user);
            $users[] = $user; 
        }
        // Mise en place de 5 ingrédients pour chaque utilisateur
        $nameIngredients = [
            'Chocolat', 'Oeuf', 'Poulet', 'Menthe', 'Lait', 'Laitue'
        ];
        $ingredients = [];

        foreach ($users as $user) {
            foreach ($nameIngredients as $nameIngredient) {
                $ingredient = new Ingredient();

                $ingredient->setUser($user);
                $ingredient->setName($nameIngredient);
                $ingredient->setPrice(mt_rand(3,27));

                $manager->persist($ingredient);
                $ingredients[] = $ingredient;
            }
        }

        // Mise en place de 3 recettes par utilisateur
        $nameRecipes = [
            0 => [
                'Poulet au chocolat', 'Oeuf et menthe', 'Un peu de laitue et de lait'
            ],
            1 => [
                'Poulet au lait', 'Oeuf au chocolat', 'Laitue et sa menthe'
            ],
            2 => [
                'Poulet et sa laitue', 'Du lait et des oeufs', 'Chocolat a la menthe'
                ]
        ];
        $recipes = [];

        foreach ($users as $userIndex => $user) {            
            foreach ($nameRecipes as $nameRecipe) {
                $recipe = new Recipe();

                $recipe->setUser($user);
                $recipe->setName($nameRecipe[$userIndex]);
                $recipe->setInstructions($faker->text(50));
                $recipe->setTime(mt_rand(5, 80));
                $recipe->setDifficulty(mt_rand(1, 5));
                $recipe->setPrice(mt_rand(70, 160) / 10);

                $rand = mt_rand(0, 1);
                $recipe->setShared($rand == 0 ? true : false);

                $manager->persist($recipe);
                $recipes[] = $recipe;
                $userRecipes[] = $recipe;

                // Nous gérons les images liées à la recette
                $recipeImages = [];
                $rand = mt_rand(0, 5);

                for ($i=0; $i < $rand; $i++) { 
                    $recipeImage = new RecipeImage();

                    $recipeImage->setUrl("https://i.picsum.photos/id/581/300/300.jpg");
                    $manager->persist($recipeImage); 
                    $recipeImages[] = $recipeImage;

                    $recipe->addImage($recipeImage);
                }
            }
        }

        // Mise en place des ingrédients pour chaque recette
        foreach ($recipes as $recipe) {
            foreach ($ingredients as $ingredient) {
                if($recipe->getUser() === $ingredient->getUser()) {
                    $rand = mt_rand(0, 1);
                    $rand == 0 ? $recipe->addIngredient($ingredient) : '';
                }
            }
        }

        // Mise en place des ingrédients pour chaque recette
        foreach ($recipes as $recipe) {
            if($recipe->getShared()) {
                $user = $users[array_rand($users)];
                
                if($user !== $recipe->getUser()) {
                    $mark = new Mark();

                    $mark->setMark(mt_rand(0, 10));
                    $mark->setUser($user);
                    $mark->setRecipe($recipe);

                    $manager->persist($mark);
                }
            }
        }
        
        $manager->flush();
    }
}
