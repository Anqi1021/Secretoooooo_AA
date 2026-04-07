<?php
// incluyo la conexion a la base de datos
require_once __DIR__ . '/../includes/conexion.php';

// indico que la respuesta sera en formato json
header('Content-Type: application/json');

// conecto a la base de datos
$db = conectar();

// atiendo peticion GET para obtener todos los jugadores
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $resultados = $db->query("SELECT * FROM jugadores ORDER BY victorias DESC");
    $jugadores  = [];

    while ($fila = $resultados->fetchArray(SQLITE3_ASSOC)) {
        $jugadores[] = $fila;
    }

    echo json_encode($jugadores);

// atiendo peticion POST para crear o encontrar un jugador por nombre
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // valido que llegue el nombre del jugador
    if (!isset($input['nombre']) || empty(trim($input['nombre']))) {
        http_response_code(400);
        echo json_encode(["error" => "El nombre del jugador es obligatorio"]);
        exit;
    }

    $nombre = trim($input['nombre']);

    // busco si el jugador ya existe en la base de datos
    $stmt = $db->prepare("SELECT * FROM jugadores WHERE nombre = :nombre");
    $stmt->bindValue(':nombre', $nombre, SQLITE3_TEXT);
    $resultado = $stmt->execute();
    $jugador   = $resultado->fetchArray(SQLITE3_ASSOC);

    // si no existe lo creo
    if (!$jugador) {
        $stmt = $db->prepare("INSERT INTO jugadores (nombre) VALUES (:nombre)");
        $stmt->bindValue(':nombre', $nombre, SQLITE3_TEXT);
        $stmt->execute();

        $id      = $db->lastInsertRowID();
        $jugador = ['id' => $id, 'nombre' => $nombre, 'victorias' => 0, 'derrotas' => 0, 'explosiones' => 0, 'total_partidas' => 0];
    }

    http_response_code(201);
    echo json_encode($jugador);

} else {
    // rechazo cualquier otro metodo http
    http_response_code(405);
    echo json_encode(["error" => "Metodo no permitido"]);
}

// cierro la conexion a la base de datos
$db->close();
?>