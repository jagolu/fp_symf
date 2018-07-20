<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\User;

class LoginController extends Controller
{
    public function checkBeforeRoom($where){
        $email = $_POST['email'];
        $nickname = $_POST['nickname'];
        $password = $_POST['password'];

        $session = new Session();
        $session->start();
        $pattern = "/[^\w.]+/";
        
        if(strlen($email)<5){
            $session->getFlashBag()->add('warning', 'Ha habido un problema con tu registro');
            return $this->redirectToRoute('login');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $session->getFlashBag()->add('warning', 'Ha habido un problema con tu registro');
            return $this->redirectToRoute('login');
        }
        if(preg_match($pattern, $password)==1){
            $session->getFlashBag()->add('warning', 'Ha habido un problema con tu registro');
            return $this->redirectToRoute('login');
        }
        if(strlen($password)<8 || strlen($password)>20){
            $session->getFlashBag()->add('warning', 'Ha habido un problema con tu registro');
            return $this->redirectToRoute('login');
        }
        if(preg_match($pattern, $nickname)==1){
            $session->getFlashBag()->add('warning', 'Ha habido un problema con tu registro');
            return $this->redirectToRoute('login');
        }
        if(strlen($nickname)<3 || strlen($nickname)>20){
            $session->getFlashBag()->add('warning', 'Ha habido un problema con tu registro');
            return $this->redirectToRoute('login');
        }
        if($where != 'newRoom' && $where != 'existingRoom'){
            $session->getFlashBag()->add('warning', 'Ha habido un problema con tu registro');
            return $this->redirectToRoute('login');
        }

        $users = $this->getDoctrine()
        ->getRepository(User::class)
        ->findByEmail($email);
        if(count($users)!=0){
            $session->getFlashBag()->add('warning', 'Esa direccion de correo ya esta registrada');
            return $this->redirectToRoute('login');
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
            $session->getFlashBag()->add('success', 'Has completado tu registro con exito');
            return $this->redirectToRoute('login');
        }
    }

    public function login_check(){

    }
}
