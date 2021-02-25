<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderCancelController extends AbstractController
{
    private $orderRepository ;
    private $entityManger ;

    public function  __construct(OrderRepository $orderRepository, EntityManagerInterface $entityManager)
    {
        $this->orderRepository  = $orderRepository ;
        $this->entityManger  = $entityManager ;
    }
    
    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="order_cancel")
     */
    public function index($stripeSessionId)
    {
        $order = $this->orderRepository->findOneByStripeSessionId($stripeSessionId);
        
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        // Envoyer un email à l'utilisateur pour lui indiquer ll'échec du payement
        return $this->render('order_cancel/index.html.twig', [
           'order' => $order
        ]);
    }
}
