<?php
// Incluir el archivo de configuración
require_once 'config.php';

// Función para obtener todos los registros de la tabla
function obtenerRegistros() {
    global $conn;
    $sql = "SELECT * FROM LicenciasTipos";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $registros = array();
        while ($row = $result->fetch_assoc()) {
            // Verificar si hay un registro en la tabla 'licencia' con el mismo 'serie'
            $serie = $row['serial'];
            $sql_licencia = "SELECT * FROM licencia WHERE serie = '$serie'";
            $result_licencia = $conn->query($sql_licencia);

            if ($result_licencia->num_rows > 0) {
                // Si hay un registro en 'licencia' con el mismo 'serie', agregarlo a los registros
                $registros[] = $row;
            }
        }
        return $registros;
    } else {
        return array();
    }
}

function obtenerRegistro($id) {
    global $conn;
    $sql = "SELECT * FROM LicenciasTipos WHERE id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $registro = $result->fetch_assoc();
        // Verificar si hay un registro en la tabla 'licencia' con el mismo 'serie'
        $serie = $registro['serial'];
        $sql_licencia = "SELECT * FROM licencia WHERE serie = '$serie'";
        $result_licencia = $conn->query($sql_licencia);

        if ($result_licencia->num_rows > 0) {
            // Si hay un registro en 'licencia' con el mismo 'serie', devolver el registro
            return $registro;
        } else {
            return array();
        }
    } else {
        return array();
    }
}
function FiltrarRegistros($serialValor) {
    global $conn;

    // Escapar el valor para evitar inyección de SQL
    $serialValor = $conn->real_escape_string($serialValor);

    // Construir la consulta SQL filtrando por el valor de serial
    $sql = "SELECT * FROM LicenciasTipos WHERE serial = '$serialValor'";

    // Ejecutar la consulta
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $registros = array();
        while ($row = $result->fetch_assoc()) {
            $registros[] = $row;
        }
        return $registros;
    } else {
        // No se encontraron registros que coincidan con el valor de serial
        return array();
    }
}



// Función para insertar un registro
function insertarRegistro($datos) {
    global $conn;
    $serial = $datos['serial'];
    $tipo = $datos['tipo'];
    $fecha_finalizacion = $datos['fecha_finalizacion'];
    
    // Verificar si ya existe una fila con el mismo serial y tipo
    $sql_check = "SELECT id FROM LicenciasTipos WHERE serial = '$serial' AND tipo = '$tipo'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        // Si ya existe una fila con el mismo serial y tipo, editar la fila existente
        $row = $result_check->fetch_assoc();
        $id_existente = $row['id'];
        $sql_update = "UPDATE LicenciasTipos SET fecha_finalizacion = '$fecha_finalizacion' WHERE id = '$id_existente'";
        $conn->query($sql_update);

        return $id_existente; // Devolver el ID de la fila actualizada
    } else {
        // Si no existe una fila con el mismo serial y tipo, insertar una nueva fila
        $sql_insert = "INSERT INTO LicenciasTipos (serial, tipo, fecha_finalizacion) VALUES ('$serial', '$tipo', '$fecha_finalizacion')";
        $conn->query($sql_insert);

        return $conn->insert_id; // Devolver el ID de la nueva fila insertada
    }
}


// Función para actualizar un registro
function actualizarRegistro($id, $datos) {
    global $conn;
    $serial = $datos['serial'];
    $tipo = $datos['tipo'];
    $fecha_finalizacion = $datos['fecha_finalizacion'];
    $sql = "UPDATE LicenciasTipos SET serial = '$serial', tipo = '$tipo', fecha_finalizacion = '$fecha_finalizacion' WHERE id = '$id'";
    $conn->query($sql);

    return true;
}

// Función para eliminar un registro
function eliminarRegistro($id) {
    global $conn;
    $sql = "DELETE FROM LicenciasTipos WHERE id = '$id'";
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
    case 'FILTRO':
        if (isset($_GET['serial'])) {
            $registros = FiltrarRegistros($_GET['serial']);
            echo json_encode($registros);
         } else {
            echo json_encode(array('error' => 'Se requiere el parámetro "serial" para el filtro.'));
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
