<?php

namespace App\Controller;

use App\Repository\RetailerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request): Response
    {
        return $this->render('front/index.html.twig', [
            'access_token' => $request->get('access_token') ?? false,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): Response
    {
        $retailer = $this->getUser();
        $retailer->setApiToken(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($retailer);
        $em->flush();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/auth", name="github_auth")
     */
    public function auth(GoogleUserProvider $userProvider, Request $request): Response
    {
        $user = $userProvider->getGithubAccessToken($request->get('code'));

        return $this->redirectToRoute('home', ['access_token' => $user->getApiToken()]);
    }
}
