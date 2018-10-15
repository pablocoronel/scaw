<?php
	session_start();
	include_once 'conexionDB.php';

	//Evitar ingreso sin login
	if (!isset($_SESSION['loginId'])){
		header('location: ingreso.php');
		exit();
	}

	session_destroy();
	header('location: ingreso.php');
	exit();
?>