# 🎬 Movie Library (Symfony-Projekt)

Dieses Projekt ist eine einfache Movie-Library-Anwendung, entwickelt mit Symfony. Ziel ist es, lokale Filmdateien strukturiert zu erfassen und in einer Weboberfläche darzustellen.

---

## ✨ Funktionen

- 🔍 Durchsuchen eines lokalen Ordners nach Filmdateien (`.mp4`, `.mkv`, `.avi`, …)
- 📄 Erzeugen einer JSON-Datei mit Dateipfad, Dateiname und Dateiendung
- 📥 Upload und Import dieser JSON-Datei in die Datenbank
- 📝 Manuelle Bearbeitung der Filmdaten (z. B. Genre, Spieldauer, Beschreibung)
- 🎥 Anzeige und späteres Abspielen der Filme (sofern vom Browser unterstützt)

---

## ⚙️ Technischer Überblick

- Framework: **Symfony 7.x**
- Lokale Umgebung: [**DDEV**](https://ddev.readthedocs.io/)
- Datenbank: MySQL/MariaDB via Doctrine ORM
- Frontend: Twig + Bootstrap 5 (über Webpack Encore)
- Asset-Management: Webpack Encore (mit Stimulus, jQuery, Popper.js)

---

## 🚀 Installation (lokal mit DDEV)

## 🚀 Installation & Einrichtung

### 🔹 DDEV starten

```bash
ddev start
```

> Falls du das Projekt neu einrichtest, führe zuerst `ddev config` aus.

### 🔹 Abhängigkeiten installieren

```bash
ddev composer install --working-dir=app
```

### 🔹 Datenbank initialisieren

```bash
ddev bin/console --working-dir=app doctrine:database:create
ddev bin/console --working-dir=app doctrine:schema:update --force
```

### 🔹 Projekt im Browser öffnen

```
http://movie-library.ddev.site
```

---

## 📂 JSON-Datei erzeugen

1. Lokalen Ordner mit Videodateien durchsuchen.
2. JSON-Datei manuell oder per Skript im folgenden Format erzeugen:

   ```json
   [
     {
       "name": "Inception",
       "path": "/mnt/nas/video/Inception.mp4",
       "extension": "mp4"
     },
     {
       "name": "Interstellar",
       "path": "/mnt/nas/video/Interstellar.mkv",
       "extension": "mkv"
     }
   ]
   ```

3. Die Datei über das Upload-Formular im Browser hochladen.
4. Filme erscheinen anschließend in der Liste und können weiterbearbeitet werden.

---

## 🎨 Assets & Frontend

Die Anwendung verwendet Webpack Encore für modernes Asset-Management.

### 🔹 Node-Abhängigkeiten installieren

```bash
cd app/
yarn install
```

### 🔹 Assets bauen

```bash
# Entwicklungsmodus
yarn encore dev

# Produktionsmodus
yarn encore production
```

### 🔹 Notwendige Pakete (Bootstrap 5 Setup)

```bash
yarn add --dev bootstrap @popperjs/core jquery
```

Falls du moderne JS-Features nutzt:

```bash
yarn add --dev @babel/plugin-proposal-class-properties
```

### 🔹 Integration in Twig

In `base.html.twig` sollten folgende Zeilen enthalten sein:

```twig
{{ encore_entry_link_tags('app') }}
{{ encore_entry_script_tags('app') }}
```

---

## 🧪 Geplante Features

- 🧠 Automatisches Einlesen von Metadaten (z.B. Dauer, Codec, Auflösung)
- 🌐 Integration mit externen APIs (OMDb, TMDB) zur Anreicherung von Filmdaten
- ⭐ Bewertungssystem
- 🔎 Erweiterte Filterung und Suche
- 🎥 Modal-Videoanzeige mit Video.js oder Plyr
- 📁 Anbindung an Netzlaufwerke (NAS)

---

## 🧑‍💻 Entwicklung

- Symfony-Code liegt im Verzeichnis `app/`
- Frontend-Assets befinden sich in `app/assets/`
- Kompilierte Assets landen in `app/public/build/`

---

## 📝 Lizenz

MIT License – siehe [LICENSE](LICENSE)

