# Utiliser l'image officielle de PHP avec Apache
FROM php:8.3-apache

# allow composer super user
ENV COMPOSER_ALLOW_SUPERUSER 1

# Mettre à jour la liste des paquets et installer les dépendances nécessaires
RUN apt-get update && \
    apt-get install -y git && \
    apt-get install -y unzip && \
    apt-get install -y libzip-dev && \
    apt-get install -y nano && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Activer le mod_rewrite pour Apache
RUN a2enmod rewrite

# Copier les fichiers de configuration Apache si nécessaire
COPY deploy/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Générer un clef ssh pour github
RUN mkdir /root/.ssh

# Copier la clef ssh dans le conteneur
COPY deploy/ssh-keys/id_test_deploy /root/.ssh/id_rsa

# Changer les permissions de la clef ssh
RUN chmod 600 /root/.ssh/id_rsa

# Ajouter la clef ssh à la liste des clefs connues
RUN touch /root/.ssh/known_hosts
RUN ssh-keyscan github.com >> /root/.ssh/known_hosts

# Cloner le dépôt de l'application dans /var/www/html
RUN git clone git@github.com:ITech-Fixers/vide-grenier-en-ligne.git /var/www/html

# Installer les dépendances de l'application
RUN cd /var/www/html && composer install && npm install

# Changer le propriétaire des fichiers de l'application
RUN chown -R www-data:www-data /var/www/html

# Changer les permissions des fichiers de l'application
RUN chmod -R 755 /var/www/html

# Compiler les assets
RUN cd /var/www/html && npm run watch

# Exposer le port 80
EXPOSE 80

# Commande pour démarrer Apache en arrière-plan
CMD ["apache2-foreground"]
