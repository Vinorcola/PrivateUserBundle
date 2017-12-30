<?php

namespace Vinorcola\PrivateUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * Save the database.
     */
    protected function saveDatabase(): void
    {
        return $this->getDoctrine()->getManager()->flush();
    }
}
