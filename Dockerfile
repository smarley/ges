FROM composer:latest as composer
COPY composer.json /app
RUN composer install --optimize-autoloader

FROM php:8
COPY --from=composer /app/vendor /app/vendor
COPY app.php /app/
WORKDIR /app
CMD ["php", "-S", "0.0.0.0:80"]