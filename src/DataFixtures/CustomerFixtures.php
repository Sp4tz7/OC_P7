<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CustomerFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $clients = [
            [
                "fullname" => "Jean Neymar",
                "email" => "jean.neymar@contrarie.com",
            ],[
                "fullname" => "Jean Sérien",
                "email" => "jea.serien@inculte.com",
            ],[
                "fullname" => "Thérèse Plendissante",
                "email" => "therese.plendissante@bombasse.com",
            ],[
                "fullname" => "Carrie Bout",
                "email" => "carrie.bout@renne-noel.com",
            ],[
                "fullname" => "Sarah Croche",
                "email" => "sarah.croche@nokia.com",
            ],[
                "fullname" => "Sarah Pelle",
                "email" => "sarah.pelle@nokia.com",
            ],[
                "fullname" => "Sarah Masse",
                "email" => "sarah.masse@plein-la-tronche.com",
            ],[
                "fullname" => "Marc Déposée",
                "email" => "marc.deposee@copyright.com",
            ],[
                "fullname" => " Pierre Kiroule",
                "email" => "pierre.kiroule@rolling-stones.com",
            ],[
                "fullname" => "Paul Hochon",
                "email" => "paul.hochon@coussin.com",
            ],[
                "fullname" => "Agathe Zeblouze",
                "email" => "agathe.zeblouze@rolling-stones.com",
            ],

        ];

        foreach ($clients as $client) {
            $customer = new Customer();
            $customer->setFullname($client['fullname']);
            $customer->setEmail($client['email']);
            $customer->setRetailer($this->getReference(RetailerFixtures::RETAILER1_REFERENCE));
            $manager->persist($customer);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            RetailerFixtures::class,
        ];
    }
}
