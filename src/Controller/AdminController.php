<?php

namespace Vinorcola\PrivateUserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vinorcola\PrivateUserBundle\Data\EditUser;
use Vinorcola\PrivateUserBundle\Form\CreateUserType;
use Vinorcola\PrivateUserBundle\Form\EditUserType;
use Vinorcola\PrivateUserBundle\Model\UserManagerInterface;
use Vinorcola\PrivateUserBundle\Repository\UserRepositoryInterface;

/**
 * @Route(name="private_user.admin.")
 */
class AdminController extends Controller
{
    /**
     * @Route("", name="list")
     * @Method("GET")
     *
     * @param UserRepositoryInterface $userRepository
     * @return Response
     */
    public function list(UserRepositoryInterface $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('@VinorcolaPrivateUser/Admin/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @Method({"GET", "POST"})
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
     * @Route("/edit/{userEmailAddress}", name="edit")
     * @Method({"GET", "POST"})
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
}
