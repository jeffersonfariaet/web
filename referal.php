<?php

  require_once 'administrator/includes/register.class.php';


   if (isset($_SESSION['activess'])) 
   {
       if (strcmp($_SESSION['activess'], SESSTK) == 0) 
       {
          header('Location: '.base_url_return().'dashboard');
          exit();
       }else{

       }
   }

  // Incluimos nuestra clase de registro
  $register = new Register();


  // Tomamos el permalink
  $perma = $_GET['permalink'];


  // En caso de un ataque de SQL o XSS
  $xss = new xss_filter();
  $sanperma = $xss->filter_it($perma);

  // Si el permalink esta vacio entonces hacemos logout inmediatamente
  if (empty($perma) || empty($sanperma) || ctype_space($perma) || ctype_space($sanperma))
  {
  	header('Location: '.base_url_return().'logout');
  	exit();
  }

  // Si el permalink no es alfanumerico tambien lo sacamos
  if (ctype_alnum($perma)) 
  {


     // Tomamos el ID del Referido
     $parts = $register -> getIDUserReferal($perma);

     // Hacemos explode para saber cuantos referidos tiene y cual es su id
     // para poder asi hacer el conteo
     $parseparts = explode('|', $parts);

     // ID de usuario
     $iduser = $parseparts[0];
     // Numero de referidos
     $referalnumbers = $parseparts[1];


     // A partir del ID del referido sacamos toma la numeración
     $returndata = $register -> generateNumbers($iduser,$referalnumbers);

  }
  else
  {
  	 header('Location: '.base_url_return().'logout');
  	 exit();
  }
