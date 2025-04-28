import os
import requests

# URLs de ejemplo para las imágenes de las cartas (deberás reemplazar con URLs válidas)
cartas = [
    'El Gallo', 'El Diablo', 'La Dama', 'El Catrín', 'El Paraguas', 'La Sirena', 'La Escalera',
    'La Botella', 'El Barril', 'El Árbol', 'El Melón', 'El Valiente', 'El Gorrito', 'La Muerte',
    'La Pera', 'La Bandera', 'El Bandolón', 'El Violoncello', 'La Garza', 'El Pájaro', 'La Mano',
    'La Bota', 'La Luna', 'El Cotorro', 'El Borracho', 'El Negrito', 'El Corazón', 'La Sandía',
    'El Tambor', 'El Camarón', 'Las Jaras', 'El Músico', 'La Araña', 'El Soldado', 'La Estrella',
    'El Cazo', 'El Mundo', 'El Apache', 'El Nopal', 'El Alacrán', 'La Rosa', 'La Calavera',
    'La Campana', 'El Cantarito', 'El Venado', 'El Sol', 'La Corona', 'La Chalupa', 'El Pino',
    'El Pescado', 'La Palma', 'La Maceta', 'El Arpa', 'La Rana'
]

# Carpeta destino
folder = 'cartas'
os.makedirs(folder, exist_ok=True)

# Ejemplo de URLs base (deberás reemplazar con URLs reales de imágenes)
base_url = 'https://example.com/cartas/'

for carta in cartas:
    filename = f"{carta}.png"
    url = base_url + filename.replace(' ', '%20')
    filepath = os.path.join(folder, filename)
    try:
        print(f"Descargando {filename}...")
        response = requests.get(url)
        if response.status_code == 200:
            with open(filepath, 'wb') as f:
                f.write(response.content)
            print(f"{filename} descargada correctamente.")
        else:
            print(f"No se pudo descargar {filename}: Código {response.status_code}")
    except Exception as e:
        print(f"Error al descargar {filename}: {e}")
