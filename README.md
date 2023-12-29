<p align="center"><img src="frontend/public/logo.svg" alt="Logo" width="250"></p>
<h1 align="center">eCamp<sup><small>v3</small></sup></h1>
<p align="center"><a href="https://github.com/ecamp/ecamp3/actions?query=workflow%3ACI"><img src="https://github.com/ecamp/ecamp3/workflows/CI/badge.svg?branch=devel" alt="CI"></a> <a href="https://coveralls.io/github/ecamp/ecamp3?branch=devel"><img src="https://coveralls.io/repos/github/ecamp/ecamp3/badge.svg?branch=devel" alt="Coverage Status"></a> <a href="https://github.com/ecamp/ecamp3/blob/devel/LICENSE"><img src="https://badgen.net/github/license/ecamp/ecamp3" alt="License"></a> <a href="https://discord.gg/tdwtRytV6P"><img src="https://discord.com/api/guilds/1165624811800768702/widget.png?style=shield" alt="Discord shield"></a></p>

## English

_(deutsche Übersetzung weiter unten)_

eCamp is a web application for planning J+S camps, J+S courses and other camps. It helps to exchange all planned programme among the camp organizers.

An older version called "eCamp v2" is productive and can be used at the following address (but only in german): [https://ecamp.pfadiluzern.ch](https://ecamp.pfadiluzern.ch/)

eCamp3 is the re-development of eCamp with the goal of reimplementing the functionality of eCamp v2 using modern technologies and an extensible [architecture](https://github.com/ecamp/ecamp3/wiki/architecture). Currently, an MVP (minimum viable product) is in operation. Next, we will focus on course planning, and further extensions will follow later.

The following commonly requested improvements over eCamp v2 are already implemented:

- Improved saving features - where possible, data are auto-saved on the fly.
- Usability on mobile phones - the design is mobile-first.
- Login via MiData account of the Swiss Guide and Scouts Movement is possible
- Switching the user interface language, powered by [Lokalise](https://lokalise.com)
- Formatting texts (bold, italic, etc.)

eCamp v3 is made up of a backend based on the PHP framework API Platform (Symfony), which offers an API, as well as a Vue.js frontend and some other smaller services.

### How can I help?

Thanks for helping! There are a few ways to get started.

- Visit our test environment at [https://dev.ecamp3.ch](https://dev.ecamp3.ch). If you discover a bug, [open a new issue for it](https://github.com/ecamp/ecamp3/issues/new).
- To run the project locally on your computer, follow one of the installation guides:
  - [Installation with Docker on Linux](https://github.com/ecamp/ecamp3/wiki/Development-install-on-linux)
  - [Installation with Docker on Windows + WSL2](https://github.com/ecamp/ecamp3/wiki/Development-installation-on-Windows)
  - [Set up VS Code as your code editor](https://github.com/ecamp/ecamp3/wiki/Development-installation-on-Windows#setting-up-the-ide)
- If you encounter issues during setup or have questions, don't worry, you're not alone. You can ask for help on our [community discord server](https://discord.gg/tdwtRytV6P) and we'll do our best to help you get up and running.
- Before you start coding, read our [contributing guidelines](CONTRIBUTING.md).
- Familiarize yourself with the [documentation in the Wiki](https://github.com/ecamp/ecamp3/wiki).
- Choose an [issue labeled "good first issue"](https://github.com/ecamp/ecamp3/issues?q=is%3Aissue+is%3Aopen+label%3A%22good+first+issue%22) and try to solve it.

## Deutsch

eCamp ist eine Webapplikation, mit welcher J+S-Lager, J+S-Kurse und andere Lager geplant werden können. Dabei verfügen alle Benutzer stets über den aktuellsten Stand der Planung.

Die ältere Version eCamp v2 ist produktiv im Einsatz und kann unter folgender Adresse genutzt werden: [https://ecamp.pfadiluzern.ch](https://ecamp.pfadiluzern.ch/)

eCamp3 ist die Neu-Entwicklung von eCamp. Das Ziel ist es, die Funktionalität von eCamp v2 mit moderneren Technologien und einer erweiterbaren [Architektur](https://github.com/ecamp/ecamp3/wiki/architecture) neu zu implementieren. Aktuell läuft eine MVP (minimum viable product, minimale nützliche Version). Als Nächstes fokussieren wir uns auf die Kursplanung und später werden dann noch weitere Erweiterungen folgen.

Folgende Verbesserungen, die bei eCamp v2 oft gewünscht wurden, sind bereits implementiert:

- Bessere Speicher-Funktion - wo immer möglich werden die Daten laufend automatisch gespeichert.
- Benutzbarkeit auf dem Mobiltelefon - das Design ist mobile-first.
- Login via MiData-Account der Pfadibewegung Schweiz ist möglich
- Mehrsprachigkeit mit [Lokalise](https://lokalise.com)
- Formatierung in Texten (fett, kursiv, etc.)

eCamp v3 besteht aus einem Backend basierend auf dem PHP-Framework API Platform (Symfony), welches eine API anbietet, einem Vue.js-Frontend sowie einigen weiteren kleineren Services.

### Wie kann ich helfen?

Danke dass du mithelfen möchtest! Es gibt ein paar verschiedene Arten wie du beginnen kannst.

- Besuche unsere Testumgebung auf https://dev.ecamp3.ch. Wenn du einen Fehler entdeckst, [eröffne ein Issue dafür](https://github.com/ecamp/ecamp3/issues/new).
- Um das Projekt bei dir auf dem Computer laufen zu lassen, folge einer der Installationsanleitungen:
  - [Installation mit Docker auf Linux](https://github.com/ecamp/ecamp3/wiki/Development-install-on-linux#Deutsch)
  - [Installation mit Docker auf Windows + WSL2](https://github.com/ecamp/ecamp3/wiki/Development-installation-on-Windows) (nur englisch)
  - [VS Code als Code-Editor einrichten](https://github.com/ecamp/ecamp3/wiki/Development-installation-on-Windows#setting-up-the-ide) (nur englisch)
- Falls du Probleme beim Einrichten oder Verständnisfragen hast - keine Sorge, du bist nicht allein. Auf unserem [Community Discord Server](https://discord.gg/tdwtRytV6P) unterstützen wir dich sehr gerne.
- Bevor du mit programmieren loslegen kannst, lies unsere [contributing guidelines](./CONTRIBUTING_DE.md)
- Studier die [Dokumentation im Wiki](https://github.com/ecamp/ecamp3/wiki)
- Such dir ein [einsteigertaugliches Issue](https://github.com/ecamp/ecamp3/issues?q=is%3Aissue+is%3Aopen+label%3A%22good+first+issue%22) aus und probiere es zu lösen
