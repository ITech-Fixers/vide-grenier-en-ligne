# Utiliser l'image officielle de PHP avec Apache
FROM php:8.3-apache

# Paramétrer les variables d'environnement pour Composer
ENV COMPOSER_ALLOW_SUPERUSER 1

# Mettre à jour la liste des paquets et installer les dépendances infra
RUN apt-get update && \
    apt-get install -y git unzip libzip-dev nano && \
    apt-get clean && \
    docker-php-ext-install pdo_mysql zip mysqli pdo && \
    rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Installer Node.js
RUN curl -sL https://deb.nodesource.com/setup_21.x | bash -
RUN apt-get install -y nodejs

# Activer le mod_rewrite pour Apache
RUN a2enmod rewrite

# Copier le fichier de configuration d'Apache
COPY deploy/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Créer le répertoire pour les clefs ssh
RUN mkdir /root/.ssh

# Ajouter github.com dans les known_hosts
RUN touch /root/.ssh/known_hosts
RUN ssh-keyscan github.com >> /root/.ssh/known_hosts

# Créer le répertoire de l'application si nécessaire
RUN mkdir -p /var/www/html

# Copier les fichiers de l'application dans /var/www/html
COPY . /var/www/html

# Changer le propriétaire des fichiers de l'application
RUN chown -R www-data:www-data /var/www/html

# Changer les permissions des fichiers de l'application
RUN chmod -R 755 /var/www/html

# Copy the entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Make the entrypoint script executable
RUN chmod +x /usr/local/bin/entrypoint.sh

# Définir le répertoire de travail
WORKDIR /var/www/html

# Exposer le port 80
EXPOSE 80

# Exécuter le script d'entrée
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
