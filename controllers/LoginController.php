<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $auth = new Usuario($_POST);

            

            $alertas = $auth->validarLogin();

            if (empty($alertas)) { // Si esta vacio es que el usuario ingreso email y password
                // Comprobar que el usuario exista
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario) {
                    // Verificar el password
                    if( $usuario->comprobarPasswordAndVerificado($auth->password) ) {
                        // Autenticar al usuario
                       if (!isset($_SESSION)) {
                            session_start();
                       }
                        
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionamiento
                        if ($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;

                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }
                    }
                    
                } else {
                    Usuario::setAlerta('error','Usuario no encontrado');
                }
            } 
        }
  
        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }
    public static function logout() {
        if (! $_SESSION){
            session_start();
            }

        $_SESSION = [];

        header('Location: /');
    }
    public static function olvide(Router $router) {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();
            
            if (empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario && $usuario->confirmado  === "1") {
                    // Generar un token para cambio de contraseña
                    $usuario->crearToken();
                    $usuario->guardar();
                    
                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    Usuario::setAlerta('exito', 'Revisa tu email'); // Insertar Alertas   
                } else {
                    Usuario::setAlerta('error','El Usuario no existe o no esta confirmado'); // Insertar
                } 
            }
        }
        $alertas = Usuario::getAlertas(); // Obtener alertas
        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }
    public static function recuperar(Router $router) {
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);
        
        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token No Valido');
            $error = true;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Leer el nuevo password y guardarlo

            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if (empty($alertas)) {
                $usuario->password = null; // Limpiamos el password
                $usuario->password = $password->password; // Insertamos el nuevo password
                $usuario->hashPassword(); // Hasheamos el nuevo password
                $usuario->token = null; // Eliminamos el token

                $resultado = $usuario->guardar();

                if ($resultado) {
                    header('Location: /');
                }
            }
            
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas'=> $alertas,
            'error'=> $error
         ]);
    }
    public static function crear(Router $router) {

        $usuario = new Usuario;

        // Alertas Vacias
        $alertas = []; // Cuando se visite el sitio por primera vez estara vacio y se recorrera al llenar el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();  

            // Revisar que alertas este vacio
            if (empty($alertas)) {
                // Verificar que el usuario no este registrado
               $resultado = $usuario->existeUsuario();

               // Validacion de usuario si existe o no existe
               if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
               } else {
                    // Hashear el password

                    $usuario->hashPassword();

                    // Generar Token único
                    $usuario->crearToken();

                    // Enviar el Email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);

                    $email->enviarConfirmacion();  

                    // Crear Usuario
                    $resultado = $usuario->guardar();
                    

                    if ($resultado) {
                        header('Location: /mensaje');
                    }
             
            
               }
            }
        }

        $router->render('auth/crear-cuenta', [
           'usuario' => $usuario,
           'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router) {

        $router->render('auth/mensaje');
    }

    public static function confirmar (Router $router) {
        $alertas = [];

        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            //Mostrar el mensaje de error
            Usuario::setAlerta('error', 'Token No Válido');
        } else {
             // Modificar a usuario confirmado
             $usuario->confirmado = "1";
             $usuario->token = null;
             $usuario->guardar();
             Usuario::setAlerta('exito', 'Cuenta comprobada correctamente');
        }
        // Actualizar las alertas, obteniendo las de arriba
        $alertas =Usuario::getAlertas();
        // renderizar la vista
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }

}
