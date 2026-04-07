// defino el estado del juego que se actualizara durante la partida
let estadoJuego = null;

// cargo el estado del juego desde la api al iniciar la pagina
async function cargarEstado() {
    try {
        const respuesta = await fetch('/Secretoooooo_AA/api/estado_juego.php');
        if (!respuesta.ok) {
            window.location.href = '/Secretoooooo_AA/setup.php';
            return;
        }
        estadoJuego = await respuesta.json();
        inicializarPartida();
    } catch (error) {
        console.error('error al cargar el estado del juego:', error);
    }
}

// inicializo la partida con los datos recibidos de la api
function inicializarPartida() {
    if (!estadoJuego || !estadoJuego.jugadores) {
        window.location.href = '/Secretoooooo_AA/setup.php';
        return;
    }

    // si la partida acaba de empezar la inicializo con las clases php via fetch
    if (estadoJuego.fase === 'inicio') {
        fetch('/Secretoooooo_AA/api/estado_juego.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({
                ...estadoJuego,
                fase:        'jugando',
                turnoActual: 0,
                mazo:        generarMazo(estadoJuego.jugadores.length),
                descarte:    [],
                rondas:      0
            })
        }).then(() => cargarEstado());
        return;
    }

    renderizarMesa();
}

// genero el mazo inicial segun el numero de jugadores
function generarMazo(numJugadores) {
    const cartas = [];

    // añado 4 copias de cada carta normal
    const tipos = [
        { tipo: 'skip',    nombre: 'Skip',           descripcion: 'Salta tu turno sin robar',          icono: '⏭️' },
        { tipo: 'attack',  nombre: 'Attack',          descripcion: 'El siguiente toma 2 turnos',         icono: '⚔️' },
        { tipo: 'nope',    nombre: 'Nope',            descripcion: 'Cancela cualquier carta',            icono: '🙅' },
        { tipo: 'future',  nombre: 'See the Future',  descripcion: 'Ves las 3 cartas del mazo',         icono: '👀' },
        { tipo: 'shuffle', nombre: 'Shuffle',         descripcion: 'Baraja el mazo',                    icono: '🔀' },
        { tipo: 'favor',   nombre: 'Favor',           descripcion: 'Pide una carta a otro jugador',     icono: '🤝' },
        { tipo: 'cat',     nombre: 'Tacocat',         descripcion: 'En pareja roba una carta al rival', icono: '🐱' },
    ];

    tipos.forEach(tipo => {
        for (let i = 0; i < 4; i++) cartas.push({ ...tipo });
    });

    // añado los gatitos explosivos
    for (let i = 0; i < numJugadores - 1; i++) {
        cartas.push({ tipo: 'bomba', nombre: 'Exploding Kitten', descripcion: 'Si la robas sin Defuse explota', icono: '💣' });
    }

    // barajo el mazo
    return cartas.sort(() => Math.random() - 0.5);
}

// reparto las cartas iniciales a cada jugador
function repartirCartas() {
    const defuse = { tipo: 'defuse', nombre: 'Defuse', descripcion: 'Desactiva el gatito', icono: '🔧' };

    estadoJuego.jugadores = estadoJuego.jugadores.map(jugador => ({
        ...jugador,
        mano:      [defuse, ...estadoJuego.mazo.splice(0, 7)],
        eliminado: false
    }));
}

// renderizo toda la mesa de juego
function renderizarMesa() {
    const jugadorActual = estadoJuego.jugadores[estadoJuego.turnoActual];

    // actualizo el indicador de turno
    document.getElementById('indicador-turno').textContent = `Turno de ${jugadorActual.nombre}`;
    document.getElementById('info-mazo').textContent       = `${estadoJuego.mazo.length} cartas en el mazo`;
    document.getElementById('contadorMazo').textContent    = estadoJuego.mazo.length;
    document.getElementById('etiquetaMano').textContent    = `Tu mano — ${jugadorActual.nombre}`;

    renderizarRivales();
    renderizarMano(jugadorActual);
    renderizarEliminados();
}

// renderizo los chips de los rivales en la barra superior
function renderizarRivales() {
    const contenedor = document.getElementById('rivales');
    contenedor.innerHTML = '';

    estadoJuego.jugadores.forEach((jugador, indice) => {
        if (indice === estadoJuego.turnoActual || jugador.eliminado) return;
        const chip = document.createElement('div');
        chip.className   = 'chip-rival';
        chip.textContent = `${jugador.nombre} (${jugador.mano ? jugador.mano.length : 0} 🂠)`;
        contenedor.appendChild(chip);
    });
}

// renderizo las cartas de la mano del jugador actual
function renderizarMano(jugador) {
    const contenedor = document.getElementById('cartasMano');
    contenedor.innerHTML = '';

    if (!jugador.mano) return;

    jugador.mano.forEach((carta, indice) => {
        const div = document.createElement('div');
        div.className   = `carta-mano carta-${carta.tipo}`;
        div.title       = carta.descripcion;
        div.innerHTML   = `<span class="icono-carta">${carta.icono}</span><span class="nombre-carta-mano">${carta.nombre}</span>`;
        div.onclick     = () => jugarCarta(indice);
        contenedor.appendChild(div);
    });
}

// renderizo la lista de jugadores eliminados
function renderizarEliminados() {
    const contenedor = document.getElementById('listaEliminados');
    const eliminados = estadoJuego.jugadores.filter(j => j.eliminado);

    if (eliminados.length === 0) {
        contenedor.innerHTML = '<span class="sin-eliminados">Nadie ha explotado aún 😅</span>';
        return;
    }

    contenedor.innerHTML = eliminados.map(j => `<span class="chip-eliminado">💀 ${j.nombre}</span>`).join('');
}

