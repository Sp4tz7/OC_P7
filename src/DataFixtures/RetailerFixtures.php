<?php

namespace App\DataFixtures;

use App\Entity\Retailer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RetailerFixtures extends Fixture
{

    public const RETAILER1_REFERENCE = 'retailer-1';

    public function load(ObjectManager $manager)
    {
        $retailer = new Retailer('Swisscom SARL', 'swisscom', 'example@example.com', '');
        $retailer->setRoles(['ROLE_USER']);

        $manager->persist($retailer);

        $manager->flush();

        $this->addReference(self::RETAILER1_REFERENCE, $retailer);
    }


}
