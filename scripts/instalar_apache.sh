#!/bin/bash

# Actualizar el repositorio de paquetes
sudo apt update -y

# Instalar Servidor Web Apache
sudo apt install apache2 -y

# Habilitar e iniciar el servicio de Apache
sudo systemctl enable apache2
sudo systemctl start apache2

echo "¡Apache se ha instalado y configurado correctamente!"