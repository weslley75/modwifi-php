#!/bin/sh
echo "Instalando dependências"
sudo apt-get install php5 >/dev/null
sudo apt-get install rfkill >/dev/null
sudo chmod +x index.php >/dev/null
sudo cp index.php /usr/bin/modwifi >/dev/null
echo "Pronto execute com o comando 'modwifi'"
