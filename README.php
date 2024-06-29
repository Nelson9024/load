<?php
// Lee el parámetro 'api' de la URL
$api = isset($_GET['api']) ? $_GET['api'] : '';

switch ($api) {
    case '1':
        require_once 'servicios/api.php';
        break;
    case '2':
        require_once 'servicios/api2.php';
        break;
    default:
        echo "Por favor, selecciona una API válida.";
}
?>
