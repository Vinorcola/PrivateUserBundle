<?php

namespace Vinorcola\PrivateUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vinorcola\PrivateUserBundle\DependencyInjection\VinorcolaPrivateUserExtension;

class VinorcolaPrivateUserBundle extends Bundle
{
    /**
     * {@inheritDoc}
     * @return VinorcolaPrivateUserExtension
     */
    public function getContainerExtension()
    {
        return new VinorcolaPrivateUserExtension();
    }
}
