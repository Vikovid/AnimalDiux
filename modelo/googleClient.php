<?php 
	require_once('vendor/autoload.php');
	$cliente = new Google_Client();

	$cliente->setClientId    ('954800726997-rb4nujm1loup384ioa6a2kj3vq1p0fdd.apps.googleusercontent.com');
	$cliente->setClientSecret('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');
	$cliente->setRedirectUri ('http://localhost/animaldiux/vista/login/home.php');

	$cliente->addScope(Google_Service_Calendar::CALENDAR);
	$cliente->addScope('email');

	if (isset($_GET['code'])) {
		$token = $cliente->fetchAccessTokenWithAuthCode($_GET['code']);
		$_SESSION['GoogleLoginToken'] = $token;

		$oauth2 =   new Google_Service_Oauth2($cliente);
		$userInfo = $oauth2->userinfo->get();

		$_SESSION['mailUsuario'] = $userInfo->email;
		$session->msg("i","Bienvenido \"".$_SESSION['mailUsuario']."\" has iniciado sesión con Google.");

		if (isset($_SERVER['HTTP_REFERER'])) redirect($_SERVER['HTTP_REFERER'],false);
		else redirect("http://localhost/animaldiux/vista/login/home.php",false);
	} 
	if (isset($_SESSION['GoogleLoginToken']))
		$cliente->setAccessToken($_SESSION['GoogleLoginToken']);
?>