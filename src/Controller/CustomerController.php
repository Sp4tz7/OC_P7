<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Exception\ApiException;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\HttpFoundation\Response;

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

        $exception = new ApiException(403);
        $exception->setError('Forbidden');
        $exception->setErrorDescription('You are not authorized to access this page. You can only access your own customer.');

        return $exception->getException();
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

        $exception = new ApiException(403);
        $exception->setError('Forbidden');
        $exception->setErrorDescription('You are not authorized to access this page. You can only access your own customer.');

        return $exception->getException();
    }
}
