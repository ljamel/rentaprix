<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MemberController extends AbstractController
{
    #[Route('/member', name: 'app_member')]
    public function index(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('Password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_member', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('member/index.html.twig', [
            'controller_name' => 'MemberController',
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