// juego una carta de la mano del jugador actual
async function jugarCarta(indice) {
    const jugador = estadoJuego.jugadores[estadoJuego.turnoActual];
    const carta   = jugador.mano[indice];

    if (!carta) return;

    // elimino la carta de la mano
    jugador.mano.splice(indice, 1);

    // añado la carta al descarte
    estadoJuego.descarte.push(carta);

    // actualizo el descarte visualmente
    document.getElementById('cartaDescarte').innerHTML = `
        <span class="icono-descarte">${carta.icono}</span>
        <span class="nombre-descarte">${carta.nombre}</span>
    `;

    // muestro el efecto de la carta en el log
    mostrarLog(efectoCarta(carta));

    // guardo el estado actualizado
    await guardarEstado();
    renderizarMesa();
}

// devuelvo el mensaje del efecto de cada carta
function efectoCarta(carta) {
    const efectos = {
        skip:    '⏭️ ¡Skip! Tu turno termina sin robar.',
        attack:  '⚔️ ¡Attack! El siguiente jugador toma 2 turnos.',
        nope:    '🙅 ¡Nope! Cancelas la última acción.',
        future:  `👀 Las 3 cartas del tope son: ${estadoJuego.mazo.slice(0, 3).map(c => c.icono).join(' ')}`,
        shuffle: '🔀 Barajas el mazo.',
        favor:   '🤝 Pides una carta a otro jugador.',
        cat:     '🐱 Tacocat jugado — necesitas una pareja.',
        defuse:  '🔧 Guardas el Defuse para cuando lo necesites.',
    };
    return efectos[carta.tipo] || 'Carta jugada.';
}

// robo la carta del tope del mazo
async function robarCarta() {
    if (estadoJuego.mazo.length === 0) {
        mostrarLog('¡El mazo está vacío!');
        return;
    }

    const carta   = estadoJuego.mazo.shift();
    estadoJuego.rondas++;

    // compruebo si la carta es una bomba
    if (carta.tipo === 'bomba') {
        const jugador    = estadoJuego.jugadores[estadoJuego.turnoActual];
        const indiceDefuse = jugador.mano ? jugador.mano.findIndex(c => c.tipo === 'defuse') : -1;

        if (indiceDefuse !== -1) {
            // el jugador tiene defuse — se salva
            jugador.mano.splice(indiceDefuse, 1);
            estadoJuego.descarte.push(carta);
            mostrarLog('💣 ¡Exploding Kitten! Usas tu Defuse... ¡Salvado! 😮‍💨');
            // devuelvo la bomba al mazo en una posicion aleatoria
            const posicion = Math.floor(Math.random() * estadoJuego.mazo.length);
            estadoJuego.mazo.splice(posicion, 0, carta);
        } else {
            // el jugador no tiene defuse — explota
            jugador.eliminado  = true;
            jugador.exploto    = true;
            estadoJuego.descarte.push(carta);
            mostrarExplosion(jugador.nombre);
        }
    } else {
        // carta normal — la añado a la mano del jugador
        const jugador = estadoJuego.jugadores[estadoJuego.turnoActual];
        if (!jugador.mano) jugador.mano = [];
        jugador.mano.push(carta);
        mostrarLog(`Robas: ${carta.nombre} ${carta.icono} — añadida a tu mano.`);
        siguienteTurno();
    }

    await guardarEstado();

    // compruebo si la partida ha terminado
    const activos = estadoJuego.jugadores.filter(j => !j.eliminado);
    if (activos.length === 1) {
        terminarPartida(activos[0]);
        return;
    }

    renderizarMesa();
}

// paso al siguiente turno saltando eliminados
function siguienteTurno() {
    do {
        estadoJuego.turnoActual = (estadoJuego.turnoActual + 1) % estadoJuego.jugadores.length;
    } while (estadoJuego.jugadores[estadoJuego.turnoActual].eliminado);
}

// muestro la animacion de explosion y elimino al jugador
function mostrarExplosion(nombre) {
    const overlay = document.getElementById('explosionOverlay');
    overlay.classList.add('visible');
    mostrarLog(`💥 ¡${nombre} ha EXPLOTADO! ¡KABOOM!`);

    // reproduzco el sonido de explosion si existe
    const sonido = new Audio('/Secretoooooo_AA/assets/sounds/explosion.mp3');
    sonido.play().catch(() => {});

    setTimeout(() => {
        overlay.classList.remove('visible');
        siguienteTurno();
        renderizarMesa();
    }, 2000);
}

// muestro un mensaje en el log del juego
function mostrarLog(mensaje) {
    document.getElementById('logJuego').textContent = mensaje;
}

// guardo el estado actual en la api
async function guardarEstado() {
    try {
        await fetch('/Secretoooooo_AA/api/estado_juego.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(estadoJuego)
        });
    } catch (error) {
        console.error('error al guardar el estado:', error);
    }
}

// termino la partida y la guardo en la base de datos
async function terminarPartida(ganador) {
    mostrarLog(`🏆 ¡${ganador.nombre} ha ganado la partida!`);

    try {
        await fetch('/Secretoooooo_AA/api/partidas.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({
                ganador:   ganador.nombre,
                rondas:    estadoJuego.rondas,
                jugadores: estadoJuego.jugadores.map(j => ({
                    nombre:  j.nombre,
                    exploto: j.exploto ? 1 : 0
                }))
            })
        });

        // elimino la partida de la sesion
        await fetch('/Secretoooooo_AA/api/estado_juego.php', { method: 'DELETE' });

        // redirijo al historial despues de 3 segundos
        setTimeout(() => {
            window.location.href = '/Secretoooooo_AA/historial.php';
        }, 3000);

    } catch (error) {
        console.error('error al guardar la partida:', error);
    }
}






// inicio todo al cargar la pagina
cargarEstado();

