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
        $nickname = $_POST['nickname'];
        $password = $_POST['password'];
        
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new Response("Your email is not valid");
        }
        //Comprobacion tipografia password
        if(strlen($password)<8 || strlen($password)>20){
            return new Response("There has been an error with the length of the password");
        }
        //Comprobacion tipografia username
        if(strlen($nickname)<3 || strlen($nickname)>20){
            return new Response("There has been an error with the length of the nickname");
        }
        if($where != 'newRoom' && $where != 'existingRoom'){
            return new Response("There has been an error with the value of the parameter 'where'".$where);
        }



        $users = $this->getDoctrine()
        ->getRepository(User::class)
        ->findByEmail($email);
        if(count($users)!=0){
            return new Response('The user already exists');
        }
        else{
            $user = new User();
            $user->setEmail($email);
            $user->setNickname($nickname);
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
