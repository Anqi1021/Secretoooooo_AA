<?php
// incluyo las clases que necesito para la partida
require_once __DIR__ . '/Carta.php';
require_once __DIR__ . '/Jugador.php';

// defino la clase partida que gestiona toda la logica del juego
class Partida {
    // defino los atributos de la partida
    private array $jugadores;
    private array $mazo;
    private array $descarte;
    private int   $turnoActual;
    private bool  $terminada;

    // constructor que inicializa la partida con los jugadores
    public function __construct(array $nombresJugadores) {
        $this->jugadores   = [];
        $this->mazo        = [];
        $this->descarte    = [];
        $this->turnoActual = 0;
        $this->terminada   = false;

        // creo un objeto jugador por cada nombre recibido
        foreach ($nombresJugadores as $nombre) {
            $this->jugadores[] = new Jugador($nombre);
        }
    }

    // creo y barajo el mazo inicial del juego
    public function inicializarMazo(): void {
        $cartas = [];

        // añado las cartas normales al mazo
        $tipos = [
            ['tipo' => 'skip',    'nombre' => 'Skip',           'descripcion' => 'Salta tu turno sin robar',         'icono' => '⏭️'],
            ['tipo' => 'attack',  'nombre' => 'Attack',         'descripcion' => 'El siguiente toma 2 turnos',        'icono' => '⚔️'],
            ['tipo' => 'nope',    'nombre' => 'Nope',           'descripcion' => 'Cancela cualquier carta',           'icono' => '🙅'],
            ['tipo' => 'future',  'nombre' => 'See the Future', 'descripcion' => 'Ves las 3 cartas del mazo',        'icono' => '👀'],
            ['tipo' => 'shuffle', 'nombre' => 'Shuffle',        'descripcion' => 'Baraja el mazo',                   'icono' => '🔀'],
            ['tipo' => 'favor',   'nombre' => 'Favor',          'descripcion' => 'Pide una carta a otro jugador',    'icono' => '🤝'],
            ['tipo' => 'cat',     'nombre' => 'Tacocat',        'descripcion' => 'En pareja roba una carta al rival','icono' => '🐱'],
        ];

        // añado 4 copias de cada carta normal
        foreach ($tipos as $tipo) {
            for ($i = 0; $i < 4; $i++) {
                $cartas[] = $tipo;
            }
        }

        // añado los gatitos explosivos segun el numero de jugadores menos uno
        $numBombas = count($this->jugadores) - 1;
        for ($i = 0; $i < $numBombas; $i++) {
            $cartas[] = [
                'tipo'        => 'bomba',
                'nombre'      => 'Exploding Kitten',
                'descripcion' => 'Si la robas sin Defuse, explota',
                'icono'       => '💣'
            ];
        }

        // barajo el mazo aleatoriamente
        shuffle($cartas);
        $this->mazo = $cartas;

        // reparto 7 cartas a cada jugador mas 1 defuse garantizado
        foreach ($this->jugadores as $jugador) {
            $jugador->recibirCarta([
                'tipo'        => 'defuse',
                'nombre'      => 'Defuse',
                'descripcion' => 'Desactiva el gatito y lo devuelves al mazo',
                'icono'       => '🔧'
            ]);
            for ($i = 0; $i < 7; $i++) {
                if (!empty($this->mazo)) {
                    $jugador->recibirCarta(array_shift($this->mazo));
                }
            }
        }
    }

    // devuelvo el jugador que tiene el turno actual
    public function getJugadorActual(): Jugador {
        return $this->jugadores[$this->turnoActual];
    }

    // paso al siguiente turno saltando jugadores eliminados
    public function siguienteTurno(): void {
        do {
            $this->turnoActual = ($this->turnoActual + 1) % count($this->jugadores);
        } while ($this->jugadores[$this->turnoActual]->estaEliminado());
    }

    // compruebo si la partida ha terminado
    public function comprobarFin(): bool {
        $activos = array_filter($this->jugadores, fn($j) => !$j->estaEliminado());
        if (count($activos) === 1) {
            $this->terminada = true;
        }
        return $this->terminada;
    }

    // devuelvo el ganador de la partida
    public function getGanador(): ?Jugador {
        foreach ($this->jugadores as $jugador) {
            if (!$jugador->estaEliminado()) {
                return $jugador;
            }
        }
        return null;
    }

    // devuelvo el mazo actual
    public function getMazo(): array {
        return $this->mazo;
    }

    // devuelvo todos los jugadores
    public function getJugadores(): array {
        return $this->jugadores;
    }

    // devuelvo la partida como array para guardarla en la sesion
    public function toArray(): array {
        return [
            'jugadores'   => array_map(fn($j) => $j->toArray(), $this->jugadores),
            'mazo'        => $this->mazo,
            'descarte'    => $this->descarte,
            'turnoActual' => $this->turnoActual,
            'terminada'   => $this->terminada,
        ];
    }
}
?>