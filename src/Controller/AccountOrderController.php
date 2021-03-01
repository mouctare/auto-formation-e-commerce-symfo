<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountOrderController extends AbstractController
{
 private $orderRepository;

 public function __construct(OrderRepository  $orderRepository)
 {
     $this->orderRepository = $orderRepository;
     
 }


    /**
     * @Route("/comptes/mes-commande", name="account_order")
     */
    public function index(): Response
    {
        $orders = $this->orderRepository->findSuccessOrders($this->getUser());
        //dd($orders);
        return $this->render('account/order.html.twig', [
            'orders' => $orders
            
        ]);
    }

    /**
     * @Route("/comptes/mes-commande/{reference}", name="account_order_show")
     */
    public function show($reference): Response
    {
        $order = $this->orderRepository->findOneByReference($reference);

     
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_order');
        }

        
        return $this->render('account/order_show.html.twig', [
            'order' => $order
            
        ]);
    }
}
