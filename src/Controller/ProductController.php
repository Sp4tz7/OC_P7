<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;


class ProductController extends AbstractController
{
    /**
     * @Get(
     *     path = "api/products",
     *     name = "app_products_list"
     * )
     * @View
     *     statusCode = 200,
     */
    public function getProductList(ProductRepository $productRepository)
    {
        return $productRepository->findAll();

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
