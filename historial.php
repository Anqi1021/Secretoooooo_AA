<?php
// incluyo la cabecera
require_once 'includes/header.php';
?>

<main>
    <section class="historial">
        <h1>Historial de partidas</h1>

        <div class="stats-globales" id="statsGlobales">
            <div class="stat">
                <p class="stat-etiqueta">Total partidas</p>
                <p class="stat-valor" id="stat-total">-</p>
            </div>
            <div class="stat">
                <p class="stat-etiqueta">Explosiones totales</p>
                <p class="stat-valor" id="stat-explosiones">-</p>
            </div>
            <div class="stat">
                <p class="stat-etiqueta">Partida más larga</p>
                <p class="stat-valor" id="stat-rondas">-</p>
            </div>
            <div class="stat">
                <p class="stat-etiqueta">Jugador más ganador</p>
                <p class="stat-valor" id="stat-mejor">-</p>
            </div>
        </div>

        <h2>Jugadores</h2>
        <div class="grid-jugadores" id="gridJugadores"></div>

        <h2>Últimas partidas</h2>
        <div class="filtros" id="filtros"></div>
        <div class="lista-partidas" id="listaPartidas"></div>
    </section>
</main>

<script>
// cargo el historial completo al cargar la pagina
async function cargarHistorial() {
    try {
        // pido los jugadores y las partidas a la api
        const [resJugadores, resPartidas] = await Promise.all([
            fetch('/Secretoooooo_AA/api/jugadores.php'),
            fetch('/Secretoooooo_AA/api/partidas.php')
        ]);

        const jugadores = await resJugadores.json();
        const partidas  = await resPartidas.json();

        renderizarStats(jugadores, partidas);
        renderizarJugadores(jugadores);
        renderizarFiltros(jugadores, partidas);
        renderizarPartidas(partidas);

    } catch (error) {
        console.error('error al cargar el historial:', error);
    }
}

// renderizo las estadisticas globales
function renderizarStats(jugadores, partidas) {
    const totalExplosiones = jugadores.reduce((acc, j) => acc + j.explosiones, 0);
    const maxRondas        = partidas.reduce((max, p) => Math.max(max, p.rondas), 0);
    const mejorJugador     = [...jugadores].sort((a, b) => b.victorias - a.victorias)[0];

    document.getElementById('stat-total').textContent      = partidas.length;
    document.getElementById('stat-explosiones').textContent = totalExplosiones;
    document.getElementById('stat-rondas').textContent      = maxRondas + ' rondas';
    document.getElementById('stat-mejor').textContent       = mejorJugador ? mejorJugador.nombre + ' 🏆' : '-';
}

// renderizo las tarjetas de jugadores con sus estadisticas
function renderizarJugadores(jugadores) {
    const contenedor = document.getElementById('gridJugadores');
    contenedor.innerHTML = '';

    jugadores.forEach(jugador => {
        const porcentaje = jugador.total_partidas > 0
            ? Math.round((jugador.victorias / jugador.total_partidas) * 100)
            : 0;

        const tarjeta = document.createElement('div');
        tarjeta.className = 'tarjeta-jugador';
        tarjeta.innerHTML = `
            <p class="nombre-jugador">${jugador.nombre}</p>
            <p class="porcentaje-jugador">${porcentaje}% victorias · ${jugador.total_partidas} partidas</p>
            <div class="barra-stat">
                <span class="etiqueta-stat">Victorias</span>
                <div class="track"><div class="fill" style="width:${porcentaje}%"></div></div>
                <span>${jugador.victorias}</span>
            </div>
            <div class="barra-stat">
                <span class="etiqueta-stat">Derrotas</span>
                <div class="track"><div class="fill derrota" style="width:${jugador.total_partidas > 0 ? (jugador.derrotas/jugador.total_partidas)*100 : 0}%"></div></div>
                <span>${jugador.derrotas}</span>
            </div>
            <div class="barra-stat">
                <span class="etiqueta-stat">Explosiones</span>
                <div class="track"><div class="fill explosion" style="width:${jugador.total_partidas > 0 ? (jugador.explosiones/jugador.total_partidas)*100 : 0}%"></div></div>
                <span>${jugador.explosiones}</span>
            </div>
        `;
        contenedor.appendChild(tarjeta);
    });
}

// renderizo los botones de filtro por jugador
function renderizarFiltros(jugadores, partidas) {
    const contenedor = document.getElementById('filtros');
    contenedor.innerHTML = '<button class="btn-filtro activo" onclick="filtrarPartidas(\'todas\', this)">Todas</button>';

    jugadores.forEach(jugador => {
        const btn = document.createElement('button');
        btn.className   = 'btn-filtro';
        btn.textContent = jugador.nombre;
        btn.onclick     = function() { filtrarPartidas(jugador.nombre, this); };
        contenedor.appendChild(btn);
    });

    // guardo las partidas para poder filtrarlas
    window._partidas = partidas;
}

// filtro las partidas por jugador
function filtrarPartidas(filtro, btn) {
    document.querySelectorAll('.btn-filtro').forEach(b => b.classList.remove('activo'));
    btn.classList.add('activo');

    const partidas = filtro === 'todas'
        ? window._partidas
        : window._partidas.filter(p => p.jugadores && p.jugadores.includes(filtro));

    renderizarPartidas(partidas);
}

// renderizo la lista de partidas
function renderizarPartidas(partidas) {
    const contenedor = document.getElementById('listaPartidas');
    contenedor.innerHTML = '';

    if (partidas.length === 0) {
        contenedor.innerHTML = '<p class="sin-partidas">No hay partidas registradas aún.</p>';
        return;
    }

    partidas.forEach(partida => {
        const item = document.createElement('div');
        item.className = 'item-partida';
        item.innerHTML = `
            <span class="fecha-partida">${partida.fecha}</span>
            <span class="ganador-partida">🏆 ${partida.ganador}</span>
            <span class="jugadores-partida">${partida.jugadores || '-'}</span>
            <span class="rondas-partida">${partida.rondas} rondas</span>
        `;
        contenedor.appendChild(item);
    });
}

cargarHistorial();
</script>

<?php
require_once 'includes/footer.php';
?>