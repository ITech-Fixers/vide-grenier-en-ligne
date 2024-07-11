#!/bin/bash
set -e

# Activer le mod_rewrite pour Apache
a2enmod rewrite

# Copier le fichier de configuration d'Apache
cp /var/www/html_src/deploy/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Créer le répertoire pour les clefs ssh
mkdir -p /root/.ssh

# Ajouter github.com dans les known_hosts
touch /root/.ssh/known_hosts
ssh-keyscan github.com >> /root/.ssh/known_hosts

# Créer le répertoire de l'application si nécessaire
mkdir -p /var/www/html

# Copier les fichiers de l'application dans /var/www/html
cp -r /var/www/html_src/* /var/www/html/

# Installer les dépendances de l'application
cd /var/www/html && composer install && npm install

# Générer le fichier swagger.yaml
cd /var/www/html && ./vendor/bin/openapi --output public/swagger-ui/swagger.yaml ./App/Controllers/

# Changer le propriétaire des fichiers de l'application
chown -R www-data:www-data /var/www/html

# Changer les permissions des fichiers de l'application
chmod -R 755 /var/www/html

# Enfin, démarrer Apache en arrière-plan
exec apache2-foreground
