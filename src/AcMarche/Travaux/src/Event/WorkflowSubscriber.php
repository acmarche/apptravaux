<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 8/12/16
 * Time: 13:25
 */

namespace AcMarche\Travaux\Event;

use AcMarche\Travaux\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

/**
 * @TODO Comment l'appeler depuis un controller !!
 * http://symfony.com/doc/current/workflow/usage.html#using-events
 */
class WorkflowSubscriber implements EventSubscriberInterface
{
    private $em;
    private $authorizationChecker;
    private $mailer;

    public function __construct(
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authorizationChecker,
        Mailer $mailer
    ) {
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
        $this->mailer = $mailer;
    }

    /**
     * @override
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'workflow.intervention_publication.enter.publish' => array('publish'),
        );
    }

    public function publish(Event $event)
    {
        $intervention = $event->getSubject();
    }
}
