<?php

namespace App\MessageHandler;

use App\Messages\AsyncTemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: AsyncTemplatedEmail::class)]
class AsyncTemplatedMailHandler
{
    public function __construct(private MailerInterface $mailer)
    {

    }

    public function __invoke(AsyncTemplatedEmail $email)
    {
        $this->mailer->send($email);
    }
}
