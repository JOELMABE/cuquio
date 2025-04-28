from kivy.app import App
from kivy.uix.boxlayout import BoxLayout
from kivy.uix.image import Image
from kivy.uix.label import Label
from kivy.uix.button import Button
from kivy.uix.scrollview import ScrollView
from kivy.uix.gridlayout import GridLayout
from kivy.core.window import Window
from kivy.clock import Clock
from kivy.properties import StringProperty
from kivy.lang import Builder
from kivy.utils import platform

import os
import random
import unicodedata

# For text to speech on Android
if platform == 'android':
    from jnius import autoclass
    PythonActivity = autoclass('org.kivy.android.PythonActivity')
    Context = autoclass('android.content.Context')
    TextToSpeech = autoclass('android.speech.tts.TextToSpeech')
    Locale = autoclass('java.util.Locale')

    class AndroidTTS:
        def __init__(self):
            self.tts = TextToSpeech(PythonActivity.mActivity, None)
            self.tts.setLanguage(Locale("es", "MX"))

        def speak(self, text):
            self.tts.speak(text, TextToSpeech.QUEUE_FLUSH, None, None)
else:
    # For other platforms, use plyer TTS or dummy
    try:
        from plyer import tts
        class AndroidTTS:
            def __init__(self):
                pass
            def speak(self, text):
                tts.speak(text, lang='es')
    except ImportError:
        class AndroidTTS:
            def __init__(self):
                pass
            def speak(self, text):
                print("TTS:", text)

# Remove accents function
def remove_accents(input_str):
    nfkd_form = unicodedata.normalize('NFKD', input_str)
    return "".join([c for c in nfkd_form if not unicodedata.combining(c)])

# Cards list
CARTAS = [
    'EL GALLO', 'EL DIABLO', 'LA DAMA', 'EL CATRIN', 'EL PARAGUAS', 'LA SIRENA', 'LA ESCALERA',
    'LA BOTELLA', 'EL BARRIL', 'EL ARBOL', 'EL MELON', 'EL VALIENTE', 'EL GORRITO', 'LA MUERTE',
    'LA PERA', 'LA BANDERA', 'EL BANDOLON', 'EL VIOLONCELLO', 'LA GARZA', 'EL PAJARO', 'LA MANO',
    'LA BOTA', 'LA LUNA', 'EL COTORRO', 'EL BORRACHO', 'EL NEGRITO', 'EL CORAZON', 'LA SANDIA',
    'EL TAMBOR', 'EL CAMARON', 'LAS JARAS', 'EL MUSICO', 'LA ARANA', 'EL SOLDADO', 'LA ESTRELLA',
    'EL CAZO', 'EL MUNDO', 'EL APACHE', 'EL NOPAL', 'EL ALACRAN', 'LA ROSA', 'LA CALAVERA',
    'LA CAMPANA', 'EL CANTARITO', 'EL VENADO', 'EL SOL', 'LA CORONA', 'LA CHALUPA', 'EL PINO',
    'EL PESCADO', 'LA PALMA', 'LA MACETA', 'EL ARPA', 'LA RANA'
]

CARDS_FOLDER = 'cartas'

KV = """
<LoteriaLayout>:
    orientation: 'vertical'
    padding: 10
    spacing: 10

    Label:
        id: title_label
        text: "Lotería Mexicana"
        font_size: '24sp'
        size_hint_y: None
        height: self.texture_size[1]

    Image:
        id: card_image
        source: ''
        size_hint: (1, 0.6)
        allow_stretch: True
        keep_ratio: True

    Label:
        id: card_name
        text: ''
        font_size: '20sp'
        size_hint_y: None
        height: self.texture_size[1]
        color: 0.23, 0.51, 0.96, 1

    BoxLayout:
        size_hint_y: None
        height: '48dp'
        spacing: 10

        Button:
            text: 'Comenzar'
            on_press: root.iniciar_juego()

        Button:
            id: pause_button
            text: 'Pausar'
            on_press: root.toggle_pausa()

        Button:
            text: 'Reiniciar'
            on_press: root.reiniciar_juego()

    ScrollView:
        size_hint_y: 0.2
        do_scroll_x: True
        do_scroll_y: False

        GridLayout:
            id: thumbnails_grid
            cols: len(root.cartas)
            size_hint_x: None
            width: self.minimum_width
            height: '100dp'
            spacing: 5
"""

