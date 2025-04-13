#!/bin/sh

# Espera o banco de dados estar disponível
until php artisan migrate --force; do
  echo "Aguardando o banco de dados estar pronto..."
  sleep 3
done

# Inicia o servidor
php artisan serve --host=0.0.0.0 --port=8080
