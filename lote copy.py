import tkinter as tk
from tkinter import messagebox, ttk, Toplevel
from PIL import Image, ImageTk
import os
import random
import pyttsx3
import unicodedata

# Configuración de la voz
driver = pyttsx3.init()
driver.setProperty('rate', 150)
voices = driver.getProperty('voices')
for voice in voices:
    if 'spanish' in voice.name.lower() or 'es_' in voice.id.lower():
        driver.setProperty('voice', voice.id)
        break

CARDS_FOLDER = 'cartas'

def remove_accents(input_str):
    nfkd_form = unicodedata.normalize('NFKD', input_str)
    return "".join([c for c in nfkd_form if not unicodedata.combining(c)])

CARTAS = [
    'EL GALLO', 'EL DIABLO', 'LA DAMA', 'EL CATRIN', 'EL PARAGUAS', 'LA SIRENA', 'LA ESCALERA',
    'LA BOTELLA', 'EL BARRIL', 'EL ARBOL', 'EL MELON', 'EL VALIENTE', 'EL GORRITO', 'LA MUERTE',
    'LA PERA', 'LA BANDERA', 'EL BANDOLON', 'EL VIOLONCELLO', 'LA GARZA', 'EL PAJARO', 'LA MANO',
    'LA BOTA', 'LA LUNA', 'EL COTORRO', 'EL BORRACHO', 'EL NEGRITO', 'EL CORAZON', 'LA SANDIA',
    'EL TAMBOR', 'EL CAMARON', 'LAS JARAS', 'EL MUSICO', 'LA ARAÑA', 'EL SOLDADO', 'LA ESTRELLA',
    'EL CAZO', 'EL MUNDO', 'EL APACHE', 'EL NOPAL', 'EL ALACRAN', 'LA ROSA', 'LA CALAVERA',
    'LA CAMPANA', 'EL CANTARITO', 'EL VENADO', 'EL SOL', 'LA CORONA', 'LA CHALUPA', 'EL PINO',
    'EL PESCADO', 'LA PALMA', 'LA MACETA', 'EL ARPA', 'LA RANA'
]

