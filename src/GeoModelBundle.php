<?php

namespace Maris\Symfony\Geo\Model;

use Maris\Symfony\Geo\Model\DependencyInjection\GeoModelExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class GeoModelBundle extends AbstractBundle{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new GeoModelExtension();
    }

}