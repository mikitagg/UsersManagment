<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;


class UsersController extends AbstractController
{


    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security
    )
    {
    }

    #[Route('/users')]
    public function Table(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->render('table/table.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        $this->security->logout(false);
    }

    #[Route('/users/action', name: 'userAction', methods: ['POST'])]
    public function userActions(Request $request, UserRepository $userRepository)
    {
        $action = $request->get('action');
        $selectedUsers = $request->get('selected_users');

        if (!$selectedUsers) {
            return $this->redirectToRoute('app_users_table');
        }
        if ($action === 'delete') {
            $this->deleteAction($selectedUsers);
        }
        if ($action === 'block') {
                $this->blockAction($selectedUsers);
        }
        if ($action === 'unblock') {
                $this->unblockAction($selectedUsers);
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('app_users_table');

    }

    public function deleteAction(array $selected)
    {
        $currentUser = $this->security->getUser();
        if (in_array($currentUser->getId(), $selected)) {
            $this->security->logout(false);
        }

        foreach ($selected as $id) {

            $user = $this->entityManager->getRepository(User::class)->find($id);
            $this->entityManager->remove($user);
        }
    }

    public function blockAction(array $selected)
    {
        foreach ($selected as $id) {
            $user = $this->entityManager->getRepository(User::class)->find($id);
            $user->setStatus('Blocked');
            $user->setRoles(['ROLE_USER']);
            $user->setIsBlocked(true);

        }
    }

    public function unblockAction(array $selected)
    {
        foreach ($selected as $id) {
            $user = $this->entityManager->getRepository(User::class)->find($id);
            $user->setStatus('Active');
            $user->setIsBlocked(false);
        }
    }



}