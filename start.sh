#!/bin/bash

echo "Subindo containers..."
cp .env.example .env

docker compose up -d --build

echo "Ajustando permissões da pasta storage..."
sudo chmod -R 777 storage storage/

echo "Instalando dependências do Composer dentro do container..."
docker exec -u 0 -it app-laravel php artisan key:generate
docker exec -u 0 -it app-laravel composer install

echo "Migrando banco de dados..."
docker exec -it app-laravel php artisan migrate --seed

echo "Criando banco para testes"
docker exec -it postgres-db psql -U root -d postgres -c "CREATE DATABASE laravel_test;"
