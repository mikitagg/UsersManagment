<?php

namespace App\Service;



use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegisterUser
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function setUser(User $user, FormInterface $form): void
    {
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );
        $user->setRoles(['ROLE_ADMIN']);
        $user->setRegistrationDate();
        $user->setLastLoginDate();
        $user->setStatus('Active');
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}