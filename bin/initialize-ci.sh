#!/bin/sh

PARENT_DIR=$(dirname $(cd "$(dirname "$0")"; pwd))
CI_DIR="$PARENT_DIR/ci-stuff/environment"

ODB_VERSION=${1:-"1.2.0"}
ODB_PACKAGE="orientdb-${ODB_VERSION}"
ODB_DIR="${CI_DIR}/${ODB_PACKAGE}"
ODB_LAUNCHER="${ODB_DIR}/bin/server.sh"

echo "=== Initializing CI environment ==="

cd "$PARENT_DIR"

. "$PARENT_DIR/bin/odb-shared.sh"

if [ ! -d "$CI_DIR" ]; then
  # Download and extract OrientDB
  echo "--- Downloading OrientDB v${ODB_VERSION} ---"
  odb_download "https://orient.googlecode.com/files/${ODB_PACKAGE}.zip" $CI_DIR
  unzip -q "${CI_DIR}/${ODB_PACKAGE}.zip" -d $CI_DIR && chmod +x $ODB_LAUNCHER

  # Copy the configuration file and the demo database
  echo "--- Setting up OrientDB ---"
  cp $PARENT_DIR/ci-stuff/orientdb-server-config.xml "${ODB_DIR}/config/"
  cp $PARENT_DIR/ci-stuff/orientdb-server-log.properties "${ODB_DIR}/config/"
else
  echo "!!! Directory $CI_DIR exists, skipping downloads !!!"
fi

# Start OrientDB in background.
echo "--- Starting an instance of OrientDB ---"
sh -c $ODB_LAUNCHER </dev/null &>/dev/null &

# Wait a bit for OrientDB to finish the initialization phase.
sleep 5
printf "\n=== The CI environment has been initialized ===\n"
