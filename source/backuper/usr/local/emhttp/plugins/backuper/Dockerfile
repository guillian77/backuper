FROM php:8.2-buster

RUN apt update && apt install -y nano zip unzip

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app
