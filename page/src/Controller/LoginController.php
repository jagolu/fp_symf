<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use App\Entity\User;

class LoginController extends Controller
{
    public function checkBeforeRoom($where){
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        //Comprobacion tipografia y longitud password
        //Comprobacion tipografia y longitud username
        //Comprobar que no toquen el where
        $users = $this->getDoctrine()
        ->getRepository(User::class)
        ->findByEmail($email);
        if(count($users)!=0){
            return new Response('The user already exists');
        }
        else{
            $user = new User();
            $user->setEmail($email);
            $user->setUsername($username);
            $encoder = new BCryptPasswordEncoder(15);
            $newPassword = $encoder->encodePassword($password, $user->getsalt());
            $user->setPassword($newPassword);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            if($where == 'existingRoom'){
                return new Response("You're going to join in a existing room");
            }
            else if($where == 'newRoom'){
                return new Response("You're going to create a new room");
            }
            else{
                return new Response("There has been an error, you're going back to welcome page");
            }
        }
    }
}
