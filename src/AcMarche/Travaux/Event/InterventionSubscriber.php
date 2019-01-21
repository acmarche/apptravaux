<?php

namespace AcMarche\Travaux\Event;

use AcMarche\Travaux\Repository\UserRepository;
use AcMarche\Travaux\Service\Mailer;
use AcMarche\Travaux\Service\SuiviService;
use AcMarche\Travaux\Service\TravauxUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 8/12/16
 * Time: 10:39
 */
class InterventionSubscriber implements EventSubscriberInterface
{
    private $em;
    private $authorizationChecker;
    private $mailer;
    private $token;
    private $userRepository;
    private $flashBag;
    private $travauxUtils;
    protected $suiviService;

    public function __construct(
        ObjectManager $em,
        AuthorizationCheckerInterface $authorizationChecker,
        Mailer $mailer,
        TokenStorageInterface $tokenStorage,
        FlashBagInterface $flashBag,
        TravauxUtils $travauxUtils,
        SuiviService $suiviService,
        UserRepository $userRepository
    ) {
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
        $this->mailer = $mailer;
        $this->token = $tokenStorage;
        $this->userRepository = $userRepository;
        $this->flashBag = $flashBag;
        $this->travauxUtils = $travauxUtils;
        $this->suiviService = $suiviService;
    }

    public static function getSubscribedEvents()
    {
        //Liste des évènements écoutés et méthodes à appeler
        return array(
            InterventionEvent::INTERVENTION_NEW => 'interventionNew',
            InterventionEvent::INTERVENTION_ACCEPT => 'interventionAccept',
            InterventionEvent::INTERVENTION_REJECT => 'interventionReject',
            InterventionEvent::INTERVENTION_INFO => 'interventionInfo',
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
            $this->mailer->sendNewIntervention($event);
        }

        /**
         * ADD BY REDACTEUR
         * Je previens par mail les admins pour validation
         */
        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_REDACTEUR')) {
            $this->mailer->sendAskValidation($event);
        }

        /**
         * ADD BY AUTEUR
         * Je previens par mail les admins pour validation
         */
        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_AUTEUR')) {
            $this->mailer->sendAskValidation($event);
        }

        /**
         * ADD BY CONTRIBUTEUR
         * Je previens par mail les auteurs pour validation
         */
        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_CONTRIBUTEUR')) {
            $this->mailer->sendAskValidation($event);
        }
    }

    /**
     * L'intervention est acceptée
     * @param InterventionEvent $event
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
            $this->em->persist($intervention);
            $this->em->flush();
        }

        $this->suiviService->newSuivi($intervention, $message);
        $this->flashBag->add("success", "L'intervention a bien été acceptée");

        $this->mailer->sendMailAcceptOrReject($event, "acceptée");

        /**
         * ACCEPT BY AUTEUR
         * Je demande une validation à admin
         */
        if ($this->authorizationChecker->isGranted('ROLE_TRAVAUX_AUTEUR')) {
            $this->mailer->sendAskValidationForAdmin($event);
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
        $intervention = $event->getIntervention();
        $this->em->remove($intervention);
        $this->em->flush();
        $this->flashBag->add("success", "L'intervention a bien été refusée");
        $this->mailer->sendMailAcceptOrReject($event, "refusée");
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
                    $this->mailer->sendMailPlusInfoContributeur($event);
                    break;
                case 'auteur':
                    $this->mailer->sendMailPlusInfoAuteur($event);
                    break;
                case 'redacteur':
                    $this->mailer->sendMailPlusInfoRedacteur($event);
                    break;
            }
        }
    }

    public function interventionArchive(InterventionEvent $event)
    {
        $this->mailer->sendMailArchive($event);
    }

    /**
     * @param InterventionEvent $event
     */
    public function interventionSuivi(InterventionEvent $event)
    {
        $intervention = $event->getIntervention();
        $intervention->setUpdated(new \DateTime());
        $this->em->persist($intervention);
        $this->em->flush();
        $this->mailer->sendMailSuivi($event);
    }
}
