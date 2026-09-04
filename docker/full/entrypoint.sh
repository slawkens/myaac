#!/usr/bin/env bash
set -euo pipefail

mkdir -p /home/www-data/server/data/XML

cat > /home/www-data/server/data/XML/vocations.xml <<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<vocations>
	<vocation id="0" name="None" fromvoc="0" />
	<vocation id="1" name="Sorcerer" fromvoc="1" />
	<vocation id="2" name="Druid" fromvoc="2" />
	<vocation id="3" name="Paladin" fromvoc="3" />
	<vocation id="4" name="Knight" fromvoc="4" />
	<vocation id="5" name="Master Sorcerer" fromvoc="1" />
	<vocation id="6" name="Elder Druid" fromvoc="2" />
	<vocation id="7" name="Royal Paladin" fromvoc="3" />
	<vocation id="8" name="Elite Knight" fromvoc="4" />
</vocations>
EOF

cat > /home/www-data/server/data/XML/groups.xml <<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<groups>
	<group id="1" name="player" access="0" maxdepotitems="0" maxvipentries="0" />
	<group id="6" name="god" access="1" maxdepotitems="0" maxvipentries="200" />
</groups>
EOF

mkdir -p /home/www-data/server/data/items
curl --fail --location --silent --show-error --retry 3 --retry-delay 1 --output /home/www-data/server/data/items/items.xml https://raw.githubusercontent.com/otland/forgottenserver/a4ba6bf3cd70437ad535827224804e0629105e72/data/items/items.xml

exec "$@"
