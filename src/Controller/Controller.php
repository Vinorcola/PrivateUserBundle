<?php

namespace Vinorcola\PrivateUserBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class Controller extends AbstractController
{
    /**
     * Controller constructor.
     *
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private TranslatorInterface $translator,
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * Add a form error by translating the message key using the translation parameters.
     *
     * @param FormInterface $form
     * @param string        $message
     * @param array         $translationParameters
     */
    protected function addFormError(FormInterface $form, string $message, array $translationParameters = []): void
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
        $this->entityManager->flush();
    }
}
