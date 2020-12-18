<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;

class CustomerController extends AbstractController
{
    /**
     * @Get(
     *     path = "api/customers",
     *     name = "app_customers_list"
     * )
     * @View
     *     statusCode = 200,
     */
    public function getProductList(CustomerRepository $customerRepository)
    {
        return $customerRepository->findAll();

    }

    /**
     * @Get(
     *     path = "api/customers/{id}",
     *     name = "app_customer_show",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     *     statusCode = 200,
     */
    public function getCustomer(Customer $customer)
    {
        return $customer;
    }
}
