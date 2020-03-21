<?php


namespace AcMarche\Avaloir\Data;


use AcMarche\Avaloir\Repository\AvaloirRepository;

class Localite
{
    /**
     * @var AvaloirRepository
     */
    private $avaloirRepository;

    public function __construct(AvaloirRepository $avaloirRepository)
    {
        $this->avaloirRepository = $avaloirRepository;
    }

    public function getListRues()
    {
        $rues = [];
        foreach ($this->avaloirRepository->findAll() as $avaloir) {
            if ($avaloir->getRue()) {
                $rues[] = $avaloir->getRue();
            }
        }
        $rues = array_unique($rues);
        sort($rues);
        return $rues;
    }
}