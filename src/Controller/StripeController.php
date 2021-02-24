<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


    class StripeController extends AbstractController
    {
      
        /**
         * @Route("/commande/create-session/{reference}", name="stripe_create_session")
         */
        public function index(EntityManagerInterface $entityManager, $reference, Cart $cart)
        {
    
            $product_for_stripe = [];
            $YOUR_DOMAIN = "https://127.0.0.1:8000";
    
            $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);
             if(!$order){
            new JsonResponse(['error' => 'order']);
            }
            
            foreach ($order->getOrderDetails()->getValues() as $product){
                $product_object = $entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
                $product_for_stripe[] = [
                    'price_data' => [
                   'currency' => 'eur',
                   'unit_amount' => $product->getPrice()* 100,
                   'product_data' => [
                     'name' => $product->getProduct(),
                     'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()],
                   ],
                 ],
                 'quantity' => $product->getQuantity(),
               ];
         }
    
    
         
        
         $product_for_stripe[] = [
             'price_data' => [
            'currency' => 'eur',
            'unit_amount' => $order->getCarrierPrice() * 100,
            'product_data' => [
              'name' => $order->getCarrierName(),
              'images' => [$YOUR_DOMAIN],
            ],
          ],
          'quantity' => 1,
        ];
    
    
            
            Stripe::setApiKey('sk_test_4eC39HqLyjWDarjtT1zdp7dc');
    
             $checkout_session = Session::create([
               'customer_email' => $this->getUser()->getEmail(),
                  'payment_method_types' => ['card'],
                  'line_items' => [
                      // Ici c'est le tableau qui contient la commande
                      $product_for_stripe
                  ],
                  'mode' => 'payment',
                  'success_url' => $YOUR_DOMAIN . '/success.html',
                  'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
                ]);
    
                $response = new JsonResponse(['id' => $checkout_session->id]);
    
                return $response;
        }
    }