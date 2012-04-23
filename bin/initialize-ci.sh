#!/bin/sh

PARENT_DIR=$(dirname $(cd "$(dirname "$0")"; pwd))
CI_DIR="$PARENT_DIR/ci-stuff/environment"

ODB_VERSION=${1:-"1.0rc6"}
ODB_PACKAGE="orientdb-${ODB_VERSION}-distribution.tar.gz"

echo "=== Initializing CI environment ==="

cd "$PARENT_DIR"

. "$PARENT_DIR/bin/odb-shared.sh"

if [ ! -d "$CI_DIR" ]; then
  # Fetch the stuff needed to run the CI session.
  git clone --quiet git://gist.github.com/1370152.git $CI_DIR

  # Download and extract OrientDB
  echo "--- Downloading OrientDB v${ODB_VERSION} ---"
  odb_download "http://www.orientechnologies.com/listing/m2/com/orientechnologies/orientdb/$ODB_VERSION/$ODB_PACKAGE" $CI_DIR
  tar xf $CI_DIR/$ODB_PACKAGE -C $CI_DIR

  # Copy the configuration file and the demo database
  echo "--- Setting up OrientDB ---"
  tar xf $CI_DIR/databases.tar.gz -C $CI_DIR/orientdb-${ODB_VERSION}/
  cp $PARENT_DIR/ci-stuff/orientdb-server-config.xml $CI_DIR/orientdb-${ODB_VERSION}/config/
  cp $PARENT_DIR/ci-stuff/orientdb-server-log.properties $CI_DIR/orientdb-${ODB_VERSION}/config/
else
  echo "!!! Directory $CI_DIR exists, skipping downloads !!!"
fi

# Start OrientDB in background.
echo "--- Starting an instance of OrientDB ---"
sh -c $CI_DIR/orientdb-${ODB_VERSION}/bin/server.sh </dev/null &>/dev/null &

# Wait a bit for OrientDB to finish the initialization phase.
sleep 5
printf "\n=== The CI environment has been initialized ===\n"
