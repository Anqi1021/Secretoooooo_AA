<?php
require_once 'includes/header.php';
?>

<main class="main-landing">

  <!-- ── PANEL 1 · HERO ───────────────────────────────────────── -->
<div class="panel p1">
    <div class="hero-bg-img"></div>
    <div class="hero-overlay"></div>
    <div class="panel-inner p1-inner">
        <h1 class="hero-titulo">No explotes.<br>Gana.</h1>
        <p class="hero-sub">Roba con cuidado. Juega con astucia.<br>Un Exploding Kitten puede acabar con todo en un segundo.</p>
        <div class="hero-btns">
            <a href="setup.php"     class="boton-primario">Empezar a jugar</a>
            <a href="historial.php" class="boton-secundario-hero">Ver historial</a>
        </div>
        <div class="hero-scroll-hint">
            <span>Scroll</span>
            <svg width="16" height="20" viewBox="0 0 16 20" fill="none">
                <rect x="1" y="1" width="14" height="18" rx="7" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"/>
                <rect x="6.5" y="4" width="3" height="5" rx="1.5" fill="rgba(255,255,255,0.5)"/>
            </svg>
        </div>
    </div>
</div>
<div class="spacer"></div>

    <!-- ── PANEL 2 · LAS CARTAS ─────────────────────────────────── -->
    <div class="panel p2">
        <div class="panel-inner p2-inner">

            <div class="p2-header">
                <h2 class="p2-titulo">Las cartas</h2>
                <p class="p2-subtitulo">Cada carta tiene un poder distinto. Algunas te salvan la vida, otras se la arruinan al resto. Aprende qué tienes en la mano antes de que sea demasiado tarde.</p>
            </div>

            <div class="carrusel-wrapper">
                <div class="carrusel-track" id="carruselTrack">
                    <div class="carta-grande carta-bomba">
                        <div class="carta-grande-icono">💣</div>
                        <div class="carta-grande-info">
                            <p class="carta-grande-nombre">Exploding Kitten</p>
                            <p class="carta-grande-desc">Si la robas sin Defuse, quedas fuera de la partida.</p>
                        </div>
                    </div>
                    <div class="carta-grande carta-defuse">
                        <div class="carta-grande-icono">🔧</div>
                        <div class="carta-grande-info">
                            <p class="carta-grande-nombre">Defuse</p>
                            <p class="carta-grande-desc">Desactiva el gatito y lo devuelves al mazo donde quieras.</p>
                        </div>
                    </div>
                    <div class="carta-grande carta-skip-g">
                        <div class="carta-grande-icono">⏭️</div>
                        <div class="carta-grande-info">
                            <p class="carta-grande-nombre">Skip</p>
                            <p class="carta-grande-desc">Terminas tu turno sin robar carta del mazo.</p>
                        </div>
                    </div>
                    <div class="carta-grande carta-attack-g">
                        <div class="carta-grande-icono">⚔️</div>
                        <div class="carta-grande-info">
                            <p class="carta-grande-nombre">Attack</p>
                            <p class="carta-grande-desc">El siguiente jugador debe tomar 2 turnos seguidos.</p>
                        </div>
                    </div>
                    <div class="carta-grande carta-nope-g">
                        <div class="carta-grande-icono">🙅</div>
                        <div class="carta-grande-info">
                            <p class="carta-grande-nombre">Nope</p>
                            <p class="carta-grande-desc">Cancela cualquier carta jugada por otro jugador.</p>
                        </div>
                    </div>
                    <div class="carta-grande carta-future-g">
                        <div class="carta-grande-icono">👀</div>
                        <div class="carta-grande-info">
                            <p class="carta-grande-nombre">See the Future</p>
                            <p class="carta-grande-desc">Ves en secreto las 3 cartas del tope del mazo.</p>
                        </div>
                    </div>
                    <div class="carta-grande carta-shuffle-g">
                        <div class="carta-grande-icono">🔀</div>
                        <div class="carta-grande-info">
                            <p class="carta-grande-nombre">Shuffle</p>
                            <p class="carta-grande-desc">Baraja el mazo aleatoriamente y mezcla todo.</p>
                        </div>
                    </div>
                    <div class="carta-grande carta-favor-g">
                        <div class="carta-grande-icono">🤝</div>
                        <div class="carta-grande-info">
                            <p class="carta-grande-nombre">Favor</p>
                            <p class="carta-grande-desc">Obligas a otro jugador a darte una carta de su mano.</p>
                        </div>
                    </div>
                    <div class="carta-grande carta-cat-g">
                        <div class="carta-grande-icono">🐱</div>
                        <div class="carta-grande-info">
                            <p class="carta-grande-nombre">Cat Cards</p>
                            <p class="carta-grande-desc">Jugadas en pareja, roban una carta al azar al rival.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="carrusel-pie">
                <div class="carrusel-controles">
                    <button class="btn-carrusel" id="btnPrev" onclick="moverCarrusel(-1)">←</button>
                    <button class="btn-carrusel" id="btnNext" onclick="moverCarrusel(1)">→</button>
                </div>
                <a href="setup.php" class="boton-primario boton-grande">Empezar a jugar 💥</a>
            </div>

        </div>
    </div>
    <div class="spacer"></div>

    <!-- ── PANEL 3 · CÓMO SE JUEGA ──────────────────────────────── -->
    <div class="panel p3" id="panel3">
        <div class="panel-inner p3-inner">

            <div class="p3-header">
                <h2 class="p3-titulo">Cómo se juega</h2>
                <p class="p3-subtitulo">El objetivo es sobrevivir. El último en pie gana.</p>
            </div>

            <div class="p3-regla-content" id="p3ReglaContent">
                <p class="p3-regla-num" id="p3Num">01</p>
                <h3 class="p3-regla-titulo" id="p3Titulo">Reparto inicial</h3>
                <p class="p3-regla-texto" id="p3Texto">Cada jugador recibe 7 cartas más 1 Defuse al inicio de la partida. Nadie sabe qué tiene el resto.</p>
            </div>

            <div class="p3-controles">
                <button class="p3-flecha" id="p3Prev" onclick="cambiarRegla(-1)">←</button>
                <span class="p3-indicador" id="p3Indicador">1 / 4</span>
                <button class="p3-flecha" id="p3Next" onclick="cambiarRegla(1)">→</button>
            </div>

        </div>
    </div>
    <div class="spacer"></div>

    <!-- ── PANEL 4 · ESTADÍSTICAS + CTA ─────────────────────────── -->
<div class="panel p4">
    <div class="panel-inner p4-inner">

        <div class="p4-header">
            <h2 class="p4-titulo">Estadísticas</h2>
            <p class="p4-subtitulo">Cada partida deja huella. Aquí se acumulan las victorias, las explosiones y los momentos más épicos.</p>
        </div>

        <div class="grid-stats">
            <div class="stat">
                <p class="stat-etiqueta">Partidas jugadas</p>
                <p class="stat-valor" id="total-partidas">–</p>
            </div>
            <div class="stat">
                <p class="stat-etiqueta">Explosiones totales</p>
                <p class="stat-valor" id="total-explosiones">–</p>
            </div>
            <div class="stat">
                <p class="stat-etiqueta">Jugador más ganador</p>
                <p class="stat-valor" id="mejor-jugador">–</p>
            </div>
        </div>

        <div class="p4-controles">
            <a href="setup.php"     class="boton-primario boton-grande">Empezar a jugar 💥</a>
            <a href="historial.php" class="boton-secundario-hero boton-grande">Ver historial completo</a>
        </div>

    </div>
</div>
<div class="spacer"></div>

</main>

<script src="/Secretoooooo_AA/assets/js/landing.js"></script>

<?php require_once 'includes/footer.php'; ?>