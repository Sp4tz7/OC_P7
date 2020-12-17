<?php

namespace App\Controller;

use App\Security\GithubUserProvider;
use App\Security\TokenAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use GuzzleHttp\Client;
use JMS\Serializer\Serializer;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }

    /**
     * @Route("/auth", name="auth")
     */
    public function auth(GithubUserProvider $userProvider): Response
    {
        dump( $userProvider ); die();
    }
}
