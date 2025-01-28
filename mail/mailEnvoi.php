<?php

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class mailEnvoi implements mailEnvoiInterface
{

    public function envoi($dns,$from, $to, $subject, $content): void
    {
        $transport = Transport::fromDsn();
        $mailer = new Mailer($transport);
        $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            ->subject('sujet')
            ->html('<p>ça marche</p>');

        $mailer->send($email);
    }
}