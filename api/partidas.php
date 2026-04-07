<?php
// incluyo la conexion a la base de datos
require_once __DIR__ . '/../includes/conexion.php';

// indico que la respuesta sera en formato json
header('Content-Type: application/json');

// conecto a la base de datos
$db = conectar();

// atiendo peticion GET para obtener el historial de partidas
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $resultados = $db->query("
        SELECT partidas.*, 
               GROUP_CONCAT(jugadores.nombre) as jugadores
        FROM partidas
        LEFT JOIN partidas_jugadores ON partidas.id = partidas_jugadores.id_partida
        LEFT JOIN jugadores ON partidas_jugadores.id_jugador = jugadores.id
        GROUP BY partidas.id
        ORDER BY partidas.fecha DESC
    ");

    $partidas = [];
    while ($fila = $resultados->fetchArray(SQLITE3_ASSOC)) {
        $partidas[] = $fila;
    }

    echo json_encode($partidas);

// atiendo peticion POST para guardar una partida terminada
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // valido que lleguen todos los datos necesarios
    if (!isset($input['ganador']) || !isset($input['rondas']) || !isset($input['jugadores'])) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos de la partida"]);
        exit;
    }

    // inserto la partida en la base de datos
    $stmt = $db->prepare("INSERT INTO partidas (fecha, rondas, ganador) VALUES (:fecha, :rondas, :ganador)");
    $stmt->bindValue(':fecha',   date('Y-m-d H:i:s'), SQLITE3_TEXT);
    $stmt->bindValue(':rondas',  $input['rondas'],     SQLITE3_INTEGER);
    $stmt->bindValue(':ganador', $input['ganador'],    SQLITE3_TEXT);
    $stmt->execute();

    $idPartida = $db->lastInsertRowID();

    // guardo la relacion de cada jugador con la partida
    foreach ($input['jugadores'] as $jugador) {
        // busco el id del jugador por su nombre
        $stmtJug = $db->prepare("SELECT id FROM jugadores WHERE nombre = :nombre");
        $stmtJug->bindValue(':nombre', $jugador['nombre'], SQLITE3_TEXT);
        $resJug    = $stmtJug->execute();
        $filaJug   = $resJug->fetchArray(SQLITE3_ASSOC);

        if ($filaJug) {
            // inserto la relacion en partidas_jugadores
            $stmtPJ = $db->prepare("INSERT INTO partidas_jugadores (id_partida, id_jugador, exploto) VALUES (:id_partida, :id_jugador, :exploto)");
            $stmtPJ->bindValue(':id_partida', $idPartida,           SQLITE3_INTEGER);
            $stmtPJ->bindValue(':id_jugador', $filaJug['id'],       SQLITE3_INTEGER);
            $stmtPJ->bindValue(':exploto',    $jugador['exploto'],   SQLITE3_INTEGER);
            $stmtPJ->execute();

            // actualizo las estadisticas del jugador
            $esGanador = ($jugador['nombre'] === $input['ganador']) ? 1 : 0;
            $stmtUp    = $db->prepare("
                UPDATE jugadores SET
                    victorias      = victorias      + :victoria,
                    derrotas       = derrotas       + :derrota,
                    explosiones    = explosiones    + :exploto,
                    total_partidas = total_partidas + 1
                WHERE id = :id
            ");
            $stmtUp->bindValue(':victoria', $esGanador,           SQLITE3_INTEGER);
            $stmtUp->bindValue(':derrota',  $esGanador ? 0 : 1,   SQLITE3_INTEGER);
            $stmtUp->bindValue(':exploto',  $jugador['exploto'],   SQLITE3_INTEGER);
            $stmtUp->bindValue(':id',       $filaJug['id'],        SQLITE3_INTEGER);
            $stmtUp->execute();
        }
    }

    http_response_code(201);
    echo json_encode(["success" => "Partida guardada correctamente", "id_partida" => $idPartida]);

} else {
    http_response_code(405);
    echo json_encode(["error" => "Metodo no permitido"]);
}

// cierro la conexion
$db->close();
?>