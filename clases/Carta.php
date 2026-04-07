<?php
// defino la clase carta que representa cada carta del juego
class Carta {
    // defino los atributos de la carta
    private string $tipo;
    private string $nombre;
    private string $descripcion;
    private string $icono;

    // constructor que inicializa la carta con sus datos
    public function __construct(string $tipo, string $nombre, string $descripcion, string $icono) {
        $this->tipo       = $tipo;
        $this->nombre     = $nombre;
        $this->descripcion = $descripcion;
        $this->icono      = $icono;
    }

    // devuelvo el tipo de carta
    public function getTipo(): string {
        return $this->tipo;
    }

    // devuelvo el nombre de la carta
    public function getNombre(): string {
        return $this->nombre;
    }

    // devuelvo la descripcion de la carta
    public function getDescripcion(): string {
        return $this->descripcion;
    }

    // devuelvo el icono de la carta
    public function getIcono(): string {
        return $this->icono;
    }

    // devuelvo la carta como array para poder convertirla a json facilmente
    public function toArray(): array {
        return [
            'tipo'        => $this->tipo,
            'nombre'      => $this->nombre,
            'descripcion' => $this->descripcion,
            'icono'       => $this->icono,
        ];
    }
}
?>