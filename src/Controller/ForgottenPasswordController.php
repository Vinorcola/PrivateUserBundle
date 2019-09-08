<?php

namespace Vinorcola\PrivateUserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Vinorcola\PrivateUserBundle\Data\FindUser;
use Vinorcola\PrivateUserBundle\Form\ChangePasswordType;
use Vinorcola\PrivateUserBundle\Form\FindUserType;
use Vinorcola\PrivateUserBundle\Model\EmailModel;
use Vinorcola\PrivateUserBundle\Model\UserManagerInterface;
use Vinorcola\PrivateUserBundle\Repository\UserRepositoryInterface;

/**
 * @Route(name="private_user.forgottenPassword.")
 */
class ForgottenPasswordController extends Controller
{
    /**
     * The key used in session to remember the user currently resetting password.
     */
    private const USER_TO_UPDATE_SESSION_KEY = 'private_user.user_email_address';

    /**
     * @Route("/declare-forgotten-password", methods={"GET", "POST"}, name="requirePasswordChange")
     *
     * @param Request                 $request
     * @param UserRepositoryInterface $repository
     * @param UserManagerInterface    $userManager
     * @param EmailModel              $emailModel
     * @return Response
     */
    public function requirePasswordChange(
        Request $request,
        UserRepositoryInterface $repository,
        UserManagerInterface $userManager,
        EmailModel $emailModel
    ): Response {

        $form = $this->createForm(FindUserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var FindUser $data */
            $data = $form->getData();
            $user = $repository->find($data->emailAddress);
            if (!$user) {
                $this->addFormError($form, 'private_user.forgottenPassword.requirePasswordChange.unknownUser');
            } else if ($user->getPassword() === null) {
                $this->addFormError($form, 'private_user.forgottenPassword.requirePasswordChange.notRegistered');
            } else {
                $userManager->generateToken($user);
                $this->saveDatabase();

                $emailModel->sendPasswordChangeEmail($user);

                return $this->redirectToRoute('private_user.forgottenPassword.confirmPasswordChangeRequest', [
                    'emailAddress' => $data->emailAddress,
                ]);
            }
        }

        return $this->render('@VinorcolaPrivateUser/ForgottenPassword/requirePasswordChange.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/declare-forgotten-password/confirm/{emailAddress}", methods={"GET"}, name="confirmPasswordChangeRequest")
     *
     * @param string $emailAddress
     * @return Response
     */
    public function confirmPasswordChangeRequest(string $emailAddress): Response
    {
        return $this->render('@VinorcolaPrivateUser/ForgottenPassword/confirmPasswordChangeRequest.html.twig', [
            'emailAddress' => $emailAddress,
        ]);
    }

    /**
     * @Route("/change-password/{token}", methods={"GET"}, name="changePassword", requirements={
     *     "token": "^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$",
     * })
     *
     * @param SessionInterface        $session
     * @param string                  $token
     * @param UserRepositoryInterface $repository
     * @return Response
     */
    public function changePassword(
        SessionInterface $session,
        string $token,
        UserRepositoryInterface $repository
    ): Response {

        $user = $repository->findByPasswordChangeToken($token);
        if (!$user) {
            return $this->redirectToRoute('private_user.forgottenPassword.rejectPasswordChange');
        }

        $session->set(self::USER_TO_UPDATE_SESSION_KEY, $user->getEmailAddress());

        return $this->redirectToRoute('private_user.forgottenPassword.definePassword');
    }

    /**
     * @Route("/change-password/reject", methods={"GET"}, name="rejectPasswordChange")
     *
     * @return Response
     */
    public function rejectPasswordChange(): Response
    {
        return $this->render('@VinorcolaPrivateUser/ForgottenPassword/rejectPasswordChange.html.twig');
    }

    /**
     * @Route("/change-password/define-password", methods={"GET", "POST"}, name="definePassword")
     *
     * @param SessionInterface        $session
     * @param Request                 $request
     * @param UserRepositoryInterface $repository
     * @param UserManagerInterface    $userManager
     * @return Response
     */
    public function definePassword(
        SessionInterface $session,
        Request $request,
        UserRepositoryInterface $repository,
        UserManagerInterface $userManager
    ): Response {

        if (!$session->has(self::USER_TO_UPDATE_SESSION_KEY)) {
            return $this->redirectToRoute('private_user.forgottenPassword.rejectPasswordChange');
        }

        $user = $repository->find($session->get(self::USER_TO_UPDATE_SESSION_KEY));
        if (!$user) {
            return $this->redirectToRoute('private_user.forgottenPassword.rejectPasswordChange');
        }

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->updatePassword($user, $form->getData());
            $userManager->logUserIn($user);
            $this->saveDatabase();
            $session->remove(self::USER_TO_UPDATE_SESSION_KEY);

            return $this->redirectToRoute('private_user.forgottenPassword.confirmPasswordChange');
        }

        return $this->render('@VinorcolaPrivateUser/ForgottenPassword/definePassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/change-password/confirm", methods={"GET"}, name="confirmPasswordChange")
     *
     * @return Response
     */
    public function confirmPasswordChange(): Response
    {
        return $this->render('@VinorcolaPrivateUser/ForgottenPassword/confirmPasswordChange.html.twig');
    }
}
