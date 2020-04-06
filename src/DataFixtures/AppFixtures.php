<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
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

        $manager->flush();
    }
}
