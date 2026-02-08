FROM php:8.2-cli

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

RUN docker-php-ext-install pdo pdo_mysql mysqli

WORKDIR /app

CMD ["php", "-v"]
