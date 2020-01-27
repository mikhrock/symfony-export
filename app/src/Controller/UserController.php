<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/")
     * @IsGranted("ROLE_USER")
     * @param UserRepository $repository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(UserRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        $users = $repository->findAll();

        $pagination = $paginator->paginate(
            $repository->createQueryBuilder('u')->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('user/users-list.html.twig', [
            'users' => $users,
            'pagination' => $pagination,
        ]);
    }
}