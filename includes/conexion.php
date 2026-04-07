<?php
// defino la ruta de la base de datos relativa a este archivo
define('DB_RUTA', __DIR__ . '/../base_datos/gatitos.db');

// creo una funcion para conectarme a la base de datos
function conectar() {
    try {
        // intento crear la conexion con sqlite3
        $db = new SQLite3(DB_RUTA);
        
        // activo las claves foraneas porque sqlite las tiene desactivadas por defecto
        $db->exec('PRAGMA foreign_keys = ON;');
        
        // devuelvo la conexion para usarla en otros archivos
        return $db;

    } catch (Exception $e) {
        // si hay un error lo muestro y detengo la ejecucion
        http_response_code(500);
        echo json_encode(["error" => "No se pudo conectar a la base de datos: " . $e->getMessage()]);
        exit;
    }
}
?>