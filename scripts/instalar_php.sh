#!/bin/bash

# Actualizar el repositorio de paquetes
sudo apt update -y

# Instalar PHP y los módulos comunes para trabajar con Apache y MariaDB
sudo apt install php libapache2-mod-php php-mysql -y

# Reiniciar Apache para aplicar los cambios de PHP
sudo systemctl restart apache2

echo "¡PHP se ha instalado correctamente!"