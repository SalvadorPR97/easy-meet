# **Easy Meet!** #
Es una aplicación web que permite crear eventos en una zona y que otros
usuarios puedan consultar la información y unirse. 

## **Introducción** ##
Se crea con la idea de ayudar a personas introvertidas o sin un grupo para hacer una actividad pueda conseguir otros participantes.
Permite crear un evento (concierto, actividad al aire libre, sesiones de cine...), señalándolo en un mapa y el resto de usuarios 
pueden consultar la información del evento y unirse. Una vez unidos al evento, se habilita un chat con todos los del evento.
Se podrá valorar a otros usuarios y reportarlos para mayor seguridad.

## **Características** ##
- *Gestión de usuarios*
  - CRUD
  - Login
  - Reporte
  - Valoración
- *Gestión de eventos*
  - CRUD
  - Reporte
  - Valoración

## **Requisitos** ##
| Nombre               | Versión | Enlace Oficial                                      |
|----------------------|---------|-----------------------------------------------------|
| Angular              | 19.0    | [https://angular.io](https://angular.io)            |
| MySQL                | 8.0     | [https://www.mysql.com](https://www.mysql.com)       |
| Docker               | 24.0    | [https://www.docker.com](https://www.docker.com)     |
| PHP                  | 8.3     | [https://www.php.net](https://www.php.net)           |
| Node                 | 22.11   | [https://nodejs.org](https://nodejs.org)             |
| NPM                  | 10.9    | [https://www.npmjs.com](https://www.npmjs.com)       |
| PHP Composer         | 2.8     | [https://getcomposer.org](https://getcomposer.org)    |

## **Instalación** ##
Para instalarlo primero ejecutaremos el siguiente comando con la ruta de la terminal en la carpeta server para instalar 
las dependencias del servidor de xpress:
```bash
npm install
```
Lo ejecutaremos también la carpeta cliente para instalar las dependencias de angular.
Después instalaremos las dependecias de PHP con:
```bash
cd /ruta/a/carpeta/del/composer.json
composer install
```
Después ejecutaremos el contenedor de Docker que contiene la BDD con el siguiente comando:
```bash
docker compose up -d
```
Ahora volveremos a la ruta del servidor de xpress y ejecutaremos: 
```bash
npm start
```
Y en otra terminal, sin cerrar la anterior, iremos a la ruta de angular y ejecutaremos:
```bash
ng s -o
```
Debería abrirnos el navegador por defecto con la aplicación funcionando mostrando la landpage como ésta:
![Portada de la aplicación](http://vps-1801da8f.vps.ovh.net/Curso2024-2025/tfg/Captura.PNG)

## **Uso** ##
Un nuevo usuario que vaya a utilizar nuestra aplicación comenzará en la **landpage**. Desde ahí elegirá ver la portada de los eventos que hay creados o bien *registrarse* para ver toda la información.<br>
Se registrará rellenando sus datos y confirmando el registro con un **enlace que recibirá al correo**.<br>
Una vez registrado y con la sesión iniciada puede utilizar el **buscador** para unirse a un evento concreto, de una **familia específica** (concierto, deporte...) o podrá **utilizar el mapa** para buscar eventos cerca.<br>
También podrá **crear su propio evento** rellenando los campos, y especificando la dirección o utilizando el mapa.<br>
Una vez la gente se una a su evento o él se una a uno, podrá utilizar el **chat grupal** para "romper el hielo" antes 
del evento e ir con más seguridad o **consultar la información del resto de participantes** para conocer más con 
qué personas va a hacer una actividad.<br>
El usuario también podrá modificar parte de su información personal, conctactar con los administradores, enviar 
sugerencias...