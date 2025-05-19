# Easy Meet - Backend

Este es el backend de la aplicación **Easy Meet**, desarrollado con **Laravel**. Expone una API RESTful para gestionar
usuarios, y eventos de la aplicación.

## 🛠 Tecnologías utilizadas

- [Laravel](https://laravel.com/) (v9+)
- PHP 8.0
- MySQL
- Composer
- Sanctum

## ⚙️ Requisitos previos

- PHP = 8.0
- Composer
- MySQL
- Extensiones PHP requeridas: `pdo`, `mbstring`, `openssl`, etc.

## 🚀 Instalación

### Clona el repositorio e instala dependencias:

```bash
git clone https://github.com/SalvadorPR97/easy-meet.git
cd easy-meet
composer install
```

### Copia el archivo de entorno y genera la clave:

```bash
cp .env.example .env
php artisan key:generate
```

Configura tus variables de entorno (.env) con los datos de conexión a base de datos, email, etc.

### Ejecuta las migraciones:

```bash
php artisan migrate
```

Si quieres utilizar datos de prueba, ejecuta los seeders:

```bash
php artisan db:seed
```

## ▶️ Servidor de desarrollo

### Inicia el servidor local:

```bash
php artisan serve
```

La API estará disponible en: http://localhost:8000

## 🧪 Ejecutar pruebas

```bash
php artisan test
```

Asegúrate de tener una base de datos de testing configurada en tu .env.testing

## 🔐 Autenticación

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

## ✅ Buenas prácticas aplicadas

- Arquitectura RESTful
- Rutas agrupadas con Route::prefix()
- Validación con Form Requests
- Separación de lógica en servicios
- Uso de migraciones y seeders
- Pruebas automatizadas con PHPUnit

## 📌 Pendientes / Mejoras futuras
