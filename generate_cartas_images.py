from PIL import Image, ImageDraw, ImageFont
import os

# Crear carpeta cartas si no existe
os.makedirs('cartas', exist_ok=True)

# Lista de nombres de las cartas
CARTAS = [
    'El Gallo', 'El Diablo', 'La Dama', 'El Catrín', 'El Paraguas', 'La Sirena', 'La Escalera',
    'La Botella', 'El Barril', 'El Árbol', 'El Melón', 'El Valiente', 'El Gorrito', 'La Muerte',
    'La Pera', 'La Bandera', 'El Bandolón', 'El Violoncello', 'La Garza', 'El Pájaro', 'La Mano',
    'La Bota', 'La Luna', 'El Cotorro', 'El Borracho', 'El Negrito', 'El Corazón', 'La Sandía',
    'El Tambor', 'El Camarón', 'Las Jaras', 'El Músico', 'La Araña', 'El Soldado', 'La Estrella',
    'El Cazo', 'El Mundo', 'El Apache', 'El Nopal', 'El Alacrán', 'La Rosa', 'La Calavera',
    'La Campana', 'El Cantarito', 'El Venado', 'El Sol', 'La Corona', 'La Chalupa', 'El Pino',
    'El Pescado', 'La Palma', 'La Maceta', 'El Arpa', 'La Rana'
]

# Fuente para el texto (puedes cambiar la ruta a una fuente que tengas)
try:
    font = ImageFont.truetype("arial.ttf", 40)
except IOError:
    font = ImageFont.load_default()

# Crear imágenes simples con el nombre de la carta
for carta in CARTAS:
    img = Image.new('RGB', (300, 400), color=(255, 255, 255))
    d = ImageDraw.Draw(img)
    text = carta
    bbox = d.textbbox((0, 0), text, font=font)
    w = bbox[2] - bbox[0]
    h = bbox[3] - bbox[1]
    d.text(((300 - w) / 2, (400 - h) / 2), text, fill=(0, 0, 0), font=font)
    img.save(f'cartas/{carta}.png')

print("Imágenes de cartas generadas en la carpeta 'cartas'.")
