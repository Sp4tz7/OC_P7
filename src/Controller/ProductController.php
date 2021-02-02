<?php

namespace App\Controller;

use App\Entity\Product;
use App\Representation\Products as Pr;
use App\Repository\ProductRepository;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\QueryParam;


class ProductController extends AbstractController
{
    /**
     * @Get(
     *     path = "api/products",
     *     name = "app_products_list"
     * )
     * @param ParamFetcherInterface $paramFetcher
     * @QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]",
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
     *     description="Max number of movies per page."
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
    public function getProductList(ParamFetcherInterface $paramFetcher, ProductRepository $productRepository)
    {
            $products = $productRepository->search(
                $paramFetcher->get('keyword'),
                $paramFetcher->get('order'),
                $paramFetcher->get('limit'),
                $paramFetcher->get('offset')
            );

        return new Pr($products);

    }

    /**
     * @Get(
     *     path = "api/products/{id}",
     *     name = "app_product_show",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     *     statusCode = 200,
     */
    public function getProduct(Product $product)
    {
        return $product;
    }
}
