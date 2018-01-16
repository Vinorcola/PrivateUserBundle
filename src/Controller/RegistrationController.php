<?php

namespace Vinorcola\PrivateUserBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vinorcola\PrivateUserBundle\Data\FindUser;
use Vinorcola\PrivateUserBundle\Form\FindUserType;
use Vinorcola\PrivateUserBundle\Model\UserManagerInterface;
use Vinorcola\PrivateUserBundle\Repository\UserRepositoryInterface;

/**
 * @Route(name="private_user.registration.")
 */
class RegistrationController extends Controller
{
    /**
     * @Route("/require-registration", name="requireRegistration")
     * @Method({"GET", "POST"})
     *
     * @param Request                 $request
     * @param UserRepositoryInterface $repository
     * @param UserManagerInterface    $userManager
     * @return Response
     */
    public function requireRegistration(
        Request $request,
        UserRepositoryInterface $repository,
        UserManagerInterface $userManager
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

                // TODO: Send e-mail.

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
     * @Route("/require-registration/confirm/{emailAddress}", name="confirmRegistrationRequest")
     * @Method("GET")
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
}
