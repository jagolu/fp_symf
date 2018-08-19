<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
/*  PROOF   */
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent; 
use Symfony\Component\HttpFoundation\Request;

/* END PROOF */
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

    public function rememberPassword(){
        
    }

    public function login(){

    }

    public function createNewRoom(){
        $roomName = $_POST['roomName'];
        $password = $_POST['password'];
        if(isset($_POST['liga'])) $liga = true;
        else $liga = false;
        if(isset($_POST['champions'])) $champions = true;
        else $champions = false;
        if(isset($_POST['cup'])) $cup = true;
        else $cup = false;
        
        $pattern = "/[^\w.]+/";
        $session = $this->get('session');
        if(preg_match($pattern, $password)==1){
            $session->getFlashBag()->add('warning', 'Ha habido un problema al crear la sala');
            return $this->redirectToRoute('newRoom');
        }
        if(strlen($password)<8 || strlen($password)>20){
            $session->getFlashBag()->add('warning', 'Ha habido un problema al crear la sala');
            return $this->redirectToRoute('newRoom');
        }
        if(preg_match($pattern, $roomName)==1){
            $session->getFlashBag()->add('warning', 'Ha habido un problema al crear la sala');
            return $this->redirectToRoute('newRoom');
        }
        if(strlen($roomName)<3 || strlen($roomName)>20){
            $session->getFlashBag()->add('warning', 'Ha habido un problema al crear la sala');
            return $this->redirectToRoute('newRoom');
        }
        if($liga==false && $champions==false && $cup==false){
            $session->getFlashBag()->add('warning', 'Ha habido un problema al crear la sala');
            return $this->redirectToRoute('newRoom');
        }

        if($liga==false && $champions==false && $cup==true) $type=0;
        else if($liga==false && $champions==true && $cup==false) $type=1;
        else if($liga==false && $champions==true && $cup==true) $type=2;
        else if($liga==true && $champions==false && $cup==false) $type=3;
        else if($liga==true && $champions==false && $cup==true) $type=4;
        else if($liga==true && $champions==true && $cup==false) $type=5;
        else if($liga==true && $champions==true && $cup==true) $type=6;

        $rooms = $this->getDoctrine()
        ->getRepository(Room::class)
        ->findByName($roomName);

        if(count($rooms)!=0){
            $session->getFlashBag()->add('warning', 'Ya existe una sala con ese nombre');
            return $this->redirectToRoute('welcome');
        }
        else{
            $myRoom = new Room();
            $myRoom->setType($type);
            $myRoom->setDateBegin(new \DateTime(date("Y/m/d")));
            $myRoom->setName($roomName);
            $encoder = new BCryptPasswordEncoder(15);
            $newPassword = $encoder->encodePassword($password, null);
            $myRoom->setPassword($newPassword);
            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findByIdUser($this->getUser()->getIdUser());
            $myRoom->addIdUser($this->getUser());
            $entityManager->persist($myRoom);
            $entityManager->flush();
            return $this->redirectToRoute('index');
        }
    }

    public function joinExistingRoom(){
        return new Response('joining in a existing room');
    }

    public function index(){
        $session = $this->get('session');

        $text = "Te has registrado con: </br>";
        $text = $text . $this->getUser()->getIdUser() .'</br>';
        $text = $text . $this->getUser()->getEmail() .'</br>';
        $text = $text . $this->getUser()->getNickname() .'</br>';
        $text = $text . $this->getUser()->getPassword() .'</br>';
        return new Response($text);
    }
}