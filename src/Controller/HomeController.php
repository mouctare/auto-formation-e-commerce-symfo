<?php

namespace App\Controller;

use App\Classes\Mail;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
      $mail = new Mail();
      $mail->send('mouctard78@gmail.com', "Diallo", 'Mon premier email', "Bonjour Mr diallo , j'espère que vous allez bien");
        return $this->render('home/index.html.twig');
           
    }
}
