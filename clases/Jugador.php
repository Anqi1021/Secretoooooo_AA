<?php
// defino la clase jugador que representa a cada persona en la partida
class Jugador {
    // defino los atributos del jugador
    private string $nombre;
    private array  $mano;
    private bool   $eliminado;

    // constructor que inicializa el jugador con su nombre y la mano vacia
    public function __construct(string $nombre) {
        $this->nombre    = $nombre;
        $this->mano      = [];
        $this->eliminado = false;
    }

    // devuelvo el nombre del jugador
    public function getNombre(): string {
        return $this->nombre;
    }

    // devuelvo las cartas que tiene el jugador en la mano
    public function getMano(): array {
        return $this->mano;
    }

    // añado una carta a la mano del jugador
    public function recibirCarta(array $carta): void {
        $this->mano[] = $carta;
    }

    // elimino una carta de la mano del jugador por su posicion
    public function jugarCarta(int $posicion): ?array {
        if (isset($this->mano[$posicion])) {
            $carta = $this->mano[$posicion];
            array_splice($this->mano, $posicion, 1);
            return $carta;
        }
        return null;
    }

    // marco al jugador como eliminado cuando explota
    public function eliminar(): void {
        $this->eliminado = true;
    }

    // compruebo si el jugador esta eliminado
    public function estaEliminado(): bool {
        return $this->eliminado;
    }

    // compruebo si el jugador tiene una carta defuse en la mano
    public function tieneDefuse(): bool {
        foreach ($this->mano as $carta) {
            if ($carta['tipo'] === 'defuse') {
                return true;
            }
        }
        return false;
    }

    // devuelvo el jugador como array para guardarlo en la sesion
    public function toArray(): array {
        return [
            'nombre'    => $this->nombre,
            'mano'      => $this->mano,
            'eliminado' => $this->eliminado,
        ];
    }
}
?>