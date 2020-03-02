<?php

namespace App\Controller;

use App\CustomBundles\CsvExportBundle\CsvExportBundle;
use App\Entity\User;
use App\Message\UserExportMessage;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

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
        $pagination = $paginator->paginate(
            $repository->createQueryBuilder('u')->getQuery(),
            $this->requestStack->getCurrentRequest()->query->getInt('page', 1),
            10
        );

        return $this->render('user/users-list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    public function exportAction(UserRepository $repository, MessageBusInterface $messageBus)
    {

        $userExportMessage = new UserExportMessage($this->getUser()->getEmail());
        $messageBus->dispatch($userExportMessage);

        /*$sortDirection = $this->requestStack->getCurrentRequest()->query->get('sortDirection');
        if (empty($sortDirection) || !in_array(strtoupper($sortDirection), ['ASC', 'DESC'])) {
            $sortDirection = 'DESC';
        }*/

        return $this->render('user/users-export-message.html.twig');
    }
}