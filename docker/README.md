# Docker README

There are two docker setups in this folder:

* Minimal
  * Based on @ranisalt work (Add Dockerfile & publish to GHCR #313). Contains only 1 image - web server. The rest you need to carry yourself.

* Full:
  * myaac + mysql + mailpit (mail SMTP server for debugging) + phpmyadmin ] + server-data folder (with groups.xml, items.xml & vocations.xml).

## How to?

First change to current aac folder after git clone:
`cd myaac`

Usage:

### Minimal

First, change mysqlHost in config.lua to = "host.docker.internal" if your mysql is running on windows, so myaac can connect to your db
`docker build --tag 'myaac-minimal' -f docker/minimal/Dockerfile .`
`docker run --rm --name myaac -p 9000:80 -v "C:\PathToServer:/server:ro" -v "config:/config" -d myaac-minimal`
Access:
  * myaac: http://localhost:9000

Set server path to: /server in the install page


### Full

`cd docker/full && docker compose up --build`
Access:
  * myaac: http://localhost:8001
  * phpmyadmin: http://localhost:8002 (you should be logged in automatically)
  * mailpit: http://localhost:8025 (here you can view emails sent, for testing purposes)

Server path will be set automatically.

To send email you can configure SMTP Mailing with following settings:
  * Option: SMTP
  * Host: mailpit
  * Port 1025
  * Auth: no
  * Username & password: leave empty
  * Security: None

The full is good for local development without need to configure anything.

In minimum you need to separate install mysql, tfs, phpmyadmin etc.
