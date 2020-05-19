<?php


namespace AcMarche\Avaloir\Location;

use AcMarche\Avaloir\Entity\Avaloir;
use AcMarche\Avaloir\MailerAvaloir;
use AcMarche\Avaloir\Repository\AvaloirRepository;
use AcMarche\Avaloir\Repository\RueRepository;

class LocationUpdater
{
    /**
     * @var AvaloirRepository
     */
    private $avaloirRepository;
    /**
     * @var RueRepository
     */
    private $rueRepository;
    /**
     * @var LocationReverseInterface
     */
    private $locationReverse;
    /**
     * @var MailerAvaloir
     */
    private $mailerAvaloir;

    public function __construct(
        AvaloirRepository $avaloirRepository,
        RueRepository $rueRepository,
        LocationReverseInterface $locationReverse,
        MailerAvaloir $mailerAvaloir
    ) {
        $this->avaloirRepository = $avaloirRepository;
        $this->rueRepository = $rueRepository;
        $this->locationReverse = $locationReverse;
        $this->mailerAvaloir = $mailerAvaloir;
    }

    public function updateRueAndLocalite(Avaloir $avaloir)
    {
        try {
            $result = $this->locationReverse->reverse($avaloir->getLatitude(), $avaloir->getLongitude());
            if ($result['status'] == 'OK') {
                $avaloir->setRue($this->locationReverse->getRoad());
                $road = $this->locationReverse->getRoad();
                if ($road) {
                    $avaloir->setRue($road);
                    $rue = $this->rueRepository->findOneByRue($road);
                    if ($rue) {
                        $avaloir->setLocalite($rue->getVillage());
                    } else {
                        $this->mailerAvaloir->sendError(
                            'rue non trouvee',
                            ['message' => 'dans db sql', 'rueName' => $road]
                        );
                        $avaloir->setLocalite($this->locationReverse->getLocality());
                    }
                    $this->avaloirRepository->flush();
                }
            } else {
                $this->mailerAvaloir->sendError('result pas OK', $result);
            }
        } catch (\Exception $e) {
            $this->mailerAvaloir->sendError($e->getMessage(), $result);
        }
    }
}
