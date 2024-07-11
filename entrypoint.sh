#!/bin/bash

# Quitter si une commande échoue
set -e

# installer l'application
cd /var/www/html && make install

# Générer la documentation openapi
cd /var/www/html && make openapi

# Lancer apache en premier plan
apache2-foreground