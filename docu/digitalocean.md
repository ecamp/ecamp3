# Einrichtung auf DigitalOcean

Notizen, wie die Infrastruktur auf DigitalOcean eingerichtet werden kann, um via Docker zu deployen.

* DigitalOcean Droplet erstellen mit Marketplace -> Docker 5:19.03.1~3 on Ubuntu 18.04, zweitniedrigste Leistung (50GB SSD, 2GB RAM)
* SSH-Key von lokalem Computer (bzw. Travis) eintragen

* Für zusätzliche SSH-Keys die beim Aufsetzen noch nicht eingetragen wurden:

  `cat <path-to-public-key-file> | ssh root@<droplet-ip> "cat >> ~/.ssh/authorized_keys"`

* `ssh root@<droplet-ip>`
  * `git clone --single-branch --branch devel https://github.com/<mein-github-account>/ecamp3.git`
  * `cd ecamp3`
  * `docker-compose up -d`

* Ausserhalb SSH: `export DOCKER_HOST=ssh://root@<droplet-ip>`
* DigitalOcean Managed DB Cluster erstellen mit MySQL 8.x und Verbindung zum Droplet
* Datenbank `ecamp3dev` erstellen
* User `ecamp3dev` erstellen mit normaler (moderner) Passwort-Verschlüsselung
* Vom Droplet aus via mysql-Client verbinden und `GRANT ALL PRIVILEGES ON ecamp3dev TO ecamp3dev`:

  `docker run -it --rm mysql mysql -u doadmin -pxxxxxxyyyyyyzzzzzz -h db-mysql-ecamp3-do-user-7273866-0.a.db.ondigitalocean.com -P 25060`
