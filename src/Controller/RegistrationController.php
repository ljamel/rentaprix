<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $now = new \DateTime();
            $user->setRegistrationDate($now);
            $user->setConnectionDate($now);

            $entityManager->persist($user);
            $entityManager->flush();

            $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
            // do anything else you need here, like send an email
            // send email welcome to subscribe ps (don't use bundle symfony i use mail natif php)
            $to = $form->get('email')->getData();
            $subject = "Merci pour votre inscription";
            $message = "This is a test email sent from PHP using Sendmail.";
            $headers = "From: nereply@rentaprix.com\r\n";
            mail($to, $subject, $message, $headers);

            return $this->render('registration/payement.html.twig', [
                'registrationForm' => $form->createView(),
            ]);

            //return $userAuthenticator->authenticateUser(
              //  $user,
                //$authenticator,
                //$request
            //);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/payement', name: 'payement')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') && $this->getUser()->getSubscribeId() == null) {

            $user = $this->getUser();

            // add number subsicription
            $user->setSubscribeId($request->query->get('subId'));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Souscription réussi !');
            return $this->render('registration/payement.html.twig');
        } else {
            return $this->redirectToRoute('app_dashboard');
        }


    }
}
