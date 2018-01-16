<?php

namespace Vinorcola\PrivateUserBundle\Controller;

use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route(name="private_user.security.")
 */
class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Method("GET")
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('@VinorcolaPrivateUser/Security/login.html.twig', [
            'hasError'         => $error !== null,
            'errorMessageKey'  => $error ? $error->getMessageKey() : null,
            'errorMessageData' => $error ? $error->getMessageData() : null,
            'lastEmailAddress' => $authenticationUtils->getLastUsername(),
        ]);
    }

    /**
     * @Route("/login", name="loginCheck")
     * @Method("POST")
     */
    public function loginCheckAction()
    {
        throw new LogicException('Symfony handles itself the login check action. Check your configuration to make sure you setup the good route as "check_path" option.');
    }

    /**
     * @Route("/logout", name="logout")
     * @Method("GET")
     */
    public function logoutAction()
    {
        throw new LogicException('Symfony handles itself the logout action. Check your configuration to make sure you setup the good route as "logout.path" option.');
    }
}
