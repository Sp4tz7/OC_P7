<?php

namespace App\Security;

use App\Entity\User;
use GuzzleHttp\Client;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GithubUserProvider implements UserProviderInterface
{
    private $client;
    private $serializer;

    public function __construct(Client $client, SerializerInterface  $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    public function loadUserByUsername($username)
    {
        $url = 'https://api.github.com/user?access_token='.$username;

        $response = $this->client->get($url);
        $res = $response->getBody()->getContents();
        $userData = $this->serializer->deserialize($res, 'array', 'json');

        if (!$userData) {
            throw new \LogicException('Did not managed to get your user info from Github.');
        }

        return new User($userData['email']);
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
        return 'AppBundle\Entity\User' === $class;
    }
}