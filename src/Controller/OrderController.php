<?php

namespace App\Controller;

use DateTime;
use App\Classes\Cart;
use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/commande", name="order")
     */
    public function index(Request $request, Cart $cart): Response
    {
        // Ece que toi user es - ce que tu as déjà une address
        if(!$this->getUser()->getAddresses()->getValues()){
            // Sinon je te redirige vers la page de création d'une address
            return $this->redirectToRoute('ajout_address');

        }
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
           dd($form->getData());

        }
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }
    /**
     * @Route("/commande/recapitulatif", name="order_recap", methods={"POST"})
     */
    public function add(Request $request, Cart $cart): Response
    {
       
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $date = new DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getfirstname().' '.$delivery->getLastname();
            $delivery_content .= '<br/>' .$delivery->getPhone();

            if($delivery->getCompany())
            {
                $delivery_content .= '<br/>' .$delivery->getCompany();
            }

             $delivery_content .= '<br/>' .$delivery->getAddress();
             $delivery_content .= '<br/>' .$delivery->getPostal().' '.$delivery->getCity();
             $delivery_content .= '<br/>' .$delivery->getCountry();
        
        // Enregistrer ma commande(on parle de l'entité Order)
          $order = new Order();
          $reference = $date->format('dmY').'-'.uniqid();
          $order->setReference($reference);
          $order->setUser($this->getUser());
          $order->setCreatedAt($date);
          // Ici aprés avoir récupére le nom de carriers on setCarrierNma par la varible carrier
          $order->setCarrierName($carriers->getName());
          $order->setCarrierPrice($carriers->getPrice());
          $order->setDelivery($delivery_content);
          $order->setIsPaid(0);
           

          $this->entityManager->persist($order);

          // Pour chaque produit que j'ai dans mon panier , je veux que tu l'itère et tu me fait une nouvelle entrée dans OrderDetails
           foreach ($cart->getFull() as $product) {
              
            // En fin je veux que tu fasses le lien entre OrderDetails et Order
           
               $orderDetails = new OrderDetails();
               $orderDetails->setMyOrder($order);
               $orderDetails->setProduct($product['product']->getName());
               $orderDetails->setQuantity($product['quantity']);
               $orderDetails->setPrice($product['product']->getPrice());
               $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
               // Enregister mes produits(entité OrderDetails)
               $this->entityManager->persist($orderDetails);
           }
        
             $this->entityManager->flush();

       
         

         // C'est à ce seulement si le formulaire est soumi que tu affiches la route qui va loin
         return $this->render('order/add.html.twig', [
            'cart' => $cart->getFull(),
            'carrier' =>  $carriers,
            'delivery' => $delivery_content,
            'reference' => $order->getReference()
           ]);
           

        }
        return $this->redirectToRoute('cart');

        // On deplac ele return car il disait que la variable carriers n'aitait pas defini
        // return $this->render('order/add.html.twig', [
        //     'cart' => $cart->getFull(),
        //     'carrier' =>  $carriers,
        //     'delivery' => $delivery_content
        //  ]);
    }
}
