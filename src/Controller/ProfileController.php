<?php

namespace Vinorcola\PrivateUserBundle\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vinorcola\PrivateUserBundle\Data\ChangePassword;
use Vinorcola\PrivateUserBundle\Form\ChangePasswordType;
use Vinorcola\PrivateUserBundle\Model\UserInterface;
use Vinorcola\PrivateUserBundle\Model\UserManagerInterface;
use Vinorcola\PrivateUserBundle\Repository\UserRepositoryInterface;

/**
 * @Route("/profile", name="private_user.profile.")
 */
class ProfileController extends Controller
{
    private const CHANGE_PASSWORD_ERRORS_SESSION_KEY = 'private_user.change_password_errors';

    /**
     * @Route("", methods={"GET"}, name="profile")
     *
     * @param SessionInterface      $session
     * @param UrlGeneratorInterface $urlGenerator
     * @return Response
     */
    public function profile(SessionInterface $session, UrlGeneratorInterface $urlGenerator): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        $changePasswordForm = $this->createForm(ChangePasswordType::class, null, [
            'action'                   => $urlGenerator->generate('private_user.profile.change-password'),
            'require_current_password' => true,
        ]);
        if ($session->has(self::CHANGE_PASSWORD_ERRORS_SESSION_KEY)) {
            /** @var FormError[] $errors */
            $errors = $session->get(self::CHANGE_PASSWORD_ERRORS_SESSION_KEY);
            $session->remove(self::CHANGE_PASSWORD_ERRORS_SESSION_KEY);

            foreach ($errors as $error) {
                $this->addFormError($changePasswordForm, $error['messageKey'], $error['messageParams']);
            }
        }

        return $this->render('@VinorcolaPrivateUser/Profile/profile.html.twig', [
            'user'               => $user,
            'changePasswordForm' => $changePasswordForm->createView(),
        ]);
    }

    /**
     * @Route("/change-password", methods={"POST"}, name="change-password")
     *
     * @param SessionInterface            $session
     * @param Request                     $request
     * @param UserRepositoryInterface     $repository
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param UserManagerInterface        $userManager
     * @param TranslatorInterface         $translator
     * @return Response
     */
    public function changePassword(
        SessionInterface $session,
        Request $request,
        UserRepositoryInterface $repository,
        UserPasswordHasherInterface $passwordEncoder,
        UserManagerInterface $userManager,
        TranslatorInterface $translator
    ): Response {

        /** @var UserInterface $securityUser */
        $securityUser = $this->getUser();
        if (!$securityUser || !$securityUser->isEnabled()) {
            throw new NotFoundHttpException();
        }
        $user = $repository->find($securityUser->getEmailAddress());
        if (!$user || !$user->isEnabled()) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(ChangePasswordType::class, null, [
            'require_current_password' => true,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ChangePassword $data */
            $data = $form->getData();

            if ($passwordEncoder->isPasswordValid($user, $data->currentPassword)) {
                $userManager->updatePassword($user, $data);
                $this->saveDatabase();

                $this->addFlash('success', $translator->trans('private_user.profile.changePassword.success'));
            } else {
                $this->addFormError($form, 'private_user.invalid_password');
                $this->setErrorsInSession($session, $form);
            }
        } else {
            $this->setErrorsInSession($session, $form);
        }

        return $this->redirectToRoute('private_user.profile.profile');
    }

    /**
     * @param SessionInterface $session
     * @param FormInterface    $form
     */
    private function setErrorsInSession(SessionInterface $session, FormInterface $form): void
    {
        $data = [];
        foreach ($form->getErrors() as $error) {
            $data[] = [
                'messageKey'    => $error->getMessageTemplate(),
                'messageParams' => $error->getMessageParameters(),
            ];
        }

        $session->set(self::CHANGE_PASSWORD_ERRORS_SESSION_KEY, $data);
    }
}
