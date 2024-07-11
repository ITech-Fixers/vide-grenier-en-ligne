# Utiliser l'image officielle de PHP avec Apache
FROM php:8.3-apache

# Paramétrer les variables d'environnement pour Composer
ENV COMPOSER_ALLOW_SUPERUSER 1

# Mettre à jour la liste des paquets et installer les dépendances infra
RUN apt-get update && \
    apt-get install -y git && \
    apt-get install -y unzip && \
    apt-get install -y libzip-dev && \
    apt-get install -y nano && \
    apt-get clean && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install zip && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install pdo && \
    rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Installer Node.js
RUN curl -sL https://deb.nodesource.com/setup_21.x | bash -
RUN apt-get install -y nodejs

# Copier le script entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copier les fichiers de l'application dans un répertoire temporaire
COPY . /var/www/html_src

# Définir le répertoire de travail
WORKDIR /var/www/html

# Exposer le port 80
EXPOSE 80

# Utiliser le script entrypoint comme point d'entrée du conteneur
ENTRYPOINT ["entrypoint.sh"]
