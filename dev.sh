#!/bin/bash

# todo: Setup logic here

echo "#################################################";
echo "Please add to your: /etc/hosts file next line:";
echo "<your Docker IP here> mysql.crassula.loc crassula.loc";
echo "For linux use Docker IP which you can get via command: 'ifconfig docker | grep "inet"'"
echo "For MAC OS (native Docker) use 127.0.0.1";
echo "For specific Docker machine use Docker machine IP, you can get it via command: 'docker-machine ls'"
echo "#################################################";

