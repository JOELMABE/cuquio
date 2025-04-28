<?php
// lote.php - Lotería Mexicana Deluxe adapted from lote.py structure
// This PHP file serves a web page that replicates the Lotería game functionality using PHP, HTML, CSS, and JavaScript.

// Card names array (same as in lote.py)
$cartas = [
    'EL GALLO', 'EL DIABLO', 'LA DAMA', 'EL CATRIN', 'EL PARAGUAS', 'LA SIRENA', 'LA ESCALERA',
    'LA BOTELLA', 'EL BARRIL', 'EL ARBOL', 'EL MELON', 'EL VALIENTE', 'EL GORRITO', 'LA MUERTE',
    'LA PERA', 'LA BANDERA', 'EL BANDOLON', 'EL VIOLONCELLO', 'LA GARZA', 'EL PAJARO', 'LA MANO',
    'LA BOTA', 'LA LUNA', 'EL COTORRO', 'EL BORRACHO', 'EL NEGRITO', 'EL CORAZON', 'LA SANDIA',
    'EL TAMBOR', 'EL CAMARON', 'LAS JARAS', 'EL MUSICO', 'LA ARAÑA', 'EL SOLDADO', 'LA ESTRELLA',
    'EL CAZO', 'EL MUNDO', 'EL APACHE', 'EL NOPAL', 'EL ALACRAN', 'LA ROSA', 'LA CALAVERA',
    'LA CAMPANA', 'EL CANTARITO', 'EL VENADO', 'EL SOL', 'LA CORONA', 'LA CHALUPA', 'EL PINO',
    'EL PESCADO', 'LA PALMA', 'LA MACETA', 'EL ARPA', 'LA RANA'
];

// Function to remove accents and spaces for filenames
function remove_accents($string) {
    $unwanted_array = array(
        'Á'=>'A', 'É'=>'E', 'Í'=>'I', 'Ó'=>'O', 'Ú'=>'U',
        'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
        'Ñ'=>'N', 'ñ'=>'n', ' '=>'_'
    );
    return strtr($string, $unwanted_array);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎴 Lotería Mexicana Deluxe 🎉</title>
    <style>
        body {
            background-color: #fef9f5;
            font-family: "Comic Sans MS", cursive, sans-serif;
            color: #22223b;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            font-size: 2.5rem;
            margin: 20px 0 10px 0;
        }
        #card-image {
            width: 200px;
            height: 270px;
            border: 2px solid #7209b7;
            border-radius: 8px;
            margin: 10px 0;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }
        #card-name {
            font-size: 1.5rem;
            color: #f72585;
            margin-bottom: 20px;
            min-height: 2em;
        }
        #thumbnails {
            display: flex;
            overflow-x: auto;
            padding: 10px;
            width: 100%;
            max-width: 600px;
            background-color: #fef9f5;
            border-bottom: 2px solid #7209b7;
        }
        #thumbnails img {
            width: 60px;
            height: 80px;
            margin-right: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        #buttons {
            margin: 20px 0;
        }
        button {
            background-color: #7209b7;
            color: white;
            border: none;
            padding: 10px 16px;
            margin: 0 5px;
            font-weight: bold;
            font-family: "Comic Sans MS", cursive, sans-serif;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #b5179e;
        }
        #missing-cards-modal {
            display: none;
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            max-width: 700px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            padding: 20px;
            z-index: 1000;
        }
        #missing-cards-modal h2 {
            margin-top: 0;
            color: #22223b;
        }
        #missing-cards-list {
            display: flex;
            overflow-x: auto;
            padding: 10px 0;
        }
        #missing-cards-list img {
            width: 100px;
            height: 135px;
            margin-right: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        #modal-close {
            background-color: #f72585;
            margin-top: 10px;
        }
        /* Scrollbar styling */
        #thumbnails::-webkit-scrollbar, #missing-cards-list::-webkit-scrollbar {
            height: 8px;
        }
        #thumbnails::-webkit-scrollbar-thumb, #missing-cards-list::-webkit-scrollbar-thumb {
            background-color: #7209b7;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>🎴 Lotería Mexicana 🎉</h1>
    <div id="card-image"></div>
    <div id="card-name"></div>
    <div id="card-count" style="font-size: 1.2rem; color: #22223b; margin-bottom: 10px;">Cartas mostradas: 0</div>

    <div id="thumbnails">
        <!-- Thumbnails of drawn cards will appear here dynamically -->
    </div>

    <div id="buttons">
        <button id="start-btn">Comenzar</button>
        <button id="pause-btn">Pausar</button>
        <button id="verify-btn">Verificar</button>
        <button id="restart-btn">Reiniciar</button>
    </div>

    <div id="missing-cards-modal">
        <h2>Cartas Faltantes:</h2>
        <div id="missing-cards-list"></div>
        <button id="modal-close">Cerrar</button>
    </div>

    <script>
        const cartas = <?php echo json_encode($cartas); ?>;
        let cartasBarajadas = [];
        let indice = 0;
        let jugando = false;
        let pausa = false;

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

        function removeAccents(str) {
            const accents = {'Á':'A','É':'E','Í':'I','Ó':'O','Ú':'U','á':'a','é':'e','í':'i','ó':'o','ú':'u','Ñ':'N','ñ':'n',' ':'_'};
            return str.split('').map(c => accents[c] || c).join('');
        }

        function mostrarCarta() {
            if (!jugando || pausa) return;
            if (indice >= cartasBarajadas.length) {
                alert('¡Todas las cartas se han mostrado!');
                jugando = false;
                return;
            }
            const carta = cartasBarajadas[indice];
            const index = cartas.indexOf(carta);
            const fileName = (index + 1) + '_' + removeAccents(carta) + '.jpg';
            const imgPath = 'cartas/' + fileName;

            cardImageDiv.style.backgroundImage = 'url(' + imgPath + ')';
            cardNameDiv.textContent = carta;

            // Update card count display
            cardCountDiv.textContent = 'Cartas mostradas: ' + (indice + 1);

            // Add thumbnail of the drawn card dynamically
            const thumbnailsDiv = document.getElementById('thumbnails');
            const imgThumb = document.createElement('img');
            imgThumb.src = imgPath;
            imgThumb.alt = carta;
            imgThumb.title = carta;
            thumbnailsDiv.appendChild(imgThumb);

            // Scroll thumbnails container to show the latest card
            thumbnailsDiv.scrollLeft = thumbnailsDiv.scrollWidth;

            // Speech synthesis
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(carta);
                utterance.lang = 'es-ES';
                window.speechSynthesis.cancel();
                window.speechSynthesis.speak(utterance);
            }

            indice++;
            setTimeout(mostrarCarta, 2000);
        }

        startBtn.addEventListener('click', () => {
            cartasBarajadas = cartas.slice().sort(() => Math.random() - 0.5);
            indice = 0;
            jugando = true;
            pausa = false;
            pauseBtn.textContent = 'Pausar';

            // Clear thumbnails on start
            const thumbnailsDiv = document.getElementById('thumbnails');
            thumbnailsDiv.innerHTML = '';

            // Reset card count display
            cardCountDiv.textContent = 'Cartas mostradas: 0';

            mostrarCarta();
        });

        pauseBtn.addEventListener('click', () => {
            if (!jugando) return;
            pausa = !pausa;
            pauseBtn.textContent = pausa ? 'Reanudar' : 'Pausar';
            if (!pausa) {
                mostrarCarta();
            }
        });

        restartBtn.addEventListener('click', () => {
            if (!jugando) return;
            indice = 0;
            cartasBarajadas = cartas.slice().sort(() => Math.random() - 0.5);
            pausa = false;
            pauseBtn.textContent = 'Pausar';

            // Clear thumbnails on restart
            const thumbnailsDiv = document.getElementById('thumbnails');
            thumbnailsDiv.innerHTML = '';

            // Reset card count display
            cardCountDiv.textContent = 'Cartas mostradas: 0';

            mostrarCarta();
        });

        verifyBtn.addEventListener('click', () => {
            if (!jugando) {
                alert('¡Inicia el juego primero!');
                return;
            }
            const mostradas = new Set(cartasBarajadas.slice(0, indice));
            const faltantes = cartas.filter(carta => !mostradas.has(carta));

            if (faltantes.length === 0) {
                alert('¡Felicidades! ¡Ya han salido todas las cartas!');
                return;
            }

            // Clear previous
            missingCardsList.innerHTML = '';
            faltantes.forEach(carta => {
                const index = cartas.indexOf(carta);
                const fileName = (index + 1) + '_' + removeAccents(carta) + '.jpg';
                const imgPath = 'cartas/' + fileName;
                const img = document.createElement('img');
                img.src = imgPath;
                img.alt = carta;
                img.title = carta;
                missingCardsList.appendChild(img);
            });

            missingCardsModal.style.display = 'block';
        });

        modalCloseBtn.addEventListener('click', () => {
            missingCardsModal.style.display = 'none';
        });
    </script>
</body>
</html>