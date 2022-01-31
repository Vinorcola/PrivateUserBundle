<?php

namespace Vinorcola\PrivateUserBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vinorcola\PrivateUserBundle\DependencyInjection\VinorcolaPrivateUserExtension;

class VinorcolaPrivateUserBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new VinorcolaPrivateUserExtension();
    }
}
