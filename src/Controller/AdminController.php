<?php

namespace Vinorcola\PrivateUserBundle\Controller;

use IntlDateFormatter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Vinorcola\PrivateUserBundle\Data\EditUser;
use Vinorcola\PrivateUserBundle\Form\CreateUserType;
use Vinorcola\PrivateUserBundle\Form\EditUserType;
use Vinorcola\PrivateUserBundle\Form\GenerateActivationLinkType;
use Vinorcola\PrivateUserBundle\Model\EditableUserInterface;
use Vinorcola\PrivateUserBundle\Model\UserManagerInterface;
use Vinorcola\PrivateUserBundle\Repository\UserRepositoryInterface;

/**
 * @Route(name="private_user.admin.")
 */
class AdminController extends Controller
{
    /**
     * @Route("", methods={"GET"}, name="list")
     *
     * @param UserRepositoryInterface $userRepository
     * @param UserManagerInterface    $userManager
     * @return Response
     */
    public function list(UserRepositoryInterface $userRepository, UserManagerInterface $userManager): Response
    {
        $users = $userRepository->findAll();
        foreach ($users as $user) {
            /** @var EditableUserInterface $user */
            $user->setType($userManager->getUserType($user));
        }

        return $this->render('@VinorcolaPrivateUser/Admin/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/create", methods={"GET", "POST"}, name="create")
     *
     * @param Request              $request
     * @param UserManagerInterface $userManager
     * @return Response
     */
    public function create(Request $request, UserManagerInterface $userManager): Response
    {
        $form = $this->createForm(CreateUserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->create($form->getData());
            $this->saveDatabase();

            return $this->redirectToRoute('private_user.admin.list');
        }

        return $this->render('@VinorcolaPrivateUser/Admin/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{userEmailAddress}", methods={"GET", "POST"}, name="edit")
     *
     * @param Request                 $request
     * @param string                  $userEmailAddress
     * @param UserRepositoryInterface $repository
     * @param UserManagerInterface    $userManager
     * @return Response
     */
    public function edit(
        Request $request,
        string $userEmailAddress,
        UserRepositoryInterface $repository,
        UserManagerInterface $userManager
    ): Response {

        $user = $repository->find($userEmailAddress);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        $user->setType($userManager->getUserType($user));

        $form = $this->createForm(EditUserType::class, EditUser::FromUser($user));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->update($user, $form->getData());
            $this->saveDatabase();

            return $this->redirectToRoute('private_user.admin.list');
        }

        return $this->render('@VinorcolaPrivateUser/Admin/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{userEmailAddress}/generate-activation-link", methods={"GET", "POST"}, name="generateActivationLink")
     *
     * @param Request                 $request
     * @param string                  $userEmailAddress
     * @param UserRepositoryInterface $repository
     * @param UserManagerInterface    $userManager
     * @return Response
     */
    public function generateActivationLink(
        Request $request,
        string $userEmailAddress,
        UserRepositoryInterface $repository,
        UserManagerInterface $userManager
    ): Response {

        $user = $repository->find($userEmailAddress);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        if ($user->isActivated()) {
            throw new AccessDeniedHttpException('User is already activated.');
        }

        $form = $this->createForm(GenerateActivationLinkType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->generateToken($user);
            $this->saveDatabase();

            return $this->redirectToRoute('private_user.admin.activationLink', [
                'userEmailAddress' => $userEmailAddress,
            ]);
        }

        return $this->render('@VinorcolaPrivateUser/Admin/generateActivationLink.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{userEmailAddress}/activation-link", methods={"GET"}, name="activationLink")
     *
     * @param Request                 $request
     * @param string                  $userEmailAddress
     * @param UserRepositoryInterface $repository
     * @return Response
     */
    public function activationLink(
        Request $request,
        string $userEmailAddress,
        UserRepositoryInterface $repository
    ): Response {

        $user = $repository->find($userEmailAddress);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        if ($user->isActivated()) {
            throw new AccessDeniedHttpException('User is already activated.');
        }

        $formatter = new IntlDateFormatter($request->getLocale(), IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM);

        return $this->render('@VinorcolaPrivateUser/Admin/activationLink.html.twig', [
            'user'           => $user,
            'expirationDate' => $formatter->format($user->getTokenExpirationDate()),
        ]);
    }

    /**
     * @Route("/{userEmailAddress}/generate-forgotten-password-link", methods={"GET", "POST"}, name="generateForgottenPasswordLink")
     *
     * @param Request                 $request
     * @param string                  $userEmailAddress
     * @param UserRepositoryInterface $repository
     * @param UserManagerInterface    $userManager
     * @return Response
     */
    public function generateForgottenPasswordLink(
        Request $request,
        string $userEmailAddress,
        UserRepositoryInterface $repository,
        UserManagerInterface $userManager
    ): Response {

        $user = $repository->find($userEmailAddress);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        if (!$user->isActivated()) {
            throw new AccessDeniedHttpException('User is not activated.');
        }

        $form = $this->createForm(GenerateActivationLinkType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->generateToken($user);
            $this->saveDatabase();

            return $this->redirectToRoute('private_user.admin.forgottenPasswordLink', [
                'userEmailAddress' => $userEmailAddress,
            ]);
        }

        return $this->render('@VinorcolaPrivateUser/Admin/generateForgottenPasswordLink.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{userEmailAddress}/forgotten-password-link", methods={"GET"}, name="forgottenPasswordLink")
     *
     * @param Request                 $request
     * @param string                  $userEmailAddress
     * @param UserRepositoryInterface $repository
     * @return Response
     */
    public function forgottenPasswordLink(
        Request $request,
        string $userEmailAddress,
        UserRepositoryInterface $repository
    ): Response {

        $user = $repository->find($userEmailAddress);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        if (!$user->isActivated()) {
            throw new AccessDeniedHttpException('User is already activated.');
        }

        $formatter = new IntlDateFormatter($request->getLocale(), IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM);

        return $this->render('@VinorcolaPrivateUser/Admin/forgottenPasswordLink.html.twig', [
            'user'           => $user,
            'expirationDate' => $formatter->format($user->getTokenExpirationDate()),
        ]);
    }
}
