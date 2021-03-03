<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Mail;
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
       
        if($order->getState() === 0) {
                // Vider la session "cart
                $cart->remove();
               // Modifier  le staut isPaid de notre commande
               $order->setState(1);
               $this->entityManger->flush();
            // Envoyer un email à notre client pour lui informer
            $mail = new Mail();
            $content = "Bonjour ".$order->getUser()->getFirstname()."<br/>Merci pour votre commande.<br><br/>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aperiam expedita fugiat ipsa magnam mollitia optio voluptas! Alias, aliquid dicta ducimus exercitationem facilis, incidunt magni, minus natus nihil odio quos sunt?";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Votre commande est bien validée', $content);


        }
     
        // Afficher les quelques infos de la commmande




        return $this->render('order_success/index.html.twig', [
            'order' => $order
          
        ]);
    }
}
