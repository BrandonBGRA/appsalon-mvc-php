<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {

    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;   
    }

    public function enviarConfirmacion() {

        // Crear el objeto de email
        $mail = new PHPMailer();
                    // Configurar SMTP (protocolo de envio de emails)
        $mail->isSMTP(); // Le colocamos el domio
        $mail->Host = $_ENV['EMAIL_HOST']; // El hosto
        $mail->SMTPAuth = true; // Le decimos que nos vamos a autenticar
        $mail->Username = $_ENV['EMAIL_USER']; 
        $mail->Password = $_ENV['EMAIL_PASS'];
        $mail->SMTPSecure = 'tls'; //Para que sean emails seguros
        $mail->Port = $_ENV['EMAIL_PORT']; // Puerto al que se va a conectar

        // Configurar el contenido que tendra el email
        $mail->setFrom('cuentas@appsalon.com'); // Quien envia el email
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com'); //A donde va a llegar el correo
        $mail->Subject = 'Confirmar tu cuenta';

        // Habilitar HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8'; // Aceptar caracteres especiales

        $contenido = '<html>';
        
        $contenido .= "<p> <strong> Hola " . $this->email . " </strong> Has creado tu cuenta en App Salon, solo debes confirmarla presionando el siguiente enlace </p>";
        $contenido .= " <p> Presiona aquí: <a href='" . $_ENV['APP_URL']  . "/confirmar-cuenta?token=" . $this->token . "'> Confirmar Cuenta </a> </p> ";
        $contenido .= "<p> Si tu no solicitaste esta cuenta puedes ignorar el mensaje </p>";
        $contenido .= "</html>";
        $mail->Body = $contenido;
        
        

        // Enviar el email
        $mail->send();
    }

    public function enviarInstrucciones() {
        
        // Crear el objeto de email
        $mail = new PHPMailer();
                    // Configurar SMTP (protocolo de envio de emails)
        $mail->isSMTP(); // Le colocamos el domio
        $mail->Host = $_ENV['EMAIL_HOST']; // El hosto
        $mail->SMTPAuth = true; // Le decimos que nos vamos a autenticar
        $mail->Username = $_ENV['EMAIL_USER']; 
        $mail->Password = $_ENV['EMAIL_PASS'];
        $mail->SMTPSecure = 'tls'; //Para que sean emails seguros
        $mail->Port = $_ENV['EMAIL_PORT']; // Puerto al que se va a conectar

        // Configurar el contenido que tendra el email
        $mail->setFrom('cuentas@appsalon.com'); // Quien envia el email
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com'); //A donde va a llegar el correo
        $mail->Subject = 'Restablece tu Password';

        // Habilitar HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8'; // Aceptar caracteres especiales

        $contenido = '<html>';
        
        $contenido .= "<p> <strong> Hola " . $this->nombre . " </strong> Has solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
        $contenido .= " <p> Presiona aquí: <a href='" . $_ENV['APP_URL']  . "/recuperar?token=" . $this->token . "'> Reestablecer Password </a> </p> ";
        $contenido .= "<p> Si tu no solicitaste esta cuenta puedes ignorar el mensaje </p>";
        $contenido .= "</html>";
        $mail->Body = $contenido;
        
        

        // Enviar el email
        $mail->send();
    }
}