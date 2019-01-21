<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 8/12/16
 * Time: 10:26
 */

namespace AcMarche\Travaux\Event;

use AcMarche\Travaux\Entity\Intervention;
use AcMarche\Travaux\Entity\Suivi;
use Symfony\Component\EventDispatcher\Event;

class InterventionEvent extends Event
{
    const INTERVENTION_NEW = 'ac_marche_travaux.intervention.new';
    const INTERVENTION_ACCEPT = 'ac_marche_travaux.intervention.accept';
    const INTERVENTION_REJECT = 'ac_marche_travaux.intervention.reject';
    const INTERVENTION_INFO = 'ac_marche_travaux.intervention.info';
    const INTERVENTION_ARCHIVE = 'ac_marche_travaux.intervention.archive';
    const INTERVENTION_SUIVI_NEW = 'ac_marche_travaux.intervention.suivi.new';

    protected $intervention;
    protected $message;
    protected $suivi;

    public function __construct(Intervention $intervention, $message, Suivi $suivi = null)
    {
        $this->intervention = $intervention;
        $this->message = $message;
        $this->suivi = $suivi;
    }

    public function getIntervention()
    {
        return $this->intervention;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getSuivi()
    {
        return $this->suivi;
    }
}
