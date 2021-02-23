<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddressController extends AbstractController
{
    private $entityManager;
    

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
       
    }
    /**
     * @Route("/compte/address", name="address_all")
     */
    public function index()
    {
        
        return $this->render('address/index.html.twig', [
           
        ]);
    }

    /**
     * @Route("/compte/ajouter-une-adresse", name="ajout_address")
     */
    public function addAdress(Cart $cart, Request $request)
    {
        $address = new Address();
        
         $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid()){
             // Ici on lie l'adresse à l'utilisateur connecté
             $address->setUser($this->getUser());
             $this->entityManager->persist($address);
             $this->entityManager->flush();

             if ($cart->get()) {
                // S j'ai de sproduits dans mon panier en ce moment là tu me redirige vers la commande
                return  $this->redirectToRoute('order');
            } else {
                // Si dans les adresses
                return  $this->redirectToRoute('address_all');

             }
           
            }

        return $this->render('address/address.html.twig', [
           'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/modifier-une-adresse/{id}", name="address_edit")
     */
    public function edit(Request $request, $id)
    {
     $address = $this->entityManager->getRepository(Address::class)->find($id);
       // Une importante vérification à metriser
       if(!$address || $address->getUser() != $this->getUser()){
           return $this->redirectToRoute('ajout_address');

       }
        
         $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid()){
             $this->entityManager->flush();
            return  $this->redirectToRoute('address_all');
            }

        return $this->render('address/address.html.twig', [
           'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/supprimer-une-adresse/{id}", name="delete_address")
     */
    public function delete( $id)
    {
     $address = $this->entityManager->getRepository(Address::class)->find($id);
       // Une importante vérification à metriser
       if($address &&  $address->getUser() == $this->getUser()){
        $this->entityManager->remove($address);
        $this->entityManager->flush();
       }
       
            return  $this->redirectToRoute('address_all');
           
        

     
    }
}
