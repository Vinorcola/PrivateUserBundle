<?php

namespace Vinorcola\PrivateUserBundle\Controller;

use LogicException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(name: 'private_user.security.')]
class SecurityController extends Controller
{
    #[Route('/login', methods: 'GET', name: 'login')]
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

    #[Route('/login', methods: 'POST', name: 'loginCheck')]
    public function loginCheck()
    {
        throw new LogicException('Symfony handles itself the login check action. Check your configuration to make sure you setup the good route as "check_path" option.');
    }

    #[Route('/logout', methods: 'GET', name: 'logout')]
    public function logout()
    {
        throw new LogicException('Symfony handles itself the logout action. Check your configuration to make sure you setup the good route as "logout.path" option.');
    }
}
