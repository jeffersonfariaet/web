<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'administrator/includes/functions.php';

require 'administrator/includes/phpmailer/Exception.php';
require 'administrator/includes/phpmailer/PHPMailer.php';
require 'administrator/includes/phpmailer/SMTP.php';

// Función para que no se ejecute el archivo directamente
if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
}

//-----------------------------------------
if (isset($_POST['process']))
{
//-----------------------------------------

// Conexion a base de datos
$conexion = Conexion::singleton_conexion();

// XSS
$xss = new xss_filter();

// Variable Process
$process = $_POST['process'];

// Listado de procesos
/*

- Login
- Register
- Forgot
- Image Uploader
- Change Dates

*/


/* Login  */
if ($process == 1) 
{

   $nuevoSingleton = Login::singleton_login();
 
   $email = $_POST['email'];
   $password = $_POST['password'];
   //accedemos al método usuarios y los mostramos
   $usuario = $nuevoSingleton->login_users($email,$password);
    
   if($usuario == TRUE)
   {
       echo 1;
   }
   if($usuario == FALSE)
   {
       echo 2;
   }


}


/* Registro */
elseif ($process == 2)
{

  $enable = checkModActivePage(2);
  if ($enable == 1) {
    exit();
  }

  if (!isset($_SESSION['newid'])) 
  {
    exit();
  }
  
  $nombre = $xss->filter_it($_POST['nombre']);
  $apellido = $xss->filter_it($_POST['apellido']);
  
  if (empty($nombre) || ctype_space($nombre) || is_null($nombre)) {
    echo 1;
    exit();
  }

  if (empty($apellido) || ctype_space($apellido) || is_null($apellido)) {
    echo 1;
    exit();
  }

  $checkmail = checkMail($_POST['email']);

  if ($checkmail == 1) 
  {

  $crypt = sha1(PASSTK.$_POST['password']);
  $referal = sha1(SESSTK.$_POST['email']);
  $rank = 1;
  $activeu = 2;
  $registerdatadate = date('Y-m-d');
  $skeydata = 0;
    
  $SQL = 'INSERT INTO usuarios(id,nombre, apellido, email, password, rank, activo, referal,pais,registro,skey, padre) VALUES (:id, :nombre, :apellido, :email, :password, :rank, :activo, :referal, :pais, :registro, :skey, :padre)';
  $stn = $conexion -> prepare($SQL);
  $stn -> bindParam(':id', $_SESSION['newid'] ,PDO::PARAM_INT);
  $stn -> bindParam(':nombre', $nombre ,PDO::PARAM_STR);
  $stn -> bindParam(':apellido', $apellido ,PDO::PARAM_STR);
  $stn -> bindParam(':email',$_POST['email'],PDO::PARAM_STR);
  $stn -> bindParam(':password',$crypt,PDO::PARAM_STR);
  $stn -> bindParam(':rank',$rank,PDO::PARAM_INT);
  $stn -> bindParam(':activo',$activeu,PDO::PARAM_INT);
  $stn -> bindParam(':referal',$referal,PDO::PARAM_INT);
  $stn -> bindParam(':pais',$_POST['pais'],PDO::PARAM_INT);
  $stn -> bindParam(':registro',$registerdatadate,PDO::PARAM_INT);
  $stn -> bindParam(':skey', $skeydata,PDO::PARAM_INT); 
  $stn -> bindParam(':padre', $_SESSION['referaluser'],PDO::PARAM_INT); 
  $stn -> execute();

  sumReferalNumber($_SESSION['referaluser']);

  session_start();
  unset($_SESSION['newid']);
  unset($_SESSION['referaluser']);
  session_destroy();

  echo 1;

  }
  else
  {

   echo 2;

  }


}


// Subida de Fotos
elseif ($process == 3)
{

// Checamos si la sesion existe.
IsLoginProcess();


$enable = checkModActivePage(4);
if ($enable == 1){
   exit();
}


//comprobamos si existe un directorio para subir el archivo
//si no es así, lo creamos
if(!is_dir("images/photos/")) 
mkdir("images/photos/", 0777);


//comprobamos si existe un directorio para subir el archivot emporal
//si no es así, lo creamos
if(!is_dir("images/photos/tmp")) 
mkdir("images/photos/tmp", 0777);

    
// creamos directorio para el usuario
if(!is_dir("images/photos/".$_SESSION['usuario'])) 
mkdir("images/photos/".$_SESSION['usuario'], 0777);      


//obtenemos el archivo a subir
$file = $_FILES['file']['name'];

// Obtenemos la extension
$fileext = new SplFileInfo($file);
$getextension = $fileext->getExtension();


// convertimos extension a minusculas
$extension = strtolower($getextension);

// Verificamos si el archivo que se sube es valido
if (strcmp($extension, 'jpg') == 0){
    processfilepostpicperfil($file,$_SESSION['usuario']);
    return;
}else if (strcmp($extension, 'jpeg') == 0){
    processfilepostpicperfil($file,$_SESSION['usuario']);
    return;
}else if (strcmp($extension, 'png') == 0){
    processfilepostpicperfil($file,$_SESSION['usuario']);
    return;
}else if (strcmp($extension, 'gif') == 0){
    processfilepostpicperfil($file,$_SESSION['usuario']);
    return;
}else{
   echo 1;
}

echo "string";

}

elseif ($process == 4) 
{

  // Checamos si la sesion existe.
  IsLoginProcess();


  $phone = $xss->filter_it($_POST['phone']);
  
  if (empty($phone) || ctype_space($phone) || is_null($phone)) {
    echo 1;
    exit();
  }

  # spaces
  $finalwallet = $xss->filter_it($wallet);


  $checkthis = checkInformationReturn();

  if ($checkthis == TRUE) 
  {
    # Insertamos la Información


    $SQL = 'INSERT INTO information (phone, usuario) VALUES (:phone, :usuario)';
    $stn = $conexion -> prepare($SQL);
    $stn -> bindParam(':phone',$phone,PDO::PARAM_STR);
    $stn -> bindParam(':usuario',$_SESSION['usuario'],PDO::PARAM_INT);
    $stn -> execute();

    echo 1;

  }
  else
  {
    # Si la información ya existe solamente la actualizamos

    $SQL = 'UPDATE information SET phone = :phone WHERE usuario = :usuario';
    $stn = $conexion -> prepare($SQL);
    $stn -> bindParam(':phone',$phone,PDO::PARAM_STR);
    $stn -> bindParam(':usuario',$_SESSION['usuario'],PDO::PARAM_INT);
    $stn -> execute();

    echo 1;

  }



}

else if ($process == 5) 
{


     // Checamos si la sesion existe.
     IsLoginProcess();


     $phone = $xss->filter_it($_POST['phone']);
     $nombre = $xss->filter_it($_POST['nombre']);
     $apellido = $xss->filter_it($_POST['apellido']);
     $pais = $xss->filter_it($_POST['pais']);
  
     if (empty($phone) || ctype_space($phone) || is_null($phone)) {
       echo 1;
       exit();
     }


     if (empty($nombre) || ctype_space($nombre) || is_null($nombre)) {
       echo 1;
       exit();
     }

     if (empty($apellido) || ctype_space($apellido) || is_null($apellido)) {
       echo 1;
       exit();
     }

     if (empty($pais) || ctype_space($pais) || is_null($pais)) {
       echo 1;
       exit();
     }     

     $SQL1 = 'UPDATE usuarios SET nombre = :nombre, apellido = :apellido, pais = :pais WHERE id = :id';
     $SQL2 = 'UPDATE information SET phone = :phone WHERE usuario = :usuario';

     $stn = $conexion -> prepare($SQL1);
     $stn -> bindParam(':nombre',$nombre,PDO::PARAM_STR);
     $stn -> bindParam(':apellido',$apellido,PDO::PARAM_STR);
     $stn -> bindParam(':pais',$pais,PDO::PARAM_INT);
     $stn -> bindParam(':id',$_SESSION['usuario'],PDO::PARAM_INT);
     $stn -> execute();

     $stn2 = $conexion -> prepare($SQL2);
     $stn2 -> bindParam(':phone',$phone,PDO::PARAM_STR);
     $stn2 -> bindParam(':usuario',$_SESSION['usuario'],PDO::PARAM_INT);
     $stn2 -> execute();

     echo 2;

}

else if ($process == 6) 
{


     // Checamos si la sesion existe.
     IsLoginProcess();
     
     $msj = $xss->filter_it($_POST['mensaje']);
     $email = $xss->filter_it($_POST['email']);

     if (empty($msj) || ctype_space($msj) || is_null($msj)) {
       echo 2;
       exit();
     }

     if (empty($email) || ctype_space($email) || is_null($email)) {
       echo 2;
       exit();
     }

     $checkmail = checkMail($email);


     if ($checkmail == 1){

         // Configuracion Email
         $dataem = emailConfig();

         // Separamos la Configuración
         $confe = explode('|', $dataem);

         if ($confe[2] == 1){
           $secure = 'tls';
         }else{
           $secure = 'ssl';
         }

         // Envio del Email
         $mail = new PHPMailer;
         $mail->isSMTP(); 
         $mail->SMTPDebug = 0; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages
         $mail->Host = $confe[0]; // use $mail->Host = gethostbyname('smtp.gmail.com'); // if your network does not support SMTP over IPv6
         $mail->Port = $confe[1]; // TLS only
         $mail->SMTPSecure = $secure; // ssl is depracated
         $mail->SMTPAuth = true;
         $mail->Username = $confe[3];
         $mail->Password = $confe[4];
         $mail->setFrom($confe[3], $confe[5]);
         $mail->addAddress($email, "");
         $mail->Subject = $confe[6];
         $mail->isHTML(true); // Set email format to HTML
         $mail->msgHTML($msj.'<p></p> Completa tu registro usando esl siguiente link: <a href="'.get_referal_link().'">'.get_referal_link().'</a>'); 

         if(!$mail->send()){
             echo 4;
         }else{
             echo 3;
         }


     }else{
        echo 2;
     }


}

else if ($process == 7) 
{

     // Checamos si la sesion existe.
     IsLoginProcess();

     if (isset($_POST['referalink'])) {


     $referal = $xss->filter_it($_POST['referalink']);
     if (empty($referal) || ctype_space($referal) || is_null($referal)) {
       echo 2;
       exit();
     }

     $finalreferal = str_replace(' ', '', $referal);

     $SQL = 'SELECT referal FROM usuarios WHERE referal = :referal';
     $stn = $conexion -> prepare($SQL);
     $stn -> bindParam(':referal', $referal , PDO::PARAM_STR);
     $stn -> execute();
     $rst = $stn -> fetchAll();
     if (empty($rst)){

          $UP = 'UPDATE usuarios SET referal = :referal WHERE id = :id';
          $stnu = $conexion -> prepare($UP);
          $stnu -> bindParam(':referal', $finalreferal , PDO::PARAM_STR);
          $stnu -> bindParam(':id', $_SESSION['usuario'] , PDO::PARAM_INT);
          $stnu -> execute();

          echo $finalreferal;


     }else{

         echo 2;

     }


     }else{
       exit();
     }

     $conexion = null;

}
// Cambio de la contraseña
else if ($process == 8) {
  
     // Checamos si la sesion existe.
     IsLoginProcess();

     $actual = $xss->filter_it($_POST['actual']);
     $newpass = $xss->filter_it($_POST['newpass']);
     if (empty($actual) || ctype_space($actual) || is_null($actual)) {echo 1;exit();}
     if (empty($newpass) || ctype_space($newpass) || is_null($newpass)) {echo 1;exit();} 

     $cryptnew = sha1(PASSTK.$newpass);
     $actualcrypt = sha1(PASSTK.$actual );

     $SQL = 'SELECT * FROM usuarios WHERE id = :id';
     $stn = $conexion -> prepare($SQL);
     $stn -> bindParam(':id', $_SESSION['usuario'] ,PDO::PARAM_STR);
     $stn -> execute();
     $rst = $stn -> fetchAll();
     foreach ($rst as $key){
        $actualpassget = $key['password'];
     }


     if (strcmp($actualcrypt, $actualpassget) == 0) {

          if (strcmp($cryptnew, $actualpassget) == 0){
            echo 2;
          }else{
            $U = 'UPDATE usuarios SET password = :password WHERE id = :id LIMIT 1';
            $ustn = $conexion -> prepare($U);
            $ustn -> bindParam(':password', $cryptnew ,PDO::PARAM_STR);
            $ustn -> bindParam(':id', $_SESSION['usuario'] ,PDO::PARAM_STR);
            $ustn -> execute();
            echo 3;
          }

     }else{
       echo 4;
     }

     $conexion = null;

}
//
else if ($process == 9) {
  
     // Checamos si la sesion existe.
     IsLoginProcess();

     $actual = $xss->filter_it($_POST['actual']);
     $newmail = $xss->filter_it($_POST['newemail']);
     if (empty($actual) || ctype_space($actual) || is_null($actual)) {echo 1;exit();}
     if (empty($newmail) || ctype_space($newmail) || is_null($newmail)) {echo 1;exit();} 

     $actualcrypt = sha1(PASSTK.$actual);

     $SQL = 'SELECT * FROM usuarios WHERE id = :id';
     $stn = $conexion -> prepare($SQL);
     $stn -> bindParam(':id', $_SESSION['usuario'] ,PDO::PARAM_STR);
     $stn -> execute();
     $rst = $stn -> fetchAll();
     foreach ($rst as $key){
        $actualpassget = $key['password'];
        $actualmail = $key['email'];
     }


         if (strcmp($actualcrypt, $actualpassget) == 0){

             if (strcmp($newmail, $actualmail) == 0) {
               echo 3;
             }else{

                  $CHECK = 'SELECT * FROM usuarios WHERE email = :email LIMIT 1';
                  $chkstn = $conexion -> prepare($CHECK);
                  $chkstn -> bindParam(':email', $newmail ,PDO::PARAM_STR);
                  $chkstn -> execute();
                  $chkrst = $chkstn -> fetchAll();
                  if (empty($chkrst)){

                         $U = 'UPDATE usuarios SET email = :email WHERE id = :id LIMIT 1';
                         $ustn = $conexion -> prepare($U);
                         $ustn -> bindParam(':email', $newmail ,PDO::PARAM_STR);
                         $ustn -> bindParam(':id', $_SESSION['usuario'] ,PDO::PARAM_STR);
                         $ustn -> execute();
                         echo 5;

                  }else{
                    echo 4;
                  }

             }

         }else{
           echo 2;
         }






     $conexion = null;



}

else if ($process == 10) {


     $emailget = $xss->filter_it($_POST['email']);
     if (empty($emailget) || ctype_space($emailget) || is_null($emailget)) {echo 2;exit();} 
     
      $randompass = generateRandomString();
      $cryptrandompass = sha1(PASSTK.$randompass);

      $checkmail = checkMail($emailget);
      if ($checkmail == 2){


          $U = 'UPDATE usuarios SET password = :password WHERE email = :email LIMIT 1';
          $ustn = $conexion -> prepare($U);
          $ustn -> bindParam(':password', $cryptrandompass , PDO::PARAM_STR);
          $ustn -> bindParam(':email', $emailget , PDO::PARAM_STR);
          $ustn -> execute();

         // Configuracion Email
         $dataem = emailConfig();

         // Separamos la Configuración
         $confe = explode('|', $dataem);

         if ($confe[2] == 1){
           $secure = 'tls';
         }else{
           $secure = 'ssl';
         }

         // Envio del Email
         $mail = new PHPMailer;
         $mail->isSMTP(); 
         $mail->SMTPDebug = 0; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages
         $mail->Host = $confe[0]; // use $mail->Host = gethostbyname('smtp.gmail.com'); // if your network does not support SMTP over IPv6
         $mail->Port = $confe[1]; // TLS only
         $mail->SMTPSecure = $secure; // ssl is depracated
         $mail->SMTPAuth = true;
         $mail->Username = $confe[3];
         $mail->Password = $confe[4];
         $mail->setFrom($confe[3], $confe[5]);
         $mail->addAddress($emailget, "");
         $mail->Subject = 'Recuperacion de Cuenta';
         $mail->isHTML(true); // Set email format to HTML
         $mail->msgHTML('Hola! has pedido recuperar tu cuenta, tu nuevo acceso es: <strong>'.$randompass.'</strong>'); 

         if(!$mail->send()){
             echo 4;
         }else{
             echo 1;
         }



      }else{
          echo 2;
      }      



}





























//-----------------------------------------
}
//-----------------------------------------