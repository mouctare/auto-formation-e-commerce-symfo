<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
  //private $entityManager;
  private $productRepository;


  public function __construct(ProductRepository $productRepository,  EntityManagerInterface $entityManager)
  {
     // $this->entityManager = $entityManager;
      $this->productRepository = $productRepository;
  }

    /**
     * @Route("/", name="home")
     */
    public function index()
    {
     
      /*   $mail = new Mail();
      $mail->send('mouctard78@gmail.com', "Diallo", 'Mon premier email', "Bonjour Mr diallo , j'espère que vous allez bien"); */
      //$product = $this->entityManager->getRepository(Product::class)->findByIsBest();
      // You need to pass a parameter to 'findByIsBest' cette se règle en passant 1 à la function findBy vu que c'est un boolean
      $products = $this->productRepository->findByIsBest(1);
      

        return $this->render('home/index.html.twig', [
          'products' => $products
        ]);
           
    }
}
