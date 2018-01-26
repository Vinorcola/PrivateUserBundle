<?php

namespace Vinorcola\PrivateUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

abstract class Controller extends BaseController
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Controller constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Add a form error by translating the message key using the translation parameters.
     *
     * @param FormInterface $form
     * @param string        $message
     * @param array         $translationParameters
     */
    protected function addFormError(FormInterface $form, string $message, array $translationParameters = [])
    {
        $form->addError(new FormError(
            $this->translator->trans(
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
