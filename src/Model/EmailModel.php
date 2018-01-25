<?php

namespace Vinorcola\PrivateUserBundle\Model;

use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class EmailModel
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
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
     * @param Swift_Mailer        $mailer
     * @param Environment         $twigEnvironment
     * @param TranslatorInterface $translator
     * @param string              $fromAddress
     */
    public function __construct(Swift_Mailer $mailer, Environment $twigEnvironment, TranslatorInterface $translator, string $fromAddress)
    {
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
        $email = new Swift_Message($this->translator->trans('private_user.email.registration.title'));
        $email
            ->setFrom($this->fromAddress)
            ->setTo($user->getEmailAddress())
            ->setBody(
                $this->twigEnvironment->render('@VinorcolaPrivateUser/Email/registration.html.twig', [
                    'user' => $user,
                ]),
                'text/html'
            );

        $this->mailer->send($email);
    }
}
