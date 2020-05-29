<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer le formulaire de connexion
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se déconnecter
     * 
     * @Route("/logout", name="account_logout")
     * 
     * @return void
     */
    public function logout() {
        //Nothing bro..
    }

    /**
     * Permet d'afficher le formulaire d'inscription
     * 
     * @Route("/register", name="account_register")
     * 
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest(($request));

        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été créé !"
            );

            return $this->redirectToRoute("account_login");
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Affiche le profil de l'utilisateur
     * 
     * @Route("/account/profile", name="account_profile")
     *
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager) {
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données du profil ont bien été sauvegardées !"
            );
        }

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Affiche le formulaire de modification de mot de passe
     * 
     * @Route("/account/password", name="account_password")
     *
     * @return Response
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                $form->get('oldPassword')->addError(new FormError("Le mot de passe saisie est différent de votre mot de passe actuel !"));
            }
            else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();
            }

            return $this->redirectToRoute('home');
        }

        return $this->render('account/password.html.twig', [
            'diary' => false,
            'form' => $form->createView()
        ]);
    }
}
