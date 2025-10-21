<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\UserType;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request,EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable();
            $password = $form->get('password')->getData();
            $password = password_hash($password, PASSWORD_BCRYPT);
            $user->setPassword($password);
            $user->setCreatedAt($now);
            $user->setUpdatedAt($now);
            $user->setRoles(['ROLE_USER']);
            $user->setLastConnection($now);
            $user->setConnected(true);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }



        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form->createView(),
        ]);
    }
}
