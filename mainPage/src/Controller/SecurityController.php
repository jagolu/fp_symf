<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Entity\User;

class SecurityController extends Controller
{
    public function checkBeforeRoom(){
        $email = $_POST['email'];
        $nickname = $_POST['nickname'];
        $password = $_POST['password'];
        $pattern = "/[^\w.]+/";

        $session = new Session();
        if(!$this->container->get('session')->isStarted()){
            $session->start();
        }
        else{
            $session->invalidate();
            $session->start();
        } 
        
        if(strlen($email)<5){
            $session->getFlashBag()->add('warning', 'Ha habido un problema con tu registro');
            return $this->redirectToRoute('welcome');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $session->getFlashBag()->add('warning', 'Ha habido un problema con tu registro');
            return $this->redirectToRoute('welcome');
        }
        if(preg_match($pattern, $password)==1){
            $session->getFlashBag()->add('warning', 'Ha habido un problema con tu registro');
            return $this->redirectToRoute('welcome');
        }
        if(strlen($password)<8 || strlen($password)>20){
            $session->getFlashBag()->add('warning', 'Ha habido un problema con tu registro');
            return $this->redirectToRoute('welcome');
        }
        if(preg_match($pattern, $nickname)==1){
            $session->getFlashBag()->add('warning', 'Ha habido un problema con tu registro');
            return $this->redirectToRoute('welcome');
        }
        if(strlen($nickname)<3 || strlen($nickname)>20){
            $session->getFlashBag()->add('warning', 'Ha habido un problema con tu registro');
            return $this->redirectToRoute('welcome');
        }

        $users = $this->getDoctrine()
        ->getRepository(User::class)
        ->findByEmail($email);
        if(count($users)!=0){
            $session->getFlashBag()->add('warning', 'Esa direccion de correo ya esta registrada');
            return $this->redirectToRoute('welcome');
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
            //Here I serialize the symfony session with the user data
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));
            //End of the serialize part
            return $this->render('login/chooseNewOrExistingRoom.html.twig');
        }
    }

    public function rememberPassword(){
        
    }

    public function login(){

    }

    public function index(){
        $session = new Session();
        if(!$this->container->get('session')->isStarted()){
            $session->start();
        }
        else{
            $session->invalidate();
            $session->start();
        } 
        $text = "Te has registrado con: </br>";
        $text = $text . $this->getUser()->getIdUser() .'</br>';
        $text = $text . $this->getUser()->getEmail() .'</br>';
        $text = $text . $this->getUser()->getNickname() .'</br>';
        $text = $text . $this->getUser()->getPassword() .'</br>';
        return new Response($text);
    }
}
