<?php
// No debe haber ningún espacio antes de este <?php

// Configuración de errores (solo para desarrollo, quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicia sesión
session_start();

// Verificar si hay usuario
if(!isset($_SESSION["datosusuario"])){
    header("location: index.php");
    exit;
}

$almm = $_SESSION["datosusuario"];
foreach ($almm as $reg) {
    if(!($reg["id_rol"] == 1 || $reg["id_rol"] == 2)){
        header("location: index.php");
        exit;
    }
}

// Cargar controlador
require_once 'controller/informes/ventas/inf_ventas.controller.php';

// Determinar controlador y acción
$controllerName = isset($_REQUEST['c']) ? $_REQUEST['c'] . 'Controller' : 'InformeController';
$accion = isset($_REQUEST['a']) ? $_REQUEST['a'] : 'Index';

// Instanciar controlador
$controller = new $controllerName();

// Llamar a la acción
call_user_func([$controller, $accion]);
