<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Retailer;
use App\Repository\RetailerRepository;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;
use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class RetailerController extends AbstractFOSRestController
{

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;

    }

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
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            return new Response('Your are not allowed to see this page', 203);
        }


        $retailers = $retailerRepository->findAll();
        $data = $this->serializer->serialize($retailers, 'json', SerializationContext::create()->setGroups(['list']));

        $response = new Response($data);
        $response->headers->set('Content-type', 'application/json');

        return $response;
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
    public function getRetailerDetail(Retailer $retailer, Request $request)
    {
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            return $retailer;
        }

        $exception = new ApiException(403);
        $exception->setError('Forbidden');
        $exception->setErrorDescription('You are not authorized to access this page. You can only access your own retailer account.');
        $exception->setErrorUrl($this->generateUrl('app_retailer_show', ['id' => $this->getUser()->getId()], UrlGeneratorInterface::ABSOLUTE_URL));

        return $exception->getException();
    }

    /**
     * @Get(
     *     path = "api/retailers/{id}/customers",
     *     name = "app_retailer_show_customers",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     *     statusCode = 200,
     */
    public function getRetailerCustomers(Retailer $retailer)
    {
        if ($retailer === $this->getUser() or in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {

            return $retailer->getCustomers();
        }

        $exception = new ApiException(403);
        $exception->setError('Forbidden');
        $exception->setErrorDescription('You are not authorized to access this page. You can only access your own customers.');
        $exception->setErrorUrl($this->generateUrl('app_retailer_show_customers', ['id' => $this->getUser()->getId()], UrlGeneratorInterface::ABSOLUTE_URL));

        return $exception->getException();
    }

    /**
     * @Post(
     *     path = "api/retailers/{id}/customer",
     *     name = "app_retailer_add_customer",
     *     requirements = {"id"="\d+"}
     * )
     * @ParamConverter("customer", converter="fos_rest.request_body")
     * @View
     *     statusCode = 201,
     */
    public function createRetailerCustomers(Customer $customer, Retailer $retailer, Request $request, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            return $this->view($violations, Response::HTTP_BAD_REQUEST);
        }

        if ($retailer === $this->getUser() or in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $customer->setRetailer($retailer);
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();
            return $this->view($customer, Response::HTTP_CREATED, [
                'Location' => $this->generateUrl('app_retailer_show_customers', ['id' => $customer->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);
        }

        $exception = new ApiException(403);
        $exception->setError('Forbidden');
        $exception->setErrorDescription('You are not authorized to access this page. You can only add customer to your own account.');
        $exception->setErrorUrl($this->generateUrl('app_retailer_add_customer', ['id' => $this->getUser()->getId()], UrlGeneratorInterface::ABSOLUTE_URL));

        return $exception->getException();
    }

    /**
     * @Get(
     *     path = "api/retailers/{id}/products",
     *     name = "app_retailer_show_products",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     *     statusCode = 200,
     */
    public function getRetailerProducts(
        Retailer $retailer,
        Request $request,
        RetailerRepository $retailerRepository
    ) {
        $token = $request->headers->get('BILEMO-AUTH-TOKEN');
        if ($retailer->getApiToken() === $token) {
            return $retailer->getProducts();
        }

        $retailer = $retailerRepository->findOneBy(['apiToken' => $token]);

        $exception = new ApiException(403);
        $exception->setError('Forbidden');
        $exception->setErrorDescription('You are not authorized to access this page. You can only access your own products.');
        $exception->setErrorUrl(sprintf('%s/api/retailers/%d/products', $request->getSchemeAndHttpHost(),
            $retailer->getId()));

        return $exception->getException();
    }

}
