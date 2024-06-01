<?php
   require_once 'administrator/includes/functions.php';
   
   // Login Template
   themeAdd('header.php');

   $enable = checkModActivePage(6);

   if ($enable == 1){
   	 themeAdd('disabled.php');
   }else{
   	 themeAdd('registro-template.php');
   }

   themeAdd('footer-login.php');

?>



