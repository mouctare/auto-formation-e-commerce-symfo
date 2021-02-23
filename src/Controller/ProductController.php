<?php

namespace App\Controller;

use App\Classes\Search;
use App\Classes\SearchType;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $entityManager;
    private $productRepository;

    public function __construct(EntityManagerInterface $entityManager, ProductRepository $productRepository)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }
    /**
     * @Route("/nos-produits", name="products")
     */
    public function index(Request $request): Response
    {
       // $products = $this->productRepository->findAll();
       
        $search = new Search();
        // Ici je transmet à mon formulaire une instanc ede al class Serach.php
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $products = $this->productRepository->findWithSearch($search);
         } else{
            $products = $this->productRepository->findAll();
        }
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     *
     * @Route("/produit/{id}", name="product")
     */
    public function show($id)
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if(!$product) {
            return $this->redirectToRoute('products');
        }
        
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
