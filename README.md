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

1. Projekt clonen:

```bash
git clone git@github.com:olaf-dh/movie-library.git movie-library
cd movie-library

