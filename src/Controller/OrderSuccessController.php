<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{
    private $orderRepository ;
    private $entityManger ;

    public function  __construct(OrderRepository $orderRepository, EntityManagerInterface $entityManager)
    {
        $this->orderRepository  = $orderRepository ;
        $this->entityManger  = $entityManager ;
    }
    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->orderRepository->findOneByStripeSessionId($stripeSessionId);
        
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }
       
        if(!$order->getIsPaid()) {
                // Vider la session "cart
                $cart->remove();
               // Modifier  le staut isPaid de notre commande
               $order->setIsPaid(1);
               $this->entityManger->flush();
            // Envoyer un email à notre client pour lui informer

        }
     
        // Afficher les quelques infos de la commmande




        return $this->render('order_success/index.html.twig', [
            'order' => $order
          
        ]);
    }
}
