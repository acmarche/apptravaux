<?php

namespace AcMarche\Travaux;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcMarcheTravauxBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
