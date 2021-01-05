#!/bin/bash

composer install

bin/console doctrine:database:drop --force --if-exists
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate -n
#bin/console doctrine:fixtures:load -n

echo "######### PLEASE FOLLOW THIS INSTRICTIONS #######";
echo "Please add to your: /etc/hosts file next line:";
echo "<your Docker IP here> mysql.crassula.loc crassula.loc";
echo "-------------------------------------------------"
echo "How to find Docker IP?"
echo "For linux use Docker IP which you can get via command: 'ifconfig docker | grep "inet"'"
echo "For MAC OS (native Docker) use 127.0.0.1";
echo "For specific Docker machine use Docker machine IP, you can get it via command: 'docker-machine ls'"
echo "#################################################";
