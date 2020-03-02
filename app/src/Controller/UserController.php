<?php

namespace App\Controller;

use App\CustomBundles\CsvExportBundle\CsvExportBundle;
use App\Message\UserExportMessage;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var CsvExportBundle
     */
    private $csvExportBundle;

    /**
     * @param RequestStack $requestStack
     * @param CsvExportBundle $csvExportBundle
     */
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

    /**
     * @Route("/export")
     * @IsGranted("ROLE_ADMIN")
     * @param MessageBusInterface $messageBus
     * @return Response
     */
    public function exportAction(MessageBusInterface $messageBus)
    {
        $userExportMessage = new UserExportMessage($this->getUser()->getEmail());
        $messageBus->dispatch($userExportMessage);

        return $this->render('user/users-export-message.html.twig');
    }
}