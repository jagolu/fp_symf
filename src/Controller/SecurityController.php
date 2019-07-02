<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent; 
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Room;

class SecurityController extends Controller
{
    public function checkBeforeRoom(Request $request){
        $email = $_POST['email'];
        $nickname = $_POST['nickname'];
        $password = $_POST['password'];
        $pattern = "/[^\w.]+/";

        $session = $this->get('session');
        
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
            $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles()); //main is the name of the firewall in your security.yml 
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));
            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
            return $this->render('login/chooseNewOrExistingRoom.html.twig');
        }
    }
}