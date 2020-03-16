<?php

namespace Vinorcola\PrivateUserBundle\Model;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TemplateEngine;

class EmailModel
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var TemplateEngine
     */
    private $twigEnvironment;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $fromAddress;

    /**
     * EmailModel constructor.
     *
     * @param MailerInterface     $mailer
     * @param TemplateEngine      $twigEnvironment
     * @param TranslatorInterface $translator
     * @param string              $fromAddress
     */
    public function __construct(
        MailerInterface $mailer,
        TemplateEngine $twigEnvironment,
        TranslatorInterface $translator,
        string $fromAddress
    ) {
        $this->mailer = $mailer;
        $this->twigEnvironment = $twigEnvironment;
        $this->translator = $translator;
        $this->fromAddress = $fromAddress;
    }

    /**
     * @param UserInterface $user
     */
    public function sendRegistrationEmail(UserInterface $user): void
    {
        $email = new Email();
        $email->subject($this->translator->trans('private_user.email.registration.title'));
        $email->from($this->fromAddress);
        $email->to($user->getEmailAddress());
        $email->html(
            $this->twigEnvironment->render('@VinorcolaPrivateUser/Email/registration.html.twig', [
                'user' => $user,
            ])
        );

        $this->mailer->send($email);
    }

    /**
     * @param UserInterface $user
     */
    public function sendPasswordChangeEmail(UserInterface $user): void
    {
        $email = new Email();
        $email->subject($this->translator->trans('private_user.email.forgottenPassword.title'));
        $email->from($this->fromAddress);
        $email->to($user->getEmailAddress());
        $email->html(
            $this->twigEnvironment->render('@VinorcolaPrivateUser/Email/forgottenPassword.html.twig', [
                'user' => $user,
            ])
        );

        $this->mailer->send($email);
    }
}
