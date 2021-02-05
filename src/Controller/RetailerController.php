<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Retailer;
use App\Exception\ApiForbiddenException;
use App\Repository\RetailerRepository;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use PhpParser\Node\Expr\Throw_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;
use App\Exception\ApiValidationException;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;


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
     * @SWG\Tag(name="Retailers")
     * @SWG\Response(
     *     response=200,
     *     description="Returns all retailers",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Retailer::class))
     *     )
     * )
     * @Get(
     *     path = "api/retailers",
     *     name = "app_retailers_list"
     * )
     * @View
     *     statusCode = 200,
     */
    public function getRetailerList(RetailerRepository $retailerRepository, AdapterInterface $cache)
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return new Response('Your are not allowed to see this page', 403);
        }


        $retailers = $retailerRepository->findAll();
        $retailerList = $this->serializer->serialize($retailers, 'json', SerializationContext::create()->setGroups(['list']));

        $response = new Response($retailerList);
        $response->headers->set('Content-type', 'application/json');

        $item = $cache->getItem('products_'.md5($response));
        if(!$item->isHit()){
            $item->set( $response);
            $cache->save($item);
        }


        return $item->get();
    }

    /**
     * @SWG\Tag(name="Retailers")
     * @SWG\Response(
     *     response=200,
     *     description="Returns details of a given retailer id",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Retailer::class))
     *     )
     * )
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
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $retailer;
        }

        $message = sprintf('You are not authorized to access this page. You can only access your own retailer account. %s',
            $this->generateUrl('app_retailer_show', ['id' => $this->getUser()->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL));

        throw new ApiForbiddenException($message, 403);
    }

    /**
     * @SWG\Tag(name="Customers")
     * @SWG\Response(
     *     response=200,
     *     description="Returns all customers for a given retailer id",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Retailer::class))
     *     )
     * )
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

        $message = sprintf('You are not authorized to access this page. You can only access your own customers. %s',
            $this->generateUrl('app_retailer_show_customers', ['id' => $this->getUser()->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL));

        throw new ApiForbiddenException($message, 403);

    }

    /**
     * @SWG\Tag(name="Customers")
     * @SWG\Response(
     *     response=201,
     *     description="Returns details of a new retailer",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Retailer::class))
     *     )
     * )
     * @Post(
     *     path = "api/retailers/{id}/customer",
     *     name = "app_retailer_add_customer",
     *     requirements = {"id"="\d+"}
     * )
     * @ParamConverter("customer", converter="fos_rest.request_body")
     * @View(
     *     statusCode = 201,
     * )
     */
    public function createRetailerCustomers(Customer $customer, Retailer $retailer, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'Invalid resource data: ';
            foreach ($violations as $violation) {
                $message .= sprintf('Field %s: %s', $violation->getPropertyPath(), $violation->getMessage());
            }
            throw new ApiValidationException($message, 400);
        }

        if ($retailer === $this->getUser() or in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $customer->setRetailer($retailer);
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();

            return $this->view($customer, Response::HTTP_CREATED, [
                'Location' => $this->generateUrl('app_retailer_show_customers', ['id' => $customer->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
        }

        $message = sprintf('You are not authorized to access this page. You can only add customer to your own account. %s',
            $this->generateUrl('app_retailer_add_customer', ['id' => $this->getUser()->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL));

        throw new ApiForbiddenException($message, 403);

    }

    /**
     * @SWG\Tag(name="Products")
     * @SWG\Response(
     *     response=200,
     *     description="Returns products list of a given retailer id",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Retailer::class))
     *     )
     * )
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
        Request $request
    ) {
        $token = $request->headers->get('BILEMO-AUTH-TOKEN');
        if ($retailer->getApiToken() === $token) {
            return $retailer->getProducts();
        }

        $message = sprintf('You are not authorized to access this page. You can only access your own products. %s',
            $this->generateUrl('app_retailer_show_products', ['id' => $this->getUser()->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL));

        throw new ApiForbiddenException($message, 403);
    }

}
