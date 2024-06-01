<?php
   require_once 'administrator/includes/functions.php';
   
   IsLogin();

   // Recovery Template
   $enable = checkModActivePage(5);

   if ($enable == 1){
   	 themeAdd('disabled.php');
   }else{
   	 themeAdd('recovery.php');
   }

   themeAdd('footer-login.php');

?>