class LoteriaLayout(BoxLayout):
    card_name = StringProperty('')
    cartas = CARTAS

    def __init__(self, **kwargs):
        super().__init__(**kwargs)
        self.tts = AndroidTTS()
        self.cartas_barajadas = []
        self.indice = 0
        self.jugando = False
        self.after_event = None
        self.load_thumbnails()

    def load_thumbnails(self):
        grid = self.ids.thumbnails_grid
        grid.clear_widgets()
        for carta in self.cartas:
            filename = f"{self.cartas.index(carta)+1}_{remove_accents(carta).replace(' ', '_').lower()}.jpg"
            path = os.path.join(CARDS_FOLDER, filename)
            if os.path.exists(path):
                img = Image(source=path, size_hint=(None, None), size=(60, 80))
                grid.add_widget(img)
            else:
                # Add a placeholder label for missing images
                from kivy.uix.label import Label
                placeholder = Label(text='?', size_hint=(None, None), size=(60, 80), color=(1, 0, 0, 1))
                grid.add_widget(placeholder)

    def iniciar_juego(self):
        if self.jugando:
            return
        self.cartas_barajadas = random.sample(self.cartas, len(self.cartas))
        self.indice = 0
        self.jugando = True
        self.ids.pause_button.text = 'Pausar'
        self.mostrar_carta()

    def mostrar_carta(self, *args):
        if not self.jugando or self.indice >= len(self.cartas_barajadas):
            if self.indice >= len(self.cartas_barajadas):
                from kivy.uix.popup import Popup
                from kivy.uix.label import Label
                popup = Popup(title='Fin', content=Label(text='¡Todas las cartas se han mostrado!'), size_hint=(0.6, 0.4))
                popup.open()
                self.jugando = False
            return

        carta = self.cartas_barajadas[self.indice]
        filename = f"{self.cartas.index(carta)+1}_{remove_accents(carta).replace(' ', '_').lower()}.jpg"
        path = os.path.join(CARDS_FOLDER, filename)
        if os.path.exists(path):
            self.ids.card_image.source = path
            self.ids.card_image.reload()
        self.ids.card_name.text = carta

        try:
            self.tts.speak(carta)
        except Exception as e:
            import logging
            logging.error(f"TTS speak error: {e}")

        self.indice += 1
        if self.after_event:
            self.after_event.cancel()
        self.after_event = Clock.schedule_once(self.mostrar_carta, 2)

    def toggle_pausa(self):
        if self.jugando:
            self.jugando = False
            if self.after_event:
                self.after_event.cancel()
            self.ids.pause_button.text = 'Reanudar'
        else:
            self.jugando = True
            self.ids.pause_button.text = 'Pausar'
            self.mostrar_carta()
                
    def iniciar_juego(self):
        if self.jugando:
            return
        self.cartas_barajadas = random.sample(self.cartas, len(self.cartas))
        self.indice = 0
        self.jugando = True
        self.ids.pause_button.text = 'Pausar'
        self.mostrar_carta()

    def mostrar_carta(self, *args):
        if not self.jugando or self.indice >= len(self.cartas_barajadas):
            if self.indice >= len(self.cartas_barajadas):
                from kivy.uix.popup import Popup
                from kivy.uix.label import Label
                popup = Popup(title='Fin', content=Label(text='¡Todas las cartas se han mostrado!'), size_hint=(0.6, 0.4))
                popup.open()
                self.jugando = False
            return

        carta = self.cartas_barajadas[self.indice]
        filename = f"{self.cartas.index(carta)+1}_{remove_accents(carta).replace(' ', '_').lower()}.jpg"
        path = os.path.join(CARDS_FOLDER, filename)
        if os.path.exists(path):
            self.ids.card_image.source = path
            self.ids.card_image.reload()
        self.ids.card_name.text = carta

        try:
            self.tts.speak(carta)
        except Exception as e:
            import logging
            logging.error(f"TTS speak error: {e}")

        self.indice += 1
        if self.after_event:
            self.after_event.cancel()
        self.after_event = Clock.schedule_once(self.mostrar_carta, 2)

    def toggle_pausa(self):
        if self.jugando:
            self.jugando = False
            if self.after_event:
                self.after_event.cancel()
            self.ids.pause_button.text = 'Reanudar'
        else:
            self.jugando = True
            self.ids.pause_button.text = 'Pausar'
            self.mostrar_carta()
            
    def reiniciar_juego(self):
        if self.after_event:
            self.after_event.cancel()
        self.iniciar_juego()
                
class LoteriaApp(App):
    def build(self):
        Builder.load_string(KV)
        return LoteriaLayout()

if __name__ == '__main__':
    Window.clearcolor = (0.98, 0.98, 0.98, 1)
    LoteriaApp().run()