class LoteriaApp:
    def __init__(self, root):
        self.root = root
        self.root.title("🎴 Lotería Mexicana Deluxe 🎉")
        self.root.geometry("500x640")
        self.root.resizable(False, False)
        self.root.configure(bg="#fef9f5")

        # Estilos
        self.font_title = ("Comic Sans MS", 24, "bold")
        self.font_label = ("Comic Sans MS", 16)
        self.font_button = ("Comic Sans MS", 12, "bold")
        self.primary_color = "#22223b"
        self.accent_color = "#f72585"
        self.btn_bg = "#7209b7"

        self.main_frame = tk.Frame(self.root, bg="#fef9f5")
        self.main_frame.pack(fill=tk.BOTH, expand=True)

        self.label_title = tk.Label(self.main_frame, text="🎴 Lotería Mexicana 🎉", font=self.font_title, fg=self.primary_color, bg="#fef9f5")
        self.label_title.pack(pady=(10, 5))

        self.label_imagen = tk.Label(self.main_frame, bg="#fef9f5")
        self.label_imagen.pack(pady=5)

        self.label_nombre = tk.Label(self.main_frame, text="", font=self.font_label, fg=self.accent_color, bg="#fef9f5")
        self.label_nombre.pack(pady=2)

        # Miniaturas
        self.frame_cartas = tk.Frame(self.main_frame, bg="#fef9f5", height=120)
        self.frame_cartas.pack(pady=5, fill=tk.X)
        self.canvas_cartas = tk.Canvas(self.frame_cartas, bg="#fef9f5", height=120, highlightthickness=0)
        self.scrollbar_cartas = ttk.Scrollbar(self.frame_cartas, orient=tk.HORIZONTAL, command=self.canvas_cartas.xview)
        self.canvas_cartas.configure(xscrollcommand=self.scrollbar_cartas.set)
        self.scrollbar_cartas.pack(side=tk.BOTTOM, fill=tk.X)
        self.canvas_cartas.pack(side=tk.TOP, fill=tk.BOTH, expand=True)
        self.inner_frame = tk.Frame(self.canvas_cartas, bg="#fef9f5")
        self.canvas_cartas.create_window((0, 0), window=self.inner_frame, anchor="nw")

        self.thumbnails = {}
        for carta in CARTAS:
            file = f"{CARTAS.index(carta)+1}_{remove_accents(carta).replace(' ', '_')}.jpg"
            path = os.path.join(CARDS_FOLDER, file)
            if os.path.exists(path):
                img = Image.open(path).resize((60, 80), Image.LANCZOS)
                self.thumbnails[carta] = ImageTk.PhotoImage(img)
                tk.Label(self.inner_frame, image=self.thumbnails[carta], bg="#fef9f5").pack(side=tk.LEFT, padx=4)
        self.inner_frame.update_idletasks()
        self.canvas_cartas.config(scrollregion=self.canvas_cartas.bbox("all"))

        # Botones
        self.frame_botones = tk.Frame(self.main_frame, bg="#fef9f5")
        self.frame_botones.pack(side=tk.BOTTOM, pady=10)

        self.btn_inicio = self.crear_boton("Comenzar", self.iniciar_juego, 0)
        self.btn_verificar = self.crear_boton("Verificar", self.verificar_carton, 1)
        self.btn_pausa = self.crear_boton("Pausar", self.toggle_pausa, 2)
        self.btn_reiniciar = self.crear_boton("Reiniciar", self.reiniciar_juego, 3)

        self.imagenes = {}
        for carta in CARTAS:
            file = f"{CARTAS.index(carta)+1}_{remove_accents(carta).replace(' ', '_')}.jpg"
            path = os.path.join(CARDS_FOLDER, file)
            if os.path.exists(path):
                img = Image.open(path).resize((200, 270), Image.LANCZOS)
                self.imagenes[carta] = ImageTk.PhotoImage(img)

        self.cartas_barajadas = []
        self.indice = 0
        self.jugando = False
        self.after_id = None

    def crear_boton(self, texto, comando, columna):
        btn = tk.Button(
            self.frame_botones,
            text=texto,
            command=comando,
            font=self.font_button,
            fg="white",
            bg=self.btn_bg,
            activebackground="#b5179e",
            activeforeground="white",
            bd=0,
            padx=10,
            pady=6,
            relief=tk.FLAT
        )
        btn.grid(row=0, column=columna, padx=5)
        return btn

    def iniciar_juego(self):
        self.cartas_barajadas = random.sample(CARTAS, len(CARTAS))
        self.indice = 0
        self.jugando = True
        self.btn_pausa.config(text="Pausar")
        self.mostrar_carta()

    def mostrar_carta(self):
        if not self.jugando or self.indice >= len(self.cartas_barajadas):
            if self.indice >= len(self.cartas_barajadas):
                messagebox.showinfo("Fin", "¡Todas las cartas se han mostrado!")
                self.jugando = False
            return
        carta = self.cartas_barajadas[self.indice]
        img = self.imagenes.get(carta)
        if img:
            self.label_imagen.config(image=img)
            self.label_imagen.image = img
        self.label_nombre.config(text=carta)
        driver.say(carta)
        driver.runAndWait()
        self.indice += 1
        self.after_id = self.root.after(2000, self.mostrar_carta)

    def toggle_pausa(self):
        if self.jugando:
            self.jugando = False
            if self.after_id:
                self.root.after_cancel(self.after_id)
            self.btn_pausa.config(text="Reanudar")
        else:
            self.jugando = True
            self.btn_pausa.config(text="Pausar")
            self.mostrar_carta()

    def reiniciar_juego(self):
        if self.after_id:
            self.root.after_cancel(self.after_id)
        self.iniciar_juego()

    def verificar_carton(self):
        if not self.cartas_barajadas:
            messagebox.showinfo("Info", "¡Inicia el juego primero!")
            return
        mostradas = set(self.cartas_barajadas[:self.indice])
        faltantes = [c for c in CARTAS if c not in mostradas]
        if not faltantes:
            messagebox.showinfo("¡Felicidades!", "¡Ya han salido todas las cartas!")
            return

        ventana_faltantes = Toplevel(self.root)
        ventana_faltantes.title("Cartas Faltantes")
        ventana_faltantes.geometry("600x300")
        ventana_faltantes.configure(bg="white")
        tk.Label(ventana_faltantes, text="Cartas Faltantes:", font=self.font_label, bg="white").pack(pady=10)
        frame_container = tk.Frame(ventana_faltantes, bg="white")
        frame_container.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        canvas = tk.Canvas(frame_container, bg="white", height=200)
        scrollbar = ttk.Scrollbar(frame_container, orient=tk.HORIZONTAL, command=canvas.xview)
        canvas.configure(xscrollcommand=scrollbar.set)
        scrollbar.pack(side=tk.BOTTOM, fill=tk.X)
        canvas.pack(side=tk.TOP, fill=tk.BOTH, expand=True)
        frame_faltantes = tk.Frame(canvas, bg="white")
        canvas.create_window((0, 0), window=frame_faltantes, anchor="nw")

        self.faltantes_images = []
        for carta in faltantes:
            file = f"{CARTAS.index(carta)+1}_{remove_accents(carta).replace(' ', '_')}.jpg"
            path = os.path.join(CARDS_FOLDER, file)
            if os.path.exists(path):
                img = Image.open(path).resize((100, 135), Image.LANCZOS)
                photo_img = ImageTk.PhotoImage(img)
                self.faltantes_images.append(photo_img)
                tk.Label(frame_faltantes, image=photo_img, bg="white").pack(side=tk.LEFT, padx=5)

        frame_faltantes.update_idletasks()
        canvas.config(scrollregion=canvas.bbox("all"))

# Ejecutar
if __name__ == "__main__":
    root = tk.Tk()
    app = LoteriaApp(root)
    root.mainloop()
