<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ResetPasswordRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $entityManger;
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManger, UserRepository $userRepository)
    {
        $this->entityManger = $entityManger;
        $this->userRepository = $userRepository;
        
        
    }
    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */
    public function index(Request $request)
    {

        if($this->getUser()) {
            // Ici on dit que si l'utilisateur est déjà connecté , on l'empéche d'accéder à cette route en lui rediregean automatiquement sur la hompage
            return $this->redirectToRoute('home');
        }

        // Tu regardes dans la request si une cléé email a été envoyé
        if($request->get('email')){
        // Ensuite je regarde jen l'ai ce user en base de donnée

        $user = $this->userRepository->findOneByEmail($request->get('email'));
        // Donc si mon utilisateur existe bien voici c'est que je veux faire
        if ($user){
            // 1 : Enregistrer en base la demande de reset_password avec user, token , createdAt.
            $reset_password = new ResetPassword();
            $reset_password->setUser($user);
            $reset_password->setToken(uniqid());
            $reset_password->setCreatedAt(new \DateTime());
            $this->entityManger->persist($reset_password);
            $this->entityManger->flush();
          
            // 2 : Envoyer un email à l'utilisateur qui a fait la demande avec un lien lui permetrand de mettre à jour son mot de passe

            $url = $this->generateUrl('update_password', [
             'token' => $reset_password->getToken()
            ]);
  
            $content = "Bonjour ".$user->getFirstname()."<br/>Vous avez demandé à réinitialiser votre mot de passe .<br/><br/>";
            $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='".$url."'>mettre à jour votre mot de passe</a>.";

            $mail = new Mail();
            $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), 'Réinitialiser votre mot de passe sur La Boutique Française', $content);

            $this->addFlash('notice', 'Vous allez reçevoire dans quelques secondes un email avec la procédure pour réinitialiser votre mot de passe.');

         }else{
            $this->addFlash('notice', 'Cette adresse email est inconnue.');
         }

        }
        return $this->render('reset_password/index.html.twig');
    }

     /**
     * @Route("/modifier-mon-mot-de-passe/{token}", name="update_password")
     */
    public function update(Request $request, ResetPasswordRepository $resetPasswordRepository ,$token, UserPasswordEncoderInterface $encoder)
    {
    
        $reset_password = $resetPasswordRepository->findOneByToken($token);

        if(!$reset_password){
            return $this->redirectToRoute('reset_password');
        }
        // On a le tokjen et le createdAt
        // Maintenant on va vérifier si le createdAt = à la date de maintenant - 3h
        $now = new \DateTime();
        // On verifie si la date de mainten nt est supérieure à la date demande de reinitialisation +3
        if($now >  $reset_password->getCreatedAt()->modify('+ 3 hour')){
            $this->addFlash('notice', 'Votre demande de mot de passe a expirée. Merci de la renouveller.');
            return $this->redirectToRoute('reset_password');
        
        }
            
            // Rendre une vue avec mot de passe et confirmez votre mot de passe.
            $form = $this->createForm(ResetPasswordType::class);
            $form->handleRequest($request);

 
             if ($form->isSubmitted() && $form->isValid()) {
                 $new_pwd = $form->get('new_password')->getData();
 
              // Encodage des mots de passe 
              // On récupère le user et on lui done le nouveau mot de passe 
              $password = $encoder->encodePassword($reset_password->getUser(), $new_pwd);
               $reset_password->getUser()->setPassword($password);

             
            // Flush en base de données
            $this->entityManger->flush();
            // Redirection de la l'utilisateur vers la page de connexion
            $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour');
            return $this->redirectToRoute('app_login');

        }
        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);
      
    
       
    }
  
     
}
