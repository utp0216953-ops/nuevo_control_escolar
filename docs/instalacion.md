# Instalación del Sistema de Control Escolar

## Requisitos

Para ejecutar el sistema se requiere:

- Apache
- PHP
- MariaDB
- Navegador web
- Sistema operativo Linux

## Instalación de Apache

Ejecutar el siguiente script:

    ./scripts/instalar-apache.sh

## Instalación de PHP

Ejecutar:

    ./scripts/instalar-php.sh

## Instalación de MariaDB

Ejecutar:

    ./scripts/instalar-mariadb.sh

## Base de datos

La base de datos se encuentra en:

    database/nexus.sql

El nombre de la base de datos es:

    nexus

## Configuración

La configuración de conexión se encuentra en:

    config/config.php

## Aplicación

Los archivos principales de la aplicación se encuentran dentro de:

    app/