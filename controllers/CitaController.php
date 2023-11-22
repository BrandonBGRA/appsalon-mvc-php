<?php

 namespace Controllers;

use MVC\Router;

 class CitaController {
    public static function index(Router $router){

        // Autenticar al usuario
        if (! $_SESSION){
            session_start();
            }

            isAuth();


        $router->render('cita/index', [
            'nombre'=> $_SESSION['nombre'],
            'id' => $_SESSION['id']
        ]);

        
    }
 }