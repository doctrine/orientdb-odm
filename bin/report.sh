#!/bin/sh

PARENT_DIR=$(dirname $(cd "$(dirname "$0")"; pwd))
cd $PARENT_DIR

rm -rf log
mkdir log
phpunit --log-junit log/phpunit.xml
phploc --log-xml log/phploc.xml src
phpcpd --min-lines 3 --min-tokens 10 --exclude test/ --log-pmd log/phpcpd.xml src
phpcs --report=xml --report-file=`pwd`/log/phpcs.xml src
pdepend --jdepend-chart=log/pdepend-chart.svg --jdepend-xml=log/pdepend.xml --overview-pyramid=log/pdepend-pyramid.svg src
phpmd src xml codesize,unusedcode,naming --reportfile log/phpmd.xml --exclude test/
phpcb --log=log/ --source=src --output=log/report
