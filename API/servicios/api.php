<?php
// Incluir el archivo de configuración
require_once 'config.php';

// Función para obtener todos los registros de la tabla
function obtenerRegistros() {
    global $conn;
    $sql = "SELECT * FROM licencia";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $registros = array();
        while ($row = $result->fetch_assoc()) {
            $registros[] = $row;
        }
        return $registros;
    } else {
        return array();
    }
}

// Función para obtener un registro específico
function obtenerRegistro($id) {
    global $conn;
    $sql = "SELECT * FROM licencia WHERE id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    } else {
        return array();
    }
}

// Función para insertar un registro
function insertarRegistro($datos) {
    global $conn;
    $serie = $datos['serie'];
    $nombre = $datos['nombre'];
    $sql = "INSERT INTO licencia (serie, nombre) VALUES ('$serie', '$nombre')";
    $conn->query($sql);

    return $conn->insert_id;
}



// Función para actualizar un registro
function actualizarRegistro($id, $datos) {
    global $conn;
    $serie = $datos['serie'];
    $nombre = $datos['nombre'];
    $sql = "UPDATE licencia SET serie = '$serie', nombre = '$nombre' WHERE id = '$id'";
    $conn->query($sql);

    return true;
}

// Función para eliminar un registro
function eliminarRegistro($id) {
    global $conn;
    $sql = "DELETE FROM licencia WHERE id = '$id'";
    $conn->query($sql);

    return true;
}

// Procesar las solicitudes
header('Content-Type: application/json'); // Establecer el tipo de contenido como JSON
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $registro = obtenerRegistro($_GET['id']);
            echo json_encode($registro);
        } else {
            $registros = obtenerRegistros();
            echo json_encode($registros);
        }
        break;
    case 'POST':
        $datos = json_decode(file_get_contents('php://input'), true);
        $id = insertarRegistro($datos);
        echo json_encode(array('id' => $id));
        break;
    case 'PUT':
        $id = $_GET['id'];
        $datos = json_decode(file_get_contents('php://input'), true);
        actualizarRegistro($id, $datos);
        echo json_encode(array('mensaje' => 'Registro actualizado'));
        break;
    case 'DELETE':
        $id = $_GET['id'];
        eliminarRegistro($id);
        echo json_encode(array('mensaje' => 'Registro eliminado'));
        break;
    default:
        http_response_code(405);
        echo json_encode(array('mensaje' => 'Método no permitido'));
        break;
}
?>
