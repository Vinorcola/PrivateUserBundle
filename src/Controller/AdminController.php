<?php

namespace Vinorcola\PrivateUserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vinorcola\PrivateUserBundle\Form\CreateUserType;
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
}
