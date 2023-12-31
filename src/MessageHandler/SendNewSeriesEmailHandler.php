<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\SeriesWasCreated;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendNewSeriesEmailHandler
{

    public function __construct(
        private readonly UserRepository  $userRepository,
        private readonly MailerInterface $mailer
    ) {
    }


    /**
     * @param SeriesWasCreated $message
     * @return void
     * @throws TransportExceptionInterface
     */
    public function __invoke(SeriesWasCreated $message): void
    {
        $users = $this->userRepository->findAll();
        $usersEmails = array_map(fn (User $user) => $user->getEmail(), $users);
        $series = $message->series;

        $email = (new TemplatedEmail())
            ->from('sistema@example.com')
            ->to(...$usersEmails)
            ->subject('Nova serie criada!')
            ->text("Serie {$series->getName()} foi criada")
            ->htmlTemplate("emails/series_created.html.twig")
            ->context(compact('series'));

        $this->mailer->send($email);
    }
}