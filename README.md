<p align="center"><img align="center" style="width:320px" src="https://raw.githubusercontent.com/carlobeltrame/ecamp3/update-contribution-instructions/logo.svg"/></p><br/>
<p align="center">
  <a href="https://travis-ci.com/ecamp/ecamp3"><img src="https://travis-ci.com/ecamp/ecamp3.svg?branch=devel" alt="Build Status"></a>
  <a href="https://coveralls.io/github/ecamp/ecamp3?branch=devel"><img src="https://coveralls.io/repos/github/ecamp/ecamp3/badge.svg?branch=devel" alt="Coverage Status"></a>
  <a href="https://github.com/ecamp/ecamp3/blob/devel/LICENSE"><img src="https://badgen.net/github/license/ecamp/ecamp3" alt="License"></a>
</p>

## eCamp3

(deutsche Übersetzung weiter unten)

eCamp is a web application for planning J+S camps, J+S courses and other camps. It helps to exchange all planned programme among the camp organizers.

An older version called "eCamp v2" is productive and can be used at the following address (but only in german): [https://ecamp.pfadiluzern.ch](https://ecamp.pfadiluzern.ch/)
 
eCamp3 is a project that aims to re-develop the features of eCamp v2 with modern technologies and an extensible architecture. Currently, we focus on re-building just the camp planning features, which will be released in an MVP (minimal viable product). Later, extensions for course planning and other features will follow.

The following commonly requested improvements over eCamp v2 are already implemented:

- Improved saving features - where possible, data are auto-saved on the fly.
- Usability on mobile phones - the design is mobile-first.
- Login via MiData account of the swiss scouts association is possible
- Switching the user interface language, powered by [Lokalise](https://lokalise.com)
- Formatting texts (bold, italic, etc.) - is prepared, will be activated after the initial release.

eCamp v3 is made up of a backend based on the PHP framework Laminas, which offers a HAL JSON API, as well as a Vue.js frontend and some other smaller services.

#### Want to help?

Thanks for helping! There are a few ways to get started.
- Visit our test environment at https://dev.ecamp3.ch. If you discover a bug, [open an issue](https://github.com/ecamp/ecamp3/issues/new) for it.
- To run the project on your computer for development, follow one of our installation guides:
  - [Installation with Docker on Linux](docu/install-docker.md) (German only)
  - [Installation with Docker on Windows + WSL 2](https://github.com/ecamp/ecamp3/wiki/Getting-started-on-Windows)
  - [Set up VS Code as your code editor](https://github.com/ecamp/ecamp3/wiki/Getting-started-on-Windows#setting-up-the-ide)
- If you run into problems during setup or need help, open a [discussion](https://github.com/ecamp/ecamp3/discussions)
- Before you can start coding, read our [contributing guidelines](CONTRIBUTING.md)
- Study the [documentation in the wiki](https://github.com/ecamp/ecamp3/wiki)
- Choose an [Issue labeled "good first issue"](https://github.com/ecamp/ecamp3/issues?q=is%3Aissue+is%3Aopen+label%3A%22good+first+issue%22) and try to solve it


## eCamp3 (deutsche Übersetzung)

eCamp ist eine Webapplikation, mit welcher J+S-Lager, J+S-Kurse und andere Lager geplant werden können. Dabei verfügen alle Benutzer stets über den aktuellsten Stand der Planung.

Die ältere Version eCamp v2 ist produktiv im Einsatz und kann unter folgender Adresse genutzt werden: [https://ecamp.pfadiluzern.ch](https://ecamp.pfadiluzern.ch/)

eCamp3 ist eine Neu-Entwicklung von eCamp. Das Ziel ist es, die Funktionalität von eCamp v2 mit moderneren Technologien und einer erweiterbaren Architektur neu zu implementieren. Aktuell fokussieren wir uns auf den Neubau der Lagerplanungs-Features, welche in einem MVP (minimum viable product, minimale nützliche Version) released werden. Später werden dann Erweiterungen für die Kursplanung und weiteres folgen.

Folgende Verbesserungen, die bei eCamp v2 oft gewünscht werden, sind bereits implementiert:

- Bessere Speicher-Funktion - wo immer möglich werden die Daten laufend automatisch gespeichert.
- Benutzbarkeit auf dem Mobiltelefon - das Design ist mobile-first.
- Login via MiData-Account der Pfadibewegung Schweiz ist möglich
- Mehrsprachigkeit mit [Lokalise](https://lokalise.com)
- Formatierung in Texten (fett, kursiv, etc.) - ist vorbereitet, wird aber erst nach dem ersten Release freigeschaltet.

eCamp v3 besteht aus einem Backend basierend auf dem PHP-Framework Laminas welches eine HAL JSON API anbietet, einem Vue.js-Frontend sowie einigen weiteren kleineren Services.

#### Mithelfen

Danke dass du mithelfen möchtest! Es gibt ein paar verschiedene Arten wie du beginnen kannst.
- Besuche unsere Testumgebung auf https://dev.ecamp3.ch. Wenn du einen Fehler entdeckst, [eröffne ein Issue dafür](https://github.com/ecamp/ecamp3/issues/new).
- Um das Projekt bei dir auf dem Computer laufen zu lassen, folge einer der Installationsanleitungen:
  - [Installation mit Docker auf Linux](docu/install-docker.md)
  - [Installation mit Docker auf Windows + WSL2](https://github.com/ecamp/ecamp3/wiki/Getting-started-on-Windows) (nur englisch)
  - [VS Code als Code-Editor einrichten](https://github.com/ecamp/ecamp3/wiki/Getting-started-on-Windows#setting-up-the-ide) (nur englisch)
- Falls du Probleme beim Einrichten oder Verständnisfragen hast, eröffne eine [Discussion](https://github.com/ecamp/ecamp3/discussions)
- Bevor du mit programmieren loslegen kannst, lies unsere [contributing guidelines](CONTRIBUTING.md)
- Studier die [Dokumentation im wiki](https://github.com/ecamp/ecamp3/wiki)
- Such dir ein [einsteigertaugliches Issue](https://github.com/ecamp/ecamp3/issues?q=is%3Aissue+is%3Aopen+label%3A%22good+first+issue%22) aus und probiere es zu lösen
