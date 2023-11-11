<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

class ShopController extends AbstractController
{
    #[Route('/profile/shop', name: 'app_shop')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('shop/index.html.twig', [
            'products'=> $products,
        ]);
    }
}
