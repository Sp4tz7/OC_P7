<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Exception\ApiForbiddenException;
use App\Exception\ApiNotFoundException;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

class CustomerController extends AbstractController
{
    /**
     * @SWG\Tag(name="Customers")
     * @SWG\Response(
     *     response=200,
     *     description="Returns all customers",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Customer::class))
     *     )
     * )
     * @Get(
     *     path = "api/customers",
     *     name = "app_customers_list"
     * )
     * @View
     *     statusCode = 200,
     */
    public function getCustomerList(CustomerRepository $customerRepository, AdapterInterface $cache)
    {
        $customerList = $customerRepository->findBy(['retailer' => $this->getUser()]);
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            $customerList = $customerRepository->findAll();
        }

        $item = $cache->getItem('products_'.md5(serialize($customerList)));
        if(!$item->isHit()){
            $item->set( $customerList);
            $cache->save($item);
        }

        return $item->get();
    }

    /**
     * @SWG\Tag(name="Customers")
     * @SWG\Response(
     *     response=200,
     *     description="Returns details of a given customer id",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Customer::class))
     *     )
     * )
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
     * @SWG\Tag(name="Customers")
     * @SWG\Response(
     *     response=200,
     *     description="Delete a customer",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Customer::class))
     *     )
     * )
     * @Delete(
     *     path = "api/customers/{id}",
     *     name = "app_customer_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     *     statusCode = 204,
     */
    public function deleteCustomer(Customer $customer)
    {
        if ($customer->getRetailer() == $this->getUser()){
            $em = $this->getDoctrine()->getManager();
            $em->remove($customer);
            $em->flush();
            return ;
        }

        throw new ApiForbiddenException('You are not authorized !!! to access this page. You can only delete your own customer.', 403);
    }
}
