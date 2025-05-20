#!/bin/bash

echo "Esperando que la base de datos esté disponible..."
until nc -z -v -w30 db 3306
do
  echo "Esperando a la base de datos..."
  sleep 5
done

TABLE_EXISTS=$(mysql -h db -u root -proot easymeet -e "SHOW TABLES LIKE 'migrations';" | grep migrations)

if [ -z "$TABLE_EXISTS" ]; then
  echo "Primera vez, ejecutando migrate:fresh --seed..."
  php artisan migrate:fresh --seed
else
  echo "Migraciones detectadas. Ejecutando migrate --force..."
  php artisan migrate --force
fi

if [ -L "/var/www/public/storage" ] || [ -d "/var/www/public/storage" ]; then
  rm -rf /var/www/public/storage
fi

# Crear symlink a storage
php artisan storage:link

php artisan serve --host=0.0.0.0 --port=8000
