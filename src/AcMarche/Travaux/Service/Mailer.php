<?php

namespace AcMarche\Travaux\Service;

use AcMarche\Travaux\Entity\Intervention;
use AcMarche\Travaux\Event\InterventionEvent;
use AcMarche\Travaux\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Mailer
{
    private $authorizationChecker;
    private $twig;
    private $token;
    private $userRepository;
    private $flashBag;
    private $travauxUtils;

    public function __construct(
        \Swift_Mailer $mailer,
        AuthorizationCheckerInterface $authorizationChecker,
        EngineInterface $twig,
        TokenStorageInterface $tokenStorage,
        UserRepository $userRepository,
        FlashBagInterface $flashBag,
        TravauxUtils $travauxUtils
    ) {
        $this->mailer = $mailer;
        $this->authorizationChecker = $authorizationChecker;
        $this->twig = $twig;
        $this->token = $tokenStorage;
        $this->userRepository = $userRepository;
        $this->flashBag = $flashBag;
        $this->travauxUtils = $travauxUtils;
    }

    /**
     * @param $from
     * @param $destinataires
     * @param $sujet
     * @param $body
     */
    public function send($from, $destinataires, $sujet, $body)
    {
        $mail = (new \Swift_Message($sujet))
            ->setSubject($sujet)
            ->setFrom($from)
            ->setTo($destinataires);
        $mail->setBody($body);

        $this->mailer->send($mail);
    }

    /**
     * Nouvelle intervention par un admin
     * Je previens par mail, tous les rédacteurs, les admins
     * @param InterventionEvent $event
     * @param $resultat
     */
    public function sendNewIntervention(InterventionEvent $event)
    {
        $intervention = $event->getIntervention();
        $admins = $this->travauxUtils->getEmailsByGroup("TRAVAUX_ADMIN");
        $redacteurs = $this->travauxUtils->getEmailsByGroup("TRAVAUX_REDACTEUR");

        $token = $this->token->getToken();
        $currentUser = $token->getUser();

        $from = $currentUser->getEmail();
        $sujet = $intervention->getIntitule()." a été ajoutée";
        $destinataires = array_unique(array_merge($admins, $redacteurs));

        $body = $this->twig->render(
            'mail/intervention.txt.twig',
            array(
                'intervention' => $intervention,
            )
        );


        $this->send($from, $destinataires, $sujet, $body);
        $this->flashBag->add("success", "Un mail a été envoyé à ".implode(",", $destinataires));
    }

    /**
     * Une nouvelle intervention est ajouté par contributeur, un auteur ou un redacteur
     * Contributeur => auteurs
     * Auteur => Admins
     * Redacteur => Admins
     *
     * @param InterventionEvent $event
     */
    public function sendAskValidation(InterventionEvent $event)
    {
        $intervention = $event->getIntervention();
        $destinataires = [];

        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_CONTRIBUTEUR')) {
            $destinataires = $this->travauxUtils->getEmailsByGroup("TRAVAUX_AUTEUR");
        }

        if (($this->authorizationChecker->isGranted('ROLE_TRAVAUX_AUTEUR')) || ($this->authorizationChecker->isGranted(
                'ROLE_TRAVAUX_REDACTEUR'
            ))) {
            $destinataires = $this->travauxUtils->getEmailsByGroup("TRAVAUX_ADMIN");
        }

        $token = $this->token->getToken();
        $currentUser = $token->getUser();

        $from = $currentUser->getEmail();

        $sujet = 'Une demande d\'intervention à valider';

        $body = $this->twig->render(
            'mail/new_validation.txt.twig',
            array(
                'intervention' => $intervention,
            )
        );


        $this->send($from, $destinataires, $sujet, $body);
        $this->flashBag->add("success", "Un mail a été envoyé à ".implode(",", $destinataires));
    }

    /**
     * Si auteur accepte la demande on envoie une demande aux admins
     * @param InterventionEvent $event
     */
    public function sendAskValidationForAdmin(InterventionEvent $event)
    {
        $intervention = $event->getIntervention();
        $destinataires = $this->travauxUtils->getEmailsByGroup("TRAVAUX_ADMIN");

        $token = $this->token->getToken();
        $currentUser = $token->getUser();

        $from = $currentUser->getEmail();

        $sujet = 'Une demande d\'intervention à valider';

        $body = $this->twig->render(
            'mail/new_validation.txt.twig',
            array(
                'intervention' => $intervention,
            )
        );


        $this->send($from, $destinataires, $sujet, $body);
        $this->flashBag->add(
            "success",
            "Une demande de validation a été envoyée à ".implode(",", $destinataires)
        );
    }

    /**
     * Envoie un mail lors du refus ou de l'acception d'une intervention
     * @param InterventionEvent $event
     * @param string $resultat
     * @return void
     */
    public function sendMailAcceptOrReject(InterventionEvent $event, $resultat)
    {
        $destinataires = [];
        $intervention = $event->getIntervention();

        if ($resultat == 'refusée') {
            $destinataires = $this->getDestinatairesReject($intervention);
        } elseif ($resultat == 'acceptée') {
            $destinataires = $this->getDestinatairesAccept($intervention);
        }

        $message = $event->getMessage();
        $token = $this->token->getToken();
        $currentUser = $token->getUser();

        $from = $currentUser->getEmail();
        $sujet = $intervention->getIntitule()." a été ".$resultat;

        $body = $this->twig->render(
            'mail/result_validation.txt.twig',
            array(
                'result' => $resultat,
                'intervention' => $intervention,
                'message' => $message,
            )
        );

        $this->send($from, $destinataires, $sujet, $body);
        $this->flashBag->add("success", "Un mail a été envoyé à ".implode(",", $destinataires));
    }



    /**
     * Admin demande plus d'info à un contributeur
     * On envoie un mail a tous les auteurs
     * et au contributeur
     * @param InterventionEvent $event
     */
    public function sendMailPlusInfoContributeur(InterventionEvent $event)
    {
        $intervention = $event->getIntervention();
        $message = $event->getMessage();

        $destinataires = $this->travauxUtils->getEmailsByGroup("TRAVAUX_AUTEUR");
        $userAdd = $this->userRepository->findOneBy(['username' => $intervention->getUserAdd()]);
        if ($userAdd) {
            $destinataires[] = $userAdd->getEmail();
        }

        $token = $this->token->getToken();
        $currentUser = $token->getUser();

        $from = $currentUser->getEmail();

        $sujet = "Besoin d'informations pour une intervention";
        $body = $this->twig->render(
            'mail/plus_info_contributeur.txt.twig',
            array(
                'intervention' => $intervention,
                'message' => $message,
            )
        );


        $this->send($from, $destinataires, $sujet, $body);
        $this->flashBag->add("success", "Un mail a été envoyé à ".implode(",", $destinataires));
    }


    /**
     * Admin demande plus d'info à un auteur
     * On envoie un mail a tous les auteurs
     * @param InterventionEvent $event
     */
    public function sendMailPlusInfoAuteur(InterventionEvent $event)
    {
        $intervention = $event->getIntervention();
        $message = $event->getMessage();

        $destinataires = $this->travauxUtils->getEmailsByGroup("TRAVAUX_AUTEUR");

        $token = $this->token->getToken();
        $currentUser = $token->getUser();

        $from = $currentUser->getEmail();

        $sujet = "Besoin d'informations pour une intervention";
        $body = $this->twig->render(
            'mail/plus_info_auteur.txt.twig',
            array(
                'intervention' => $intervention,
                'message' => $message,
            )
        );

        $this->send($from, $destinataires, $sujet, $body);
        $this->flashBag->add("success", "Un mail a été envoyé à ".implode(",", $destinataires));
    }

    /**
     * L'admin a besoin d'info
     * On envoie a celui qui a fait la demande
     *
     * @param InterventionEvent $event
     */
    public function sendMailPlusInfoRedacteur(InterventionEvent $event)
    {
        $intervention = $event->getIntervention();
        $message = $event->getMessage();

        $destinataires = [];
        $userAdd = $this->userRepository->findOneBy(['username' => $intervention->getUserAdd()]);
        if ($userAdd) {
            $destinataires[] = $userAdd->getEmail();
        }

        $token = $this->token->getToken();
        $currentUser = $token->getUser();

        $from = $currentUser->getEmail();

        $sujet = "Besoin d'informations pour une intervention";
        $body = $this->twig->render(
            'mail/plus_info_redacteur.txt.twig',
            array(
                'intervention' => $intervention,
                'message' => $message,
            )
        );


        $this->send($from, $destinataires, $sujet, $body);
        $this->flashBag->add("success", "Un mail a été envoyé à ".implode(",", $destinataires));
    }

    /**
     * Lorsqu'un admin archive une intervention
     * J'envoie un mail aux admins et au rédacteurs
     * @param InterventionEvent $event
     */
    public function sendMailArchive(InterventionEvent $event)
    {
        $intervention = $event->getIntervention();
        $redacteurs = $this->travauxUtils->getEmailsByGroup("TRAVAUX_REDACTEUR");
        $admins = $this->travauxUtils->getEmailsByGroup("TRAVAUX_ADMIN");

        $destinataires = array_unique(array_merge($admins, $redacteurs));

        $token = $this->token->getToken();
        $currentUser = $token->getUser();

        $from = $currentUser->getEmail();

        $sujet = 'Archivage de : '.$intervention->getIntitule();

        $body = $this->twig->render(
            'mail/archive.txt.twig',
            array(
                'intervention' => $intervention,
            )
        );


        $this->send($from, $destinataires, $sujet, $body);
        $this->flashBag->add("success", "Un mail a été envoyé à ".implode(",", $destinataires));
    }

    /**
     * @param InterventionEvent $event
     */
    public function sendMailSuivi(InterventionEvent $event)
    {
        $intervention = $event->getIntervention();
        $suivi = $event->getSuivi();

        $redacteurs = $this->travauxUtils->getEmailsByGroup("TRAVAUX_REDACTEUR");
        $admins = $this->travauxUtils->getEmailsByGroup("TRAVAUX_ADMIN");

        $destinataires = array_unique(array_merge($admins, $redacteurs));

        $token = $this->token->getToken();
        $currentUser = $token->getUser();

        $from = $currentUser->getEmail();

        $sujet = 'Ajout d\'un suivi pour : '.$intervention->getIntitule();

        $body = $this->twig->render(
            'mail/suivis.txt.twig',
            array(
                'intervention' => $intervention,
                'suivi' => $suivi,
            )
        );


        $this->send($from, $destinataires, $sujet, $body);
        $this->flashBag->add("success", "Un mail a été envoyé à ".implode(",", $destinataires));
    }

    /**
     * Si accepter par un admin
     * Je previens le groupe admin et redacteur ainsi que celui qui a a joute
     *      Si ajoute par contributeur
     *          Je previens par mail, en plus les auteurs
     *      Si auteur
     *          Je previens par mail, en plus ?
     *      Si redacteur
     *          Je previens par mail, en plus ?
     * Si accepter par auteur
     * Je previens par mail celui qui a ajoute et les auteurs
     *
     * @param Intervention $intervention
     * @return  array
     */
    private function getDestinatairesAccept(Intervention $intervention)
    {
        $userAdd = $this->userRepository->findOneBy(['username' => $intervention->getUserAdd()]);
        $admins = $redacteurs = $auteurs = [];

        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_ADMIN')) {
            $admins = $this->travauxUtils->getEmailsByGroup("TRAVAUX_ADMIN");
            $redacteurs = $this->travauxUtils->getEmailsByGroup("TRAVAUX_REDACTEUR");
            if ($userAdd) {
                $role = $this->travauxUtils->getRoleByEmail($userAdd->getEmail());
                if ($role == 'contributeur') {
                    $auteurs = $this->travauxUtils->getEmailsByGroup("TRAVAUX_AUTEUR");
                }
            }
        }

        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_AUTEUR')) {
            $auteurs = $this->travauxUtils->getEmailsByGroup("TRAVAUX_AUTEUR");
        }

        $destinataires = array_unique(array_merge($admins, $auteurs, $redacteurs));

        if ($userAdd) {
            $destinataires[] = $userAdd->getEmail();
        }

        return $destinataires;
    }

    /**
     * Si refuser par un ADMIN
     *      Si ajoute par contributeur
     *      Je previens par mail les admins, auteurs et celui qui a ajouté
     *      Si ajoute par un redacteur
     *      Je previens par mail les auteurs et celui qui a ajouté
     * Si refuser par un AUTEUR
     *      Je previens par mail les auteurs et celui qui a ajouté
     *
     * @param Intervention $intervention
     * @return  array
     */
    private function getDestinatairesReject(Intervention $intervention)
    {
        $userAdd = $this->userRepository->findOneBy(['username' => $intervention->getUserAdd()]);
        $admins = $redacteurs = [];

        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_ADMIN')) {
            $admins = $this->travauxUtils->getEmailsByGroup("TRAVAUX_ADMIN");

            if ($userAdd) {
                $role = $this->travauxUtils->getRoleByEmail($userAdd->getEmail());
                if ($role != 'contributeur') {
                    $redacteurs = $this->travauxUtils->getEmailsByGroup("TRAVAUX_REDACTEUR");
                }
            }
        }

        $auteurs = $this->travauxUtils->getEmailsByGroup("TRAVAUX_AUTEUR");

        $destinataires = array_unique(array_merge($admins, $auteurs, $redacteurs));

        if ($userAdd) {
            $destinataires[] = $userAdd->getEmail();
        }

        return $destinataires;
    }
}
