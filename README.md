# Ledge
Ledge is a simple RESTful API for user management built with Symfony.

## Dependencies
The only requirement to build and deploy Ledge is [Docker](https://www.docker.com/). nginx, PHP-FPM, and MySQL are all provided by DockerHub, while Composer and all applicable PHP libraries are installed within the container (maintaining a clean project directory) as part of the build process.

## Usage
The following commands should all be run from the main project directory:

|Command|Description|
|---|---|
|`docker-compose build`|Create the Docker containers, volumes, networks, etc. necessary to run Ledge.|
|`docker-compose up -d`|Start Ledge as a daemon.|
|`docker-compose exec api bin/console doctrine:schema:create`|Create the database schema. **(only run once)**|
|`docker-compose exec api bin/console assets:install`|Copy the UI assets so nginx can access them. **(only run once)**|
|`docker-compose down`|Stop Ledge.|

Please note that the two console commands should only be run the first time the application is built, or whenever the volumes are destroyed. They are designed to persist even when the containers are rebuilt.

Once built, run, and set up, Ledge should be accessible at http://localhost:22592/. The root path leads to the OpenAPI UI for convenient testing.
