<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Entity\RecipeImage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        // Nous gérons les users
        $users = [];

        for ($i=0; $i < 10; $i++) { 
            $user = new User();

            $user->setPseudo($faker->firstname);
            $user->setEmail($faker->email);

            $hash = $this->encoder->encodePassword($user, 'password');
            $user->setHash($hash);

            $manager->persist($user);
            $users[] = $user; 
        }

        // Nous gérons les ingrédients
        $ingredients = [];

        for ($i=0; $i < 25; $i++) { 
            $ingredient = new Ingredient();

            $randIndex = array_rand($users);
            $ingredient->setUser($users[$randIndex]);

            $ingredient->setName("Name${i}");
            $ingredient->setPrice(mt_rand(3,27));

            $manager->persist($ingredient);
            $ingredients[] = $ingredient;
        }

        // Nous gérons les recettes
        $recipes = [];

        for ($i=0; $i < 5; $i++) { 
            $recipe = new Recipe();

            $randIndex = array_rand($users);
            $recipe->setUser($users[$randIndex]);

            $recipe->setName("Recipe${i}");
            $recipe->setInstructions($faker->text(50));
            $recipe->setTime(mt_rand(5, 100));
            $recipe->setDifficulty(mt_rand(1, 5));
            $recipe->setPrice(mt_rand(70, 160) / 10);

            $rand = mt_rand(0, 1);
            $recipe->setShared($rand == 0 ? true : false);

            $manager->persist($recipe);
            $recipes[] = $recipe;

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

        // Nous gérons les ingrédients composants les recettes
        foreach ($recipes as $recipe) {
            foreach ($ingredients as $ingredient) {
                $rand = mt_rand(0, 1);
                $rand == 0 ? $recipe->addIngredient($ingredient) : '';
            }
        }
        
        $manager->flush();
    }
}
