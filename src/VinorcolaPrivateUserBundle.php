<?php

namespace Vinorcola\PrivateUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class VinorcolaPrivateUserBundle extends Bundle
{
    /**
     * {@inheritdoc}
     * @return VinorcolaPrivateUserExtension
     */
    public function getContainerExtension()
    {
        return new VinorcolaPrivateUserExtension();
    }
}
