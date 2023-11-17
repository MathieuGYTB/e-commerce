<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\ProductRepository;
class CartService {

  protected $requestStack;
  protected $productRepository;

  public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
  {
    $this->requestStack = $requestStack;
    $this->productRepository = $productRepository;
  }
  public function add(int $id) 
  {
  
    $cart = $this->requestStack->getSession()->get("cart", []);
  
    if(!empty($cart[$id])) 
    {
      $cart[$id]++;
    } else {
      $cart[$id] = 1;
    }

    $this->requestStack->getSession()->set("cart", $cart);
  }

  public function addFormation(int $id)
  {
    
    $cart = $this->requestStack->getSession()->get("cart", []);

    if(empty($cart[$id])) {
      $cart[$id] = 1;
    } 

    $this->requestStack->getSession()->set("cart", $cart);
  }
  public function less(int $id)
  {
    $cart = $this->requestStack->getSession()->get("cart", []);

    if(!empty($cart[$id])) 
    {
      if($cart[$id] > 1) {
        $cart[$id]--;
      } else if ($cart[$id] <= 1)
        unset( $cart[$id]);
    }

    $this->requestStack->getSession()->set("cart", $cart);
  }
  public function remove(int $id)
  {
    $cart = $this->requestStack->getSession()->get("cart", []);

    if(!empty($cart[$id])) 
    {
      unset($cart[$id]);
    }

    $this->requestStack->getSession()->set("cart", $cart);
  }

  public function getFullCart(): array
  {
    $cart = $this->requestStack->getSession()->get("cart", []);

    $cartWithData = [];

    foreach($cart as $id => $qty)
    {
      $cartWithData[] = [
        'product' => $this->productRepository->find($id),
        'quantity' => $qty
      ];
    }

    return $cartWithData;
  }

  public function getTotal(): float
  {
    $total = 0;

    foreach($this->getFullCart() as $item)
    {
      $total += $item['product']->getPrice() * $item['quantity'];
    }

    return $total;
  }

}