<?php

namespace App\Security;

use App\Entity\Retailer;
use App\Repository\RetailerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GithubUserProvider implements UserProviderInterface
{
    private $client;
    private $serializer;
    private $container;
    private $entityManager;
    private $retailerRepository;

    public function __construct(
        HttpClientInterface $client,
        SerializerInterface $serializer,
        ContainerInterface $container,
        EntityManagerInterface $entityManager,
        RetailerRepository $retailerRepository
    ) {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->retailerRepository = $retailerRepository;
    }

    public function getGithubAccessToken($code)
    {

        $response = $this->client->request(
            'POST',
            'https://github.com/login/oauth/access_token',
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    "Accept" => "application/json",
                    "client_id" => $this->container->getParameter('app.git.key'),
                    "client_secret" => $this->container->getParameter('app.git.secret'),
                    "code" => $code,
                    "scope" => "user",
                ],
            ]
        );

        $responseData = $response->toArray();
        if (isset($responseData['access_token'])) {
            return $this->loadUserByUsername($responseData['access_token']);
        }
        $message = $response->getStatusCode().': '.$responseData['error'];
        throw new \Exception($message);

    }

    public function loadUserByUsername($access_token)
    {
        $response = $this->client->request(
            'GET',
            'https://api.github.com/user',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$access_token,
                ],
            ]
        );

        if (!$response) {
            throw new \LogicException('Did not managed to get your user info from Github.');
        }

        $userData = $response->toArray();
        if ($userData) {

            if (!$retailer = $this->retailerRepository->findOneBy(['username' => $userData['login']])) {
                $retailer = new Retailer($userData['name'], $userData['login'], $userData['email'], $access_token);
                $this->entityManager->persist($retailer);
                $this->entityManager->flush();

                return $retailer;
            }

            $retailer->setApiToken($access_token);
            $this->entityManager->persist($retailer);
            $this->entityManager->flush();

        }

        return $retailer;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException();
        }

        return $user;
    }

    public function supportsClass($class)
    {
        return 'App\Entity\Retailer' === $class;
    }


}