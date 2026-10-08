#!/bin/bash

# Actualizar el repositorio de paquetes
sudo apt update -y

# Instalar el servidor y cliente de MariaDB
sudo apt install mariadb-server mariadb-client -y

# Habilitar e iniciar el servicio de MariaDB
sudo systemctl enable mariadb
sudo systemctl start mariadb

echo "¡MariaDB se ha instalado correctamente!"