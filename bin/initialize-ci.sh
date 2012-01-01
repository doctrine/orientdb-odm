#!/bin/sh

ODB_VERSION=${1:-"1.0rc6"}
PARENT_DIR=$(dirname $(cd "$(dirname "$0")"; pwd))

echo "=== Initializing CI environment ==="

cd $PARENT_DIR

# Fetch the stuff needed to run the CI session.
git clone --quiet git://gist.github.com/1370152.git ci-stuff/environment

# Download and extract OrientDB
echo "--- Downloading OrientDB v${ODB_VERSION} ---"
wget -nc -q -Pci-stuff/environment http://www.orientechnologies.com/listing/m2/com/orientechnologies/orientdb/${ODB_VERSION}/orientdb-${ODB_VERSION}-distribution.tar.gz
tar xf ci-stuff/environment/orientdb-${ODB_VERSION}-distribution.tar.gz -C ci-stuff/environment

# Copy the configuration file and the demo database
echo "--- Setting up OrientDB ---"
tar xf ci-stuff/environment/databases.tar.gz -C ci-stuff/environment/orientdb-${ODB_VERSION}/
cp ci-stuff/orientdb-server-config.xml ci-stuff/environment/orientdb-${ODB_VERSION}/config/
cp ci-stuff/orientdb-server-log.properties ci-stuff/environment/orientdb-${ODB_VERSION}/config/

# Start OrientDB in background.
echo "--- Starting an instance of OrientDB ---"
sh -c ./ci-stuff/environment/orientdb-${ODB_VERSION}/bin/server.sh </dev/null &>/dev/null &

# Wait a bit for OrientDB to finish the initialization phase.
sleep 5
printf "\n=== The CI environment has been initialized ===\n"
