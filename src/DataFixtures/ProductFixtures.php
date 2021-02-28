<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $mobiles = [
            [
                "Reference" => "pGAENEHVp2",
                "Name" => "Nokia",
                "Dimensions" => "143.4 x 71.4 x 8.5 mm",
                "Display_type" => "IPS LCD capacitive touchscreen  16M colors",
                "Display_size" => "720 x 1280 pixels (~294 ppi pixel density)",
            ],
            [
                "Reference" => "tzhPycsk7Z",
                "Name" => "Philips",
                "Dimensions" => "103 x 47.5 x 18.7 mm| 89 cc",
                "Display_type" => "STN  monochrome graphics",
                "Display_size" => "101 x 80 pixels| 5 lines",
            ],
            [
                "Reference" => "E72HYPBi8V",
                "Name" => "OnePlus",
                "Dimensions" => "154.2 x 74.1 x 7.3 mm",
                "Display_type" => "Optic AMOLED capacitive touchscreen  16M colors",
                "Display_size" => "1080 x 1920 pixels (~401 ppi pixel density)",
            ],
            [
                "Reference" => "G8Q3G5Vpoz",
                "Name" => "Philips",
                "Dimensions" => "104.5 x 45 x 12.8 mm",
                "Display_type" => "CSTN  Monochrome",
                "Display_size" => "96 x 64 pixels (~92 ppi pixel density)",
            ],
            [
                "Reference" => "EG9C0vfURd",
                "Name" => "Nokia",
                "Dimensions" => "110 x 46 x 14.8 mm| 63 cc",
                "Display_type" => "TFT  65K colors",
                "Display_size" => "128 x 160 pixels (~114 ppi pixel density)",
            ],
            [
                "Reference" => "2L9FZoJwHz",
                "Name" => "i-mobile",
                "Dimensions" => "103 x 45 x 14.4 mm",
                "Display_type" => "CSTN  65K colors",
                "Display_size" => "128 x 128 pixels (~121 ppi pixel density)",
            ],
            [
                "Reference" => "se3nHpKkgD",
                "Name" => "Nokia",
                "Dimensions" => "116 x 50 x 12.9 mm",
                "Display_type" => "TFT  256K colors",
                "Display_size" => "240 x 320 pixels (~166 ppi pixel density)",
            ],
            [
                "Reference" => "A9z7FThzCj",
                "Name" => "Nokia",
                "Dimensions" => "110 x 46 x 14.8 mm| 63 cc",
                "Display_type" => "TFT  65K colors",
                "Display_size" => "128 x 160 pixels (~114 ppi pixel density)",
            ],
            [
                "Reference" => "ZY6tn9bySF",
                "Name" => "Philips",
                "Dimensions" => "98 x 45 x 18.5 mm| 73 cc",
                "Display_type" => "CSTN  4096 colors",
                "Display_size" => "101 x 80 pixels| 5 lines",
            ],
            [
                "Reference" => "0CfRuav5Lc",
                "Name" => "i-mobile",
                "Dimensions" => "105 x 44 x 14.5 mm",
                "Display_type" => "CSTN  65K colors",
                "Display_size" => "128 x 160 pixels (~114 ppi pixel density)",
            ],
        ];

        $retailer = $this->getReference(RetailerFixtures::RETAILER1_REFERENCE);
        foreach ($mobiles as $mobile) {
            $product = new Product();
            $product->setName($mobile['Name']);
            $product->setReference($mobile['Reference']);
            $product->setDimensions($mobile['Dimensions']);
            $product->setDisplayType($mobile['Display_type']);
            $product->setDisplaySize($mobile['Display_size']);
            $manager->persist($product);
            $retailer->addProduct($product);
            $manager->persist($retailer);
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
