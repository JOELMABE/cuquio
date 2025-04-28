<?php
// lote.php - ¡Lotería Mexicana Deluxe! 🌮🎉
// Ahora con un 200% más de fiesta y chiles fantasmas 🌶️

// Array de cartas que harán gritar "¡Lotería!" hasta al más tranquilo
$cartas = [
    'EL GALLO', 'EL DIABLO', 'LA DAMA', 'EL CATRIN', 'EL PARAGUAS',
    'LA SIRENA', 'LA ESCALERA', 'LA BOTELLA', 'EL BARRIL', 'EL ARBOL',
    'EL MELON', 'EL VALIENTE', 'EL GORRITO', 'LA MUERTE', 'LA PERA',
    'LA BANDERA', 'EL BANDOLON', 'EL VIOLONCELLO', 'LA GARZA', 'EL PAJARO',
    'LA MANO', 'LA BOTA', 'LA LUNA', 'EL COTORRO', 'EL BORRACHO',
    'EL NEGRITO', 'EL CORAZON', 'LA SANDIA', 'EL TAMBOR', 'EL CAMARON',
    'LAS JARAS', 'EL MUSICO', 'LA ARAÑA', 'EL SOLDADO', 'LA ESTRELLA',
    'EL CAZO', 'EL MUNDO', 'EL APACHE', 'EL NOPAL', 'EL ALACRAN',
    'LA ROSA', 'LA CALAVERA', 'LA CAMPANA', 'EL CANTARITO', 'EL VENADO',
    'EL SOL', 'LA CORONA', 'LA CHALUPA', 'EL PINO', 'EL PESCADO',
    'LA PALMA', 'LA MACETA', 'EL ARPA', 'LA RANA' // ¡Ribbit! 🐸
];

// Función para limpiar nombres como cuando limpias la mesa de juego
function remove_accents($string) {
    $unwanted_array = array(
        'Á'=>'A', 'É'=>'E', 'Í'=>'I', 'Ó'=>'O', 'Ú'=>'U',
        'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
        'Ñ'=>'N', 'ñ'=>'n', ' '=>'_', '¿'=>'?', '¡'=>'!'
    );
    return strtr($string, $unwanted_array);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎴🌮 Lotería Mexicana Deluxe 🌵🎉</title>
    <style>
        body {
            background: #fef9f5 url('data:image/svg+xml,<svg width="100" height="100" transform="rotate(25)" opacity="0.2" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><rect x="0" y="0" width="100" height="100" fill="%23f72585" /><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="%23ffffff" font-size="60">🎴</text></svg>');
            font-family: "Comic Sans MS", cursive, sans-serif;
            color: #22223b;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        h1 {
            font-size: 2.5rem;
            margin: 20px 0 10px 0;
            text-shadow: 2px 2px #f72585;
            animation: titileo 1s infinite alternate;
            background: linear-gradient(45deg, #7209b7, #f72585);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }

        @keyframes titileo {
            from { text-shadow: 2px 2px #f72585; }
            to { text-shadow: 4px 4px #7209b7, -2px -2px #b5179e; }
        }

        #card-image {
            width: 200px;
            height: 270px;
            border: 3px solid #7209b7;
            border-radius: 15px;
            margin: 10px 0;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 8px 15px rgba(114, 9, 183, 0.3);
            background-color: white;
        }

        #card-image:hover {
            transform: rotate(2deg) scale(1.05);
            box-shadow: 0 0 25px #f72585;
        }

        #card-name {
            font-size: 1.5rem;
            color: #f72585;
            margin-bottom: 20px;
            min-height: 2em;
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 50px;
            border: 2px solid #7209b7;
            width: 80%;
            max-width: 400px;
        }

        #thumbnails {
            display: flex;
            overflow-x: auto;
            padding: 10px;
            width: 100%;
            max-width: 800px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            margin: 10px 0;
            box-shadow: inset 0 0 10px rgba(114, 9, 183, 0.2);
        }

        #thumbnails img {
            width: 60px;
            height: 80px;
            margin-right: 8px;
            border-radius: 8px;
            border: 2px solid #7209b7;
            transition: transform 0.3s;
        }

        #thumbnails img:hover {
            transform: scale(1.1);
            z-index: 1;
        }

        #buttons {
            margin: 20px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        button {
            background: linear-gradient(145deg, #7209b7, #b5179e);
            color: white;
            border: none;
            padding: 12px 20px;
            margin: 0 5px;
            font-weight: bold;
            font-family: "Comic Sans MS", cursive, sans-serif;
            font-size: 1rem;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(114, 9, 183, 0.3);
            min-width: 120px;
        }

        button:hover {
            transform: translateY(-2px) rotate(1deg);
            box-shadow: 0 6px 12px rgba(181, 23, 158, 0.4);
            background: linear-gradient(145deg, #b5179e, #7209b7);
        }

        #missing-cards-modal {
            display: none;
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 800px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(247, 37, 133, 0.5);
            padding: 20px;
            z-index: 1000;
            border: 3px solid #f72585;
        }

        #missing-cards-list {
            display: flex;
            overflow-x: auto;
            padding: 10px 0;
            gap: 10px;
        }

        #missing-cards-list img {
            width: 100px;
            height: 135px;
            border-radius: 10px;
            border: 2px solid #7209b7;
            box-shadow: 0 4px 8px rgba(114, 9, 183, 0.2);
        }

        #modal-close {
            background: linear-gradient(145deg, #f72585, #7209b7);
            margin-top: 15px;
        }

        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: #f72585;
            pointer-events: none;
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 2rem;
            }
            #card-image {
                width: 180px;
                height: 240px;
            }
        }
    </style>
