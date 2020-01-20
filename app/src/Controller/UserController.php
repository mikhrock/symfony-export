<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        $number = random_int(0, 100);

        return $this->render('user/users-list.html.twig', [
            'number' => $number,
        ]);
    }
}