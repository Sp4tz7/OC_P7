<?php

namespace App\Controller;

use App\Entity\Retailer;
use App\Repository\RetailerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;


class RetailerController extends AbstractController
{
    /**
     * @Get(
     *     path = "api/retailers",
     *     name = "app_retailers_list"
     * )
     * @View
     *     statusCode = 200,
     */
    public function getRetailerList(RetailerRepository $retailerRepository)
    {
        return $retailerRepository->findAll();

    }

    /**
     * @Get(
     *     path = "api/retailers/{id}",
     *     name = "app_retailer_show",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     *     statusCode = 200,
     */
    public function getRetailerDetail(Retailer $retailer)
    {
        return $retailer;
    }
}
