
## Instalacion

### Despliegue con docker

* Clonar laradock dentro de la carpeta del proyecto.

```sh
$ git submodule update --init --recursive
$ cp -f docs/docker/docker-compose.yml laradock/
$ cp -f docs/docker/env-example laradock/.env
$ cd laradock
```

* Modificar el archivo `.env` de **laradock** de acuerdo a los puertos que se irán a utilizar.

* Generar las imágenes:

```sh
$ docker-compose up -d nginx
```

* Instalar el soporte para lenguaje español:

```sh
$ docker-compose exec php-fpm /var/www/install-spanish-locale.sh
$ docker-compose exec workspace /var/www/install-spanish-locale.sh
```

* Dentro del contenedor workspace, generar las variables de entorno:

```sh
$ docker-compose exec --user laradock workspace composer run-script post-root-package-install
```

* Instalar las dependencias:

```sh
$ docker-compose exec --user laradock workspace composer install
```

* Generar las llaves de sesión y de autenticación:

```sh
$ docker-compose exec --user laradock workspace composer run-script post-create-project-cmd
```

* Modificar el archivo `.env` del proyecto laravel de acuerdo a las credenciales de base de datos, etc.

* Transpilar el código Javascript

```sh
$ docker-compose exec --user laradock workspace yarn prod
```