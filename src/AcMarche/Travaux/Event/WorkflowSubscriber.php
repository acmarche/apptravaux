<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 8/12/16
 * Time: 13:25
 */

namespace AcMarche\Travaux\Event;

use AcMarche\Travaux\Service\Mailer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @TODO Comment l'appeler depuis un controller !!
 * http://symfony.com/doc/current/workflow/usage.html#using-events
 */
class WorkflowSubscriber implements EventSubscriberInterface
{
    private $em;
    private $authorizationChecker;
    private $mailer;
    private $twig;

    public function __construct(
        ObjectManager $em,
        AuthorizationCheckerInterface $authorizationChecker,
        Mailer $mailer,
        EngineInterface $twig
    ) {
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
        $this->mailer = $mailer;
        $this->twig = $twig;
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
