<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Exception\ApiForbiddenException;
use App\Exception\ApiValidationException;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
    public function getCustomerList(CustomerRepository $customerRepository)
    {
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            return $customerRepository->findAll();
        }

        return $customerRepository->findBy(['retailer' => $this->getUser()]);

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
        if ($customer->getRetailer() == $this->getUser()){

            return $customer;
        }

        throw new ApiForbiddenException('You are not authorized to access this page. You can only access your own customer', 403);
    }

    /**
     * @Delete(
     *     path = "api/customers/{id}",
     *     name = "app_customer_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     *     statusCode = 200,
     */
    public function deleteCustomer(Customer $customer)
    {
        if ($customer->getRetailer() == $this->getUser()){
            $em = $this->getDoctrine()->getManager();
            $em->remove($customer);
            $em->flush();
            return ['removed' => true];
        }

        throw new ApiForbiddenException('You are not authorized to access this page. You can only delete your own customer.', 403);
    }
}
