rm -rf log
mkdir log
phpunit --configuration=Test/PHPUnit/phpunit.xml --log-junit log/phpunit.xml
phploc --log-xml log/phploc.xml .
phpcpd --min-lines 3 --min-tokens 10 --exclude Test/ --log-pmd log/phpcpd.xml .
phpcs . --report-xml=`pwd`/log/phpcs.xml
pdepend --jdepend-chart=log/pdepend-chart.svg --jdepend-xml=log/pdepend.xml --overview-pyramid=log/pdepend-pyramid.svg .
phpmd . xml codesize,unusedcode,naming --reportfile log/phpmd.xml --exclude Test/
phpcb --log=log/ --source=. --output=log/report
