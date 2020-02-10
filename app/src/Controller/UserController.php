<?php

namespace App\Controller;

use App\CustomBundles\CsvExportBundle\CsvExportBundle;
use App\Entity\User;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $requestStack;

    private $csvExportBundle;

    public function __construct(RequestStack $requestStack, CsvExportBundle $csvExportBundle)
    {
        $this->requestStack = $requestStack;
        $this->csvExportBundle = $csvExportBundle;
    }

    /**
     * @Route("/")
     * @IsGranted("ROLE_USER")
     * @param UserRepository $repository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(UserRepository $repository, PaginatorInterface $paginator)
    {
        $users = $repository->findAll();

        $pagination = $paginator->paginate(
            $repository->createQueryBuilder('u')->getQuery(),
            $this->requestStack->getCurrentRequest()->query->getInt('page', 1),
            10
        );

        return $this->render('user/users-list.html.twig', [
            'users' => $users,
            'pagination' => $pagination,
        ]);
    }

    public function exportAction(UserRepository $repository)
    {
        $sortDirection = $this->requestStack->getCurrentRequest()->query->get('sortDirection');
        if (empty($sortDirection) || !in_array(strtoupper($sortDirection), ['ASC', 'DESC'])) {
            $sortDirection = 'DESC';
        }

        $queryBuilder = $repository->createQueryBuilder('u');

        return $this->csvExportBundle->getResponseFromQueryBuilder(
            $queryBuilder,
            User::class,
            'users.csv'
        );
    }
}