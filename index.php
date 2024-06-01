<?php
   require_once 'administrator/includes/functions.php';
   
   IsLogin();

   // Login Template
   themeAdd('header-login.php');

   $enable = checkModActivePage(1);

   if ($enable == 1){
   	 themeAdd('disabled.php');
   }else{
   	 themeAdd('login.php');
   }

   themeAdd('footer-login.php');

?>



