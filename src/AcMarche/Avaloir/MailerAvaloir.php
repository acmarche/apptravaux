<?php


namespace AcMarche\Avaloir;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailerAvaloir
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendError(string $sujet, array $result)
    {
        $mail = (new TemplatedEmail())
            ->subject('[Avaloir] '.$sujet)
            ->from("webmaster@marche.be")
            ->to("webmaster@marche.be")
            ->textTemplate("@AcMarcheAvaloir/mail/reverse.txt.twig")
            ->context(['result' => $result]);

        try {
            $this->mailer->send($mail);
        } catch (TransportExceptionInterface $e) {
        }
    }
}
