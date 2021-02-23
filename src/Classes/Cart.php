<?php
namespace App\Classes;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class Cart
{
    private $session;
    private $entityManger;

    public function __construct(EntityManagerInterface $entityManger, SessionInterface $session)
    {
        $this->session = $session;
        $this->entityManger = $entityManger;
    }

    public function add($id)
    {
        // Mise en place du panier
        $cart = $this->session->get('cart', []);
        // Si tu as bien dans ton panier un produit d'id x déjà inséré 

        if(!empty($cart[$id])) {
        // Alors, tu m'ajoute la quantité
            $cart[$id]++;

        }else{
            $cart[$id] = 1; 
        }
        
      $this->session->set('cart', $cart);
          
    }

    public function get()
    {
        return $this->session->get('cart');
    }

    public function remove()
    {
        return $this->session->remove('cart');
    }

    public function delete($id)
    {
        $cart = $this->session->get('cart', []);
        
        unset($cart[$id]);
   // Remetre le panier à jour àprés la suppression
        return $this->session->set('cart', $cart);
    }

    public function decrease($id)
    {
        $cart = $this->session->get('cart', []);
        // Vérifier si la quantité est supérieur à 1
        if ($cart[$id] > 1){
            $cart[$id]--;
        }else{

        unset($cart[$id]);

        }
 
        return $this->session->set('cart', $cart);
    }

    public function getFull()
    {

        $cartComplete = [];
      
        if ($this->get()) {
          foreach($this->get() as $id => $quantity){
              $product_object = $this->entityManger->getRepository(Product::class)->find($id);
              if (!$product_object){
                  $this->delete($id);
                  continue;
              }
              $cartComplete[] = [
                  'product' => $product_object ,
                  'quantity' => $quantity
              ];
           }
         }
         return $cartComplete;
    }

}