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
use Symfony\Contracts\EventDispatcher\Event;

class InterventionEvent extends Event
{
    const INTERVENTION_NEW = 'ac_marche_travaux.intervention.new';
    const INTERVENTION_ACCEPT = 'ac_marche_travaux.intervention.accept';
    const INTERVENTION_REJECT = 'ac_marche_travaux.intervention.reject';
    const INTERVENTION_INFO = 'ac_marche_travaux.intervention.info';
    const INTERVENTION_REPORTER = 'ac_marche_travaux.intervention.reporter';
    const INTERVENTION_ARCHIVE = 'ac_marche_travaux.intervention.archive';
    const INTERVENTION_SUIVI_NEW = 'ac_marche_travaux.intervention.suivi.new';

    /**
     * @var Intervention
     */
    protected $intervention;
    /**
     * @var string
     */
    protected $message;
    /**
     * @var Suivi
     */
    protected $suivi;

    /**
     * @var \DateTimeInterface | null
     */
    protected $dateExecution;

    public function __construct(
        Intervention $intervention,
        $message,
        Suivi $suivi = null,
        \DateTimeInterface $dateExecution = null
    ) {
        $this->intervention = $intervention;
        $this->message = $message;
        $this->suivi = $suivi;
        $this->dateExecution = $dateExecution;
    }

    public function getIntervention(): Intervention
    {
        return $this->intervention;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getSuivi(): Suivi
    {
        return $this->suivi;
    }

    public function getDateExecution(): ?\DateTimeInterface
    {
        return $this->dateExecution;
    }

}
