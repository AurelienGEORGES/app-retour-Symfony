<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $password = $data['password'];
            $confirmPassword = $data['confirm_password'];

            if ($password !== $confirmPassword) {
                $this->addFlash('notice', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_register');
            }

            $user = new User();
            $user->setUsername($data['username']);
            $user->setName($data['prenom']);
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $password
                )
            );
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('notice', 'Utilisateur créé avec succès.');

            return $this->redirectToRoute('app_home'); 
        }

        return $this->render('utilisateur/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
