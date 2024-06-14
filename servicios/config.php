<?php
// Configuración de la base de datos
$host = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'senatino';



// Conectar a la base de datos
$conn = new mysqli($host, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Error al conectar a la base de datos: " . $conn->connect_error);
}
?>