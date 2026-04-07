<?php
// incluyo la cabecera
require_once 'includes/header.php';
require_once 'clases/Carta.php';
require_once 'clases/Jugador.php';
require_once 'clases/Partida.php';
?>

<main>
    <section class="mesa-juego" id="mesaJuego">

        <div class="barra-superior">
            <div class="info-turno">
                <span id="indicador-turno">Cargando partida...</span>
                <span id="info-mazo">-- cartas en el mazo</span>
            </div>
            <div class="rivales" id="rivales"></div>
        </div>

        <div class="zona-central">
            <div class="pila-mazo">
                <p class="etiqueta-pila">Mazo</p>
                <div class="carta-mazo" id="cartaMazo" onclick="robarCarta()">
                    <span>🂠</span>
                    <span class="contador-mazo" id="contadorMazo">--</span>
                </div>
            </div>

            <div class="log-juego" id="logJuego">
                Haz clic en una carta para jugarla, o roba del mazo al final de tu turno.
            </div>

            <div class="pila-descarte">
                <p class="etiqueta-pila">Descarte</p>
                <div class="carta-descarte" id="cartaDescarte">
                    <span>Vacío</span>
                </div>
            </div>
        </div>

        <div class="mano-jugador">
            <p class="etiqueta-mano" id="etiquetaMano">Tu mano</p>
            <div class="cartas-mano" id="cartasMano"></div>
        </div>

        <div class="barra-eliminados">
            <span class="etiqueta-eliminados">Eliminados:</span>
            <div id="listaEliminados">
                <span class="sin-eliminados">Nadie ha explotado aún 😅</span>
            </div>
        </div>

        <div class="explosion-overlay" id="explosionOverlay">
            <span class="emoji-explosion">💥</span>
        </div>

    </section>
</main>

<script src="/Secretoooooo_AA/assets/js/juego.js"></script>

<?php
require_once 'includes/footer.php';
?>