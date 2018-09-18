<?php

namespace Vinorcola\PrivateUserBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Vinorcola\PrivateUserBundle\Data\FindUser;
use Vinorcola\PrivateUserBundle\Form\ChangePasswordType;
use Vinorcola\PrivateUserBundle\Form\FindUserType;
use Vinorcola\PrivateUserBundle\Model\EmailModel;
use Vinorcola\PrivateUserBundle\Model\UserManagerInterface;
use Vinorcola\PrivateUserBundle\Repository\UserRepositoryInterface;

/**
 * @Route(name="private_user.registration.")
 */
class RegistrationController extends Controller
{
    /**
     * The key used in session to remember the user currently registering.
     */
    private const USER_TO_REGISTER_SESSION_KEY = 'private_user.user_email_address';

    /**
     * @Route("/require-registration", methods={"GET", "POST"}, name="requireRegistration")
     *
     * @param Request                 $request
     * @param UserRepositoryInterface $repository
     * @param UserManagerInterface    $userManager
     * @param EmailModel              $emailModel
     * @return Response
     */
    public function requireRegistration(
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
                $this->addFormError($form, 'private_user.registration.requireRegistration.unknownUser');
            } else if ($user->getPassword() !== null) {
                $this->addFormError($form, 'private_user.registration.requireRegistration.alreadyRegistered');
            } else {
                $userManager->generateToken($user);
                $this->saveDatabase();

                $emailModel->sendRegistrationEmail($user);

                return $this->redirectToRoute('private_user.registration.confirmRegistrationRequest', [
                    'emailAddress' => $data->emailAddress,
                ]);
            }
        }

        return $this->render('@VinorcolaPrivateUser/Registration/requireRegistration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/require-registration/confirm/{emailAddress}", methods={"GET"}, name="confirmRegistrationRequest")
     *
     * @param string $emailAddress
     * @return Response
     */
    public function confirmRegistrationRequest(string $emailAddress): Response
    {
        return $this->render('@VinorcolaPrivateUser/Registration/confirmRegistrationRequest.html.twig', [
            'emailAddress' => $emailAddress,
        ]);
    }

    /**
     * @Route("/register/{token}", methods={"GET"}, name="register", requirements={
     *     "token": "^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$",
     * })
     *
     * @param SessionInterface        $session
     * @param string                  $token
     * @param UserRepositoryInterface $repository
     * @return Response
     */
    public function register(
        SessionInterface $session,
        string $token,
        UserRepositoryInterface $repository
    ): Response {

        $user = $repository->findByRegistrationToken($token);
        if (!$user) {
            return $this->redirectToRoute('private_user.registration.rejectRegistration');
        }

        $session->set(self::USER_TO_REGISTER_SESSION_KEY, $user->getEmailAddress());

        return $this->redirectToRoute('private_user.registration.definePassword');
    }

    /**
     * @Route("/register/reject", methods={"GET"}, name="rejectRegistration")
     *
     * @return Response
     */
    public function rejectRegistration(): Response
    {
        return $this->render('@VinorcolaPrivateUser/Registration/rejectRegistration.html.twig');
    }

    /**
     * @Route("/register/define-password", methods={"GET", "POST"}, name="definePassword")
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

        if (!$session->has(self::USER_TO_REGISTER_SESSION_KEY)) {
            return $this->redirectToRoute('private_user.registration.rejectRegistration');
        }

        $user = $repository->find($session->get(self::USER_TO_REGISTER_SESSION_KEY));
        if (!$user) {
            return $this->redirectToRoute('private_user.registration.rejectRegistration');
        }

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->updatePassword($user, $form->getData());
            $userManager->logUserIn($user);
            $this->saveDatabase();
            $session->remove(self::USER_TO_REGISTER_SESSION_KEY);

            return $this->redirectToRoute('private_user.registration.confirmRegistration');
        }

        return $this->render('@VinorcolaPrivateUser/Registration/definePassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/confirm", methods={"GET"}, name="confirmRegistration")
     *
     * @return Response
     */
    public function confirmRegistration(): Response
    {
        return $this->render('@VinorcolaPrivateUser/Registration/confirmRegistration.html.twig');
    }
}
