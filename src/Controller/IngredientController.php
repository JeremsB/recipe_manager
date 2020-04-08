<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    /**
     * @Route("/ingredients", name="ingredients")
     */
    public function index()
    {
        return $this->render('ingredient/ingredients.html.twig');
    }
}
