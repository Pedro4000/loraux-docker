<?php

namespace App\Service ;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailToNewMember
{
    public function sendMailToNewMember(MailerInterface $mailer) {

        $email = (new Email())
            ->from('p.brickley@hotmail.fr')
            ->to('p.brickley@hotmail.fr')
            ->subject('Test Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

    }
}