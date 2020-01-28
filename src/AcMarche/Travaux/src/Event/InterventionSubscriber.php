<?php

namespace AcMarche\Travaux\Event;

use AcMarche\Travaux\Repository\InterventionRepository;
use AcMarche\Travaux\Repository\UserRepository;
use AcMarche\Travaux\Service\Mailer;
use AcMarche\Travaux\Service\SuiviService;
use AcMarche\Travaux\Service\TravauxUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 8/12/16
 * Time: 10:39
 */
class InterventionSubscriber implements EventSubscriberInterface
{
    /**
     * @var InterventionRepository
     */
    private $interventionRepository;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var TravauxUtils
     */
    private $travauxUtils;
    /**
     * @var SuiviService
     */
    private $suiviService;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        Mailer $mailer,
        FlashBagInterface $flashBag,
        TravauxUtils $travauxUtils,
        SuiviService $suiviService,
        InterventionRepository $interventionRepository,
        UserRepository $userRepository
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->mailer = $mailer;
        $this->flashBag = $flashBag;
        $this->travauxUtils = $travauxUtils;
        $this->suiviService = $suiviService;
        $this->interventionRepository = $interventionRepository;
        $this->userRepository = $userRepository;
    }

    public static function getSubscribedEvents()
    {
        //Liste des évènements écoutés et méthodes à appeler
        return array(
            InterventionEvent::INTERVENTION_NEW => 'interventionNew',
            InterventionEvent::INTERVENTION_ACCEPT => 'interventionAccept',
            InterventionEvent::INTERVENTION_REJECT => 'interventionReject',
            InterventionEvent::INTERVENTION_INFO => 'interventionInfo',
            InterventionEvent::INTERVENTION_REPORTER => 'interventionReporter',
            InterventionEvent::INTERVENTION_ARCHIVE => 'interventionArchive',
            InterventionEvent::INTERVENTION_SUIVI_NEW => 'interventionSuivi',
        );
    }

    public function interventionNew(InterventionEvent $event)
    {
        /**
         * ADD BY ADMIN
         * Je previens par mail, tous les rédacteurs, les admins
         */
        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_ADMIN')) {
            try {
                $this->mailer->sendNewIntervention($event);
                $this->flashBag->add("success", "Un mail a été envoyé aux administrateurs ");
            } catch (TransportExceptionInterface $e) {
                $this->flashBag->add('danger', 'Le mail n\'a pas pu être envoyé: ' . $e->getMessage());
            }
        }

        /**
         * ADD BY REDACTEUR
         * Je previens par mail les admins pour validation
         */
        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_REDACTEUR')) {
            try {
                $this->mailer->sendAskValidation($event);
                $this->flashBag->add("success", "Un mail a été envoyé aux administrateurs");
            } catch (TransportExceptionInterface $e) {
                $this->flashBag->add('danger', 'Le mail n\'a pas pu être envoyé: ' . $e->getMessage());
            }
        }

        /**
         * ADD BY AUTEUR
         * Je previens par mail les admins pour validation
         */
        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_AUTEUR')) {
            try {
                $this->mailer->sendAskValidation($event);
                $this->flashBag->add("success", "Un mail a été envoyé aux administrateurs");
            } catch (TransportExceptionInterface $e) {
                $this->flashBag->add('danger', 'Le mail n\'a pas pu être envoyé: ' . $e->getMessage());
            }
        }

        /**
         * ADD BY CONTRIBUTEUR
         * Je previens par mail les auteurs pour validation
         */
        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_CONTRIBUTEUR')) {
            try {
                $this->mailer->sendAskValidation($event);
                $this->flashBag->add("success", "Un mail a été envoyé aux administrateurs");
            } catch (TransportExceptionInterface $e) {
                $this->flashBag->add('danger', 'Le mail n\'a pas pu être envoyé: ' . $e->getMessage());
            }
        }
    }

    /**
     * L'intervention est acceptée
     * @param InterventionEvent $event
     * @throws \Exception
     *
     */
    public function interventionAccept(InterventionEvent $event)
    {
        $intervention = $event->getIntervention();
        $message = $event->getMessage();
        /**
         * ACCEPT BY ADMIN
         * Je previens par mail, tous les auteurs, les rédacteurs, les admins et celui qui a ajouté
         */
        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_ADMIN')) {
            /**
             * j'ajoute la date de validation
             */
            $intervention->setDateValidation(new \DateTime());
            $this->interventionRepository->flush();
        }

        $this->suiviService->newSuivi($intervention, $message);
        $this->flashBag->add("success", "L'intervention a bien été acceptée");

        try {
            $this->mailer->sendMailAcceptOrReject($event, "acceptée");
            //$this->flashBag->add("success", "Une demande de validation a été envoyée.");
        } catch (TransportExceptionInterface $e) {
            $this->flashBag->add('danger', 'Le mail n\'a pas pu être envoyé: ' . $e->getMessage());
        }

        /**
         * ACCEPT BY AUTEUR
         * Je demande une validation à admin
         */
        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_AUTEUR')) {
            try {
                $this->mailer->sendAskValidationForAdmin($event);
                $this->flashBag->add("success", "Une demande de validation a été envoyée.");
            } catch (TransportExceptionInterface $e) {
                $this->flashBag->add('danger', 'Le mail n\'a pas pu être envoyé: ' . $e->getMessage());
            }
        }
    }

    /**
     * L'auteur ou l'admin a refuse l'intervention
     * Celle ci est supprimée
     * Et on previent par mail
     * @param InterventionEvent $event
     */
    public function interventionReject(InterventionEvent $event)
    {
        $this->interventionRepository->remove($event->getIntervention());
        $this->flashBag->add("success", "L'intervention a bien été refusée");
        try {
            $this->mailer->sendMailAcceptOrReject($event, "refusée");
        } catch (TransportExceptionInterface $e) {
            $this->flashBag->add('danger', 'Le mail n\'a pas pu être envoyé: ' . $e->getMessage());
        }
    }

    public function interventionReporter(InterventionEvent $event)
    {
        $intervention = $event->getIntervention();
        $dateExecution = $event->getDateExecution();
        $message = $event->getMessage();
        /**
         * ACCEPT BY ADMIN
         * Je previens par mail, tous les auteurs, les rédacteurs, les admins et celui qui a ajouté
         */
        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_ADMIN')) {
            /**
             * j'ajoute la date de validation
             */
            $intervention->setDateValidation(new \DateTime());
            $intervention->setDateExecution($dateExecution);
            $this->interventionRepository->flush();
        }

        $this->suiviService->newSuivi($intervention, $message);
        $this->flashBag->add("success", "L'intervention a été reportée");

        try {
            $this->mailer->sendMailAcceptOrReject($event, "reportée");
        } catch (TransportExceptionInterface $e) {
            $this->flashBag->add('danger', 'Le mail n\'a pas pu être envoyé: ' . $e->getMessage());
        }

        /**
         * ACCEPT BY AUTEUR
         * Je demande une validation à admin
         */
        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_AUTEUR')) {
            try {
                $this->mailer->sendAskValidationForAdmin($event);
                $this->flashBag->add("success", "Une demande de validation a été envoyée.");
            } catch (TransportExceptionInterface $e) {
                $this->flashBag->add('danger', 'Le mail n\'a pas pu être envoyé: ' . $e->getMessage());
            }
        }
    }

    /**
     * L'admin demande plus d'infos
     * Si le userAdd est un contributeur on lui renvoie un mail
     * Sinon on renvoie aux auteurs
     * @param InterventionEvent $event
     */
    public function interventionInfo(InterventionEvent $event)
    {
        $intervention = $event->getIntervention();
        $message = $event->getMessage();
        $this->suiviService->newSuivi($intervention, $message);

        $this->flashBag->add("success", "L'intervention a bien été traitée");
        $userAdd = $this->userRepository->findOneBy(['username' => $intervention->getUserAdd()]);
        if ($userAdd) {
            $role = $this->travauxUtils->getRoleByEmail($userAdd->getEmail());
            switch ($role) {
                case 'contributeur':
                    try {
                        $this->mailer->sendMailPlusInfoContributeur($event);
                        $this->flashBag->add("success", "Un mail a été envoyé pour plus d'informations");
                    } catch (TransportExceptionInterface $e) {
                        $this->flashBag->add('danger', 'Le mail n\'a pas pu être envoyé: ' . $e->getMessage());
                    }
                    break;
                case 'auteur':
                    try {
                        $this->mailer->sendMailPlusInfoAuteur($event);
                        $this->flashBag->add("success", "Un mail a été envoyé pour plus d'informations");
                    } catch (TransportExceptionInterface $e) {
                        $this->flashBag->add('danger', 'Le mail n\'a pas pu être envoyé: ' . $e->getMessage());
                    }
                    break;
                case 'redacteur':
                    try {
                        $this->mailer->sendMailPlusInfoRedacteur($event);
                        $this->flashBag->add("success", "Un mail a été envoyé pour plus d'informations ");
                    } catch (TransportExceptionInterface $e) {
                        $this->flashBag->add('danger', 'Le mail n\'a pas pu être envoyé: ' . $e->getMessage());
                    }
                    break;
            }
        }
    }

    public function interventionArchive(InterventionEvent $event)
    {
        try {
            $this->mailer->sendMailArchive($event);
            $this->flashBag->add("success", "Un mail a été envoyé pour l'archivage");
        } catch (TransportExceptionInterface $e) {
            $this->flashBag->add('danger', 'Le mail n\'a pas pu être envoyé: ' . $e->getMessage());
        }
    }

    /**
     * @param InterventionEvent $event
     * @throws \Exception
     */
    public function interventionSuivi(InterventionEvent $event)
    {
        $intervention = $event->getIntervention();
        $intervention->setUpdated(new \DateTime());
        $this->interventionRepository->flush();
        try {
            $this->mailer->sendMailSuivi($event);
            $this->flashBag->add("success", "Un mail a été envoyé pour le suivis");
        } catch (TransportExceptionInterface $e) {
            $this->flashBag->add('danger', 'Le mail n\'a pas pu être envoyé: ' . $e->getMessage());
        }
    }
}
