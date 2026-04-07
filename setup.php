<?php
// incluyo la cabecera del sitio
require_once 'includes/header.php';
?>

<main>
    <section class="setup">
        <h1>Nueva partida</h1>

        <div class="pasos">
            <span class="paso completado"></span>
            <span class="paso activo"></span>
            <span class="paso"></span>
        </div>

        <h2>¿Cuántos jugadores sois?</h2>

        <div class="selector-numero" id="selectorNumero">
            <button class="btn-numero" data-numero="2" onclick="seleccionarNumero(2)">
                <span class="numero">2</span>
                <span class="etiqueta">Mínimo</span>
            </button>
            <button class="btn-numero seleccionado" data-numero="3" onclick="seleccionarNumero(3)">
                <span class="numero">3</span>
                <span class="etiqueta">Recomendado</span>
            </button>
            <button class="btn-numero" data-numero="4" onclick="seleccionarNumero(4)">
                <span class="numero">4</span>
                <span class="etiqueta">Caótico</span>
            </button>
            <button class="btn-numero" data-numero="5" onclick="seleccionarNumero(5)">
                <span class="numero">5</span>
                <span class="etiqueta">Máximo</span>
            </button>
        </div>

        <div class="formulario-jugadores" id="formularioJugadores"></div>

        <p class="aviso-guardado">
            💾 Sin registro necesario. Escribe los nombres y la app guardará el historial automáticamente.
        </p>

        <div class="acciones-setup">
            <p id="resumen-jugadores"></p>
            <button class="boton-primario" id="btnEmpezar" disabled onclick="empezarPartida()">
                Empezar partida 💥
            </button>
        </div>
    </section>
</main>

<script>
// defino el numero de jugadores seleccionado por defecto
let numeroJugadores = 3;

// los colores y emojis de cada slot de jugador
const avatares = [
    { color: '#fcebeb', emoji: '😼' },
    { color: '#e1f5ee', emoji: '🙀' },
    { color: '#eeedfe', emoji: '😸' },
    { color: '#faeeda', emoji: '😾' },
    { color: '#fbeaf0', emoji: '😹' },
];

// selecciono el numero de jugadores y renderizo los campos
function seleccionarNumero(numero) {
    numeroJugadores = numero;
    document.querySelectorAll('.btn-numero').forEach(btn => {
        btn.classList.toggle('seleccionado', parseInt(btn.dataset.numero) === numero);
    });
    renderizarJugadores();
}

// renderizo los campos de nombre segun el numero de jugadores
function renderizarJugadores() {
    const contenedor = document.getElementById('formularioJugadores');
    contenedor.innerHTML = '';

    for (let i = 0; i < 5; i++) {
        const activo  = i < numeroJugadores;
        const avatar  = avatares[i];
        const slot    = document.createElement('div');
        slot.className = 'slot-jugador' + (activo ? ' activo' : ' inactivo');
        slot.innerHTML = `
            <div class="avatar-jugador" style="background:${avatar.color}">${avatar.emoji}</div>
            <div class="datos-jugador">
                <p class="etiqueta-jugador">Jugador ${i + 1}</p>
                <input 
                    type="text" 
                    class="input-nombre" 
                    data-indice="${i}"
                    placeholder="${activo ? 'Escribe tu nombre...' : 'Slot no disponible'}"
                    ${activo ? '' : 'disabled'}
                    oninput="actualizarResumen()"
                />
            </div>
            ${i === 0 ? '<span class="badge-anfitrion">Anfitrión</span>' : ''}
        `;
        contenedor.appendChild(slot);
    }
    actualizarResumen();
}

// actualizo el resumen de jugadores y activo el boton cuando esten todos rellenos
function actualizarResumen() {
    const inputs   = document.querySelectorAll('.input-nombre:not([disabled])');
    const nombres  = Array.from(inputs).map(i => i.value.trim()).filter(n => n !== '');
    const listos   = nombres.length;
    const btn      = document.getElementById('btnEmpezar');
    const resumen  = document.getElementById('resumen-jugadores');

    resumen.textContent = `${listos} de ${numeroJugadores} jugadores listos`;
    btn.disabled = listos !== numeroJugadores;
}

// envio los jugadores a la api y redirijo a la mesa de juego
async function empezarPartida() {
    const inputs  = document.querySelectorAll('.input-nombre:not([disabled])');
    const nombres = Array.from(inputs).map(i => i.value.trim());

    try {
        // registro cada jugador en la base de datos si no existe
        const jugadores = [];
        for (const nombre of nombres) {
            const respuesta = await fetch('/Secretoooooo_AA/api/jugadores.php', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({ nombre })
            });
            const jugador = await respuesta.json();
            jugadores.push(jugador);
        }

        // guardo los jugadores en el estado del juego via la api
        await fetch('/Secretoooooo_AA/api/estado_juego.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ jugadores, fase: 'inicio' })
        });

        // redirijo a la mesa de juego
        window.location.href = '/Secretoooooo_AA/juego.php';

    } catch (error) {
        console.error('error al empezar la partida:', error);
    }
}

// renderizo los jugadores al cargar la pagina
renderizarJugadores();
</script>

<?php
require_once 'includes/footer.php';
?>