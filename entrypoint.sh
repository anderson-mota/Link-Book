#! /bin/sh

start_composer=$(composer install)
start_doctrine=$(bin/console doctrine:migrations:migrate && bin/console doctrine:fixtures:load --purge-with-truncate --no-interaction)

echo "$start_composer" > var/composer.log
echo "$start_doctrine" > var/doctrine.log

tail -f var/composer.log var/doctrine.log var/log/dev.log var/log/test.log