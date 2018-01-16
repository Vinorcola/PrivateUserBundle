<?php

namespace Vinorcola\PrivateUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

abstract class Controller extends BaseController
{
    /**
     * Add a form error by translating the message key using the translation parameters.
     *
     * @param FormInterface $form
     * @param string        $message
     * @param array         $translationParameters
     */
    protected function addFormError(FormInterface $form, string $message, array $translationParameters = [])
    {
        /** @var TranslatorInterface $translator */
        $translator = $this->get(TranslatorInterface::class);
        $form->addError(new FormError(
            $translator->trans(
                $message,
                $translationParameters,
                'validators'
            ),
            $message,
            $translationParameters
        ));
    }

    /**
     * Save the database.
     */
    protected function saveDatabase(): void
    {
        $this->getDoctrine()->getManager()->flush();
    }
}
