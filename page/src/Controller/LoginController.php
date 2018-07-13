<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;

class LoginController extends Controller
{
    public function checkBeforeRoom($where){
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $where = $_GET['where'];
        return new Response($where);
        //Comprobacion tipografia y longitud password
        //Comprobacion tipografia y longitud username
        //Comprobacion de si existe el usuario
        /*$response = $this->forward('App\Controller\OtherController::fancy', array(
            'name'  => $name,
            'color' => 'green',
        ));*/
    }
}
