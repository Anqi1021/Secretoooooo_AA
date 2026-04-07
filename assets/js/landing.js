// ── Estadísticas globales ─────────────────────────────────────
async function cargarEstadisticas() {
    try {
        const respuesta = await fetch('/Secretoooooo_AA/api/jugadores.php');
        const jugadores = await respuesta.json();
        const totalPartidas    = jugadores.reduce((acc, j) => acc + j.total_partidas, 0) / (jugadores.length || 1);
        const totalExplosiones = jugadores.reduce((acc, j) => acc + j.explosiones, 0);
        const mejorJugador     = jugadores.sort((a, b) => b.victorias - a.victorias)[0];
        document.getElementById('total-partidas').textContent    = Math.round(totalPartidas);
        document.getElementById('total-explosiones').textContent = totalExplosiones;
        document.getElementById('mejor-jugador').textContent     = mejorJugador ? mejorJugador.nombre + ' 🏆' : '–';
    } catch (error) {
        console.error('error al cargar las estadísticas:', error);
    }
}
cargarEstadisticas();


// ── Carrusel de cartas ────────────────────────────────────────
(function() {
    const track   = document.getElementById('carruselTrack');
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    if (!track) return;

    const VISIBLE   = 3;
    const GAP       = 16;
    const cartas    = track.querySelectorAll('.carta-grande');
    const total     = cartas.length;
    const maxIndice = total - VISIBLE;
    let   indice    = 0;

    function cardWidth() {
        return cartas[0].getBoundingClientRect().width + GAP;
    }

    function actualizar() {
        track.style.transform = `translateX(-${indice * cardWidth()}px)`;
        btnPrev.disabled      = indice === 0;
        btnNext.disabled      = indice >= maxIndice;
    }

    window.moverCarrusel = function(dir) {
        indice = Math.max(0, Math.min(maxIndice, indice + dir));
        actualizar();
    };

    window.addEventListener('resize', actualizar);
    actualizar();
})();


// ── Panel 3 · Reglas con cambio de color ─────────────────────
(function() {
    const REGLAS = [
        {
            num:    '01',
            titulo: 'Reparto inicial',
            texto:  'Cada jugador recibe 7 cartas más 1 Defuse al inicio de la partida. Nadie sabe qué tiene el resto.',
            color:  '#F5E642'
        },
        {
            num:    '02',
            titulo: 'Tu turno',
            texto:  'Puedes jugar cartas de acción o no jugar ninguna. Al final siempre debes robar una carta del mazo.',
            color:  '#F9B4ED'
        },
        {
            num:    '03',
            titulo: 'El peligro',
            texto:  'Si robas un Exploding Kitten sin Defuse, explota y quedas eliminado. Sin segunda oportunidad.',
            color:  '#FCA5A5'
        },
        {
            num:    '04',
            titulo: 'La victoria',
            texto:  'El último jugador vivo gana. Usa tus cartas con cabeza: el mazo es tu mayor enemigo.',
            color:  '#A7F3D0'
        },
    ];

    let indice = 0;
    const panel    = document.getElementById('panel3');
    const content  = document.getElementById('p3ReglaContent');
    const elNum    = document.getElementById('p3Num');
    const elTitulo = document.getElementById('p3Titulo');
    const elTexto  = document.getElementById('p3Texto');
    const elInd    = document.getElementById('p3Indicador');
    const btnPrev  = document.getElementById('p3Prev');
    const btnNext  = document.getElementById('p3Next');
    if (!panel) return;

    function actualizar() {
        content.style.opacity   = '0';
        content.style.transform = 'translateY(10px)';

        setTimeout(() => {
            const r = REGLAS[indice];
            panel.style.background = r.color;
            elNum.textContent      = r.num;
            elTitulo.textContent   = r.titulo;
            elTexto.textContent    = r.texto;
            elInd.textContent      = `${indice + 1} / ${REGLAS.length}`;
            btnPrev.disabled       = indice === 0;
            btnNext.disabled       = indice === REGLAS.length - 1;
            content.style.opacity   = '1';
            content.style.transform = 'translateY(0)';
        }, 220);
    }

    window.cambiarRegla = function(dir) {
        indice = Math.max(0, Math.min(REGLAS.length - 1, indice + dir));
        actualizar();
    };

    actualizar();
})();


