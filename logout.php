<?php
require_once 'administrator/includes/functions.php';
LogoutSet();
session_unset();
session_destroy();
if (isset($_COOKIE['PHPSESSID'])){
	unset($_COOKIE['PHPSESSID']);
	unset($_SESSION['usuario']);
	unset($_SESSION['rango']);
	unset($_SESSION['ipaccess']);
	unset($_SESSION['myparent']);
}else{
	unset($_COOKIE['PHPSESSID']);
	unset($_SESSION['usuario']);
	unset($_SESSION['rango']);
	unset($_SESSION['ipaccess']);
	unset($_SESSION['myparent']);	
}

session_start();
session_regenerate_id(true);
session_destroy();
header('Location: '.base_url_return());
