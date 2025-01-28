<?php
namespace mail;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class mailEnvoi implements mailEnvoiInterface
{

    public function envoi($dns,$from, $to, $subject, $content): void
    {
        $transport = Transport::fromDsn($dns);
        $mailer = new Mailer($transport);
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->html($content);

        $mailer->send($email);
    }
}