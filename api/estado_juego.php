<?php
// inicio la sesion para leer y escribir el estado del juego
session_start();

// indico que la respuesta sera en formato json
header('Content-Type: application/json');

// atiendo peticion GET para obtener el estado actual del juego
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['partida'])) {
        http_response_code(404);
        echo json_encode(["error" => "No hay ninguna partida en curso"]);
        exit;
    }
    echo json_encode($_SESSION['partida']);

// atiendo peticion POST para actualizar el estado del juego
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        http_response_code(400);
        echo json_encode(["error" => "Datos invalidos"]);
        exit;
    }

    // guardo el estado de la partida en la sesion
    $_SESSION['partida'] = $input;

    http_response_code(200);
    echo json_encode(["success" => "Estado actualizado correctamente"]);

// atiendo peticion DELETE para eliminar la partida de la sesion cuando termina
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    unset($_SESSION['partida']);
    http_response_code(200);
    echo json_encode(["success" => "Partida eliminada de la sesion"]);

} else {
    http_response_code(405);
    echo json_encode(["error" => "Metodo no permitido"]);
}
?>