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
            if ($this->isResultOk($result)) {
                $road = $this->locationReverse->getRoad();
                if ($road) {
                    if ($number = $this->locationReverse->getHouseNumber()) {
                        $road = $road.' '.$number;
                    }
                    $avaloir->setRue($road);
                    $rue = $this->rueRepository->findOneByRue($road);
                    if ($rue) {
                        $avaloir->setLocalite($rue->getVillage());
                    } else {
                        $this->mailerAvaloir->sendError(
                            'rue non trouvee dans db sql',
                            ['message' => 'dans db sql', 'rueName' => $road]
                        );
                        $avaloir->setLocalite($this->locationReverse->getLocality());
                    }
                    $this->avaloirRepository->flush();
                } else {
                    $this->mailerAvaloir->sendError('road non trouve dans api. Avaloir id '.$avaloir->getId(), $result);
                }
            } else {
                $this->mailerAvaloir->sendError('result pas OK', $result);
            }
        } catch (\Exception $e) {
            $this->mailerAvaloir->sendError($e->getMessage(), $result);
        }
    }

    protected function isResultOk(array $result): bool
    {
        if (isset($result['status']) && $result['status'] == 'OK') {
            return true;
        }

        if (!isset($result['error'])) {
            return true;
        }

        return false;
    }
}
