<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Representation\Customers as Cu;
use App\Exception\ApiForbiddenException;
use App\Exception\ApiNotFoundException;
use App\Repository\CustomerRepository;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\QueryParam;
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
     * @param ParamFetcherInterface $paramFetcher
     * @QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]+",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="15",
     *     description="Max number of customers per page."
     * )
     * @QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="1",
     *     description="The pagination offset"
     * )
     * @View
     *     statusCode = 200,
     */
    public function getCustomerList(ParamFetcherInterface $paramFetcher, CustomerRepository $customerRepository, AdapterInterface $cache)
    {

        $retailer = null;
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            $retailer = $this->getUser()->getId();
        }

        $customers = $customerRepository->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset'),
            $retailer
        );

        $data = new Cu($customers);

        $item = $cache->getItem('products_'.md5(json_encode($data->meta)));
        if(!$item->isHit()){
            $item->set($data);
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
     * @throws ApiForbiddenException
     */
    public function getCustomer(Customer $customer): Customer
    {
        if ($customer->getRetailer() == $this->getUser() or in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ){

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
        if ($customer->getRetailer() == $this->getUser() or in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            $em = $this->getDoctrine()->getManager();
            $em->remove($customer);
            $em->flush();
            return ;
        }

        throw new ApiForbiddenException('You are not authorized !!! to access this page. You can only delete your own customer.', 403);
    }
}