</head>
<body>
    <h1>🎴🌮 Lotería Mexicana Deluxe 🌵🎉</h1>
    <div id="card-image"></div>
    <div id="card-name">¡Presiona "Comenzar" para iniciar! 🚀</div>
    <div id="card-count" style="font-size: 1.2rem; color: #22223b; margin-bottom: 10px;">Cartas mostradas: 0</div>

    <div id="thumbnails"></div>

    <div id="buttons">
        <button id="start-btn">🎉 Comenzar</button>
        <button id="pause-btn">⏸️ Pausar</button>
        <button id="verify-btn">🔍 Verificar</button>
        <button id="restart-btn">🔄 Reiniciar</button>
    </div>
    <div id="speed-control" style="margin: 10px 0; text-align: center;">
        <label for="speed-slider" style="font-weight: bold; color: #7209b7;">Velocidad de decir las cartas: <span id="speed-label">2</span> segundos</label><br>
        <input type="range" id="speed-slider" min="1" max="4" value="2" step="1" style="width: 200px; cursor: pointer;">
    </div>

    <div id="missing-cards-modal">
        <h2>🕵️♀️ Cartas Faltantes:</h2>
        <div id="missing-cards-list"></div>
        <button id="modal-close">❌ Cerrar</button>
    </div>

    <script>
        const cartas = <?php echo json_encode($cartas); ?>;
        let cartasBarajadas = [];
        let indice = 0;
        let jugando = false;
        let pausa = false;
        let speed = 2; // default speed in seconds

        const cardImageDiv = document.getElementById('card-image');
        const cardNameDiv = document.getElementById('card-name');
        const cardCountDiv = document.getElementById('card-count');
        const startBtn = document.getElementById('start-btn');
        const pauseBtn = document.getElementById('pause-btn');
        const verifyBtn = document.getElementById('verify-btn');
        const restartBtn = document.getElementById('restart-btn');
        const missingCardsModal = document.getElementById('missing-cards-modal');
        const missingCardsList = document.getElementById('missing-cards-list');
        const modalCloseBtn = document.getElementById('modal-close');
        const speedSlider = document.getElementById('speed-slider');
        const speedLabel = document.getElementById('speed-label');

        function removeAccents(str) {
            const accents = {'Á':'A','É':'E','Í':'I','Ó':'O','Ú':'U','á':'a','é':'e','í':'i','ó':'o','ú':'u','Ñ':'N','ñ':'n',' ':'_'};
            return str.split('').map(c => accents[c] || c).join('');
        }

        speedSlider.addEventListener('input', (e) => {
            speed = parseInt(e.target.value);
            speedLabel.textContent = speed;
        });

        function crearConfetti() {
            for (let i = 0; i < 20; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.animation = `volar ${Math.random() * 3 + 2}s linear`;
                confetti.style.background = `hsl(${Math.random() * 360}, 100%, 50%)`;
                document.body.appendChild(confetti);
                
                setTimeout(() => confetti.remove(), 5000);
            }
        }

        function mostrarCarta() {
            if (!jugando || pausa) return;
            
            // Efecto de carta bailarina 💃
            cardImageDiv.style.transform = 'rotate(0deg)';
            setTimeout(() => cardImageDiv.style.transform = 'rotate(-1deg)', 50);
            setTimeout(() => cardImageDiv.style.transform = 'rotate(1deg)', 150);

            if (indice >= cartasBarajadas.length) {
                alert('🎊 ¡Todas las cartas se han mostrado! 🎉');
                crearConfetti();
                jugando = false;
                return;
            }
            
            const carta = cartasBarajadas[indice];
            const index = cartas.indexOf(carta);
            const fileName = (index + 1) + '_' + removeAccents(carta) + '.jpg';
            const imgPath = 'cartas/' + fileName;

            cardImageDiv.style.backgroundImage = `url(${imgPath})`;
            cardNameDiv.textContent = carta;
            cardCountDiv.textContent = `Cartas mostradas: ${indice + 1}`;

            // Miniaturas de cartas mostradas
            const thumbnailsDiv = document.getElementById('thumbnails');
            const imgThumb = document.createElement('img');
            imgThumb.src = imgPath;
            imgThumb.alt = carta;
            imgThumb.title = carta;
            thumbnailsDiv.appendChild(imgThumb);
            thumbnailsDiv.scrollLeft = thumbnailsDiv.scrollWidth;

            // ¡Voz de anuncio!
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(carta);
                utterance.lang = 'es-ES';
                window.speechSynthesis.cancel();
                window.speechSynthesis.speak(utterance);
            }

        indice++;
        setTimeout(mostrarCarta, speed * 1000);
    }

        // Event Listeners 🎧
        startBtn.addEventListener('click', () => {
            cartasBarajadas = [...cartas].sort(() => Math.random() - 0.5);
            indice = 0;
            jugando = true;
            pausa = false;
            pauseBtn.textContent = '⏸️ Pausar';
            document.getElementById('thumbnails').innerHTML = '';
            cardCountDiv.textContent = 'Cartas mostradas: 0';
            cardNameDiv.textContent = '¡Que empiece la fiesta! 🎊';
            crearConfetti();
            mostrarCarta();
        });

        pauseBtn.addEventListener('click', () => {
            if (!jugando) return;
            pausa = !pausa;
            pauseBtn.textContent = pausa ? '▶️ Reanudar' : '⏸️ Pausar';
            if (!pausa) mostrarCarta();
        });

        restartBtn.addEventListener('click', () => {
            if (!jugando) return;
            indice = 0;
            cartasBarajadas = [...cartas].sort(() => Math.random() - 0.5);
            pausa = false;
            pauseBtn.textContent = '⏸️ Pausar';
            document.getElementById('thumbnails').innerHTML = '';
            cardCountDiv.textContent = 'Cartas mostradas: 0';
            crearConfetti();
            mostrarCarta();
        });

        verifyBtn.addEventListener('click', () => {
            if (!jugando) return alert('¡Primero inicia el juego! 🎮');
            const mostradas = new Set(cartasBarajadas.slice(0, indice));
            const faltantes = cartas.filter(carta => !mostradas.has(carta));
            
            missingCardsList.innerHTML = faltantes.length ? 
                faltantes.map(carta => {
                    const index = cartas.indexOf(carta);
                    const fileName = (index + 1) + '_' + removeAccents(carta) + '.jpg';
                    return `<img src="cartas/${fileName}" alt="${carta}" title="${carta}">`;
                }).join('') : 
                '<p>¡Todas las cartas han salido! 🎉</p>';
            
            missingCardsModal.style.display = 'block';
        });

        modalCloseBtn.addEventListener('click', () => {
            missingCardsModal.style.display = 'none';
        });

        // Animación de confeti CSS 🎊
        const style = document.createElement('style');
        style.textContent = `
            @keyframes volar {
                0% { transform: translateY(0) rotate(0deg); opacity: 1; }
                100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>