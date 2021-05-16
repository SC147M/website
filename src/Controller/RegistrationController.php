<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordFormType;
use App\Form\RegistrationFormType;
use App\Form\RequestPasswordFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler    $guardHandler
     * @param LoginFormAuthenticator       $authenticator
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $clubPw = $request->get('clubpw');

            if (strtolower($clubPw) !== 'whatashot') {
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                    'wrongClubPw'      => true,
                ]);
            }

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRoles(['ROLE_PENDING']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            return $this->render('registration/registered.html.twig');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'wrongClubPw'      => false,
        ]);
    }

    /**
     * @Route("/requestpassword", name="app_request_password")
     * @param Request        $request
     * @param UserRepository $userRepository
     * @return Response
     */
    public function requestPassword(Request $request, UserRepository $userRepository, \Swift_Mailer $mailer): Response
    {
        $form = $this->createForm(RequestPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()->getEmail();
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user === null) {
                $form->get('email')->addError(new FormError('Die E-Mail Adresse wurde nicht gefunden.'));
                return $this->render('registration/requestpassword.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            $hash = md5(rand());
            $user->setHash($hash);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $message = (new \Swift_Message('m-snooker.de - Passwort vergessen'))
                ->setFrom('no-reply@sc147.de')
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                    // templates/emails/registration.html.twig
                        'emails/requestpassword.html.twig',
                        ['hash' => $hash,
                         'user' => $user]
                    ),
                    'text/html'
                )
                // you can remove the following code if you don't define a text version for your emails
                ->addPart(
                    $this->renderView(
                    // templates/emails/registration.txt.twig
                        'emails/requestpassword.txt.twig',
                        ['hash' => $hash]
                    ),
                    'text/plain'
                );

            try {
                $mailer->send($message);
            } catch (\Throwable $e) {
                print $e->getMessage();
                die();
            }

            return $this->render('registration/rpmailsend.html.twig', [
                'registrationForm' => $form->createView(),
            ]);
        }

        return $this->render('registration/requestpassword.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/setpassword/{hash}", name="app_set_password")
     * @param User                         $user
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler    $guardHandler
     * @param LoginFormAuthenticator       $authenticator
     * @return Response
     */
    public function setPassword(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $form = $this->createForm(ForgotPasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setHash(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/setpassword.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
