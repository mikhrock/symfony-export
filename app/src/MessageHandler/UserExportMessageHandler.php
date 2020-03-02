<?php

namespace App\MessageHandler;

use App\CustomBundles\CsvExportBundle\CsvExportBundle;
use App\Entity\User;
use App\Message\UserExportMessage;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Twig\Environment;

class UserExportMessageHandler implements MessageHandlerInterface
{
    /**
     * @var CsvExportBundle
     */
    private $csvExportBundle;

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @param UserRepository $repository
     * @param CsvExportBundle $csvExportBundle
     * @param \Swift_Mailer $mailer
     * @param Environment $twig
     */
    public function __construct(UserRepository $repository, CsvExportBundle $csvExportBundle, \Swift_Mailer $mailer, Environment $twig)
    {
        $this->repository = $repository;
        $this->csvExportBundle = $csvExportBundle;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param UserExportMessage $userExportMessage
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __invoke(UserExportMessage $userExportMessage)
    {
        $queryBuilder = $this->repository->createQueryBuilder('u');

        $filename = 'users.csv';

        $file = $this->csvExportBundle->getFileFromQueryBuilder(
            $queryBuilder,
            User::class
        );

        $message = (new \Swift_Message('User Export'))
            ->setFrom('info@symfony-export.loc')
            ->setTo($userExportMessage->getEmail())
            ->setBody(
                $this->twig->render(
                    'emails/user-export.html.twig'
                ),
                'text/html'
            )

            ->addPart(
                $this->twig->render(
                    'emails/user-export.txt.twig'
                ),
                'text/plain'
            )

            ->attach(\Swift_Attachment::fromPath($file)->setFilename($filename))
        ;

        $this->mailer->send($message);

        unlink($file);
    }
}