# BifrostPHP - Back-end Module

[![Repo](https://img.shields.io/badge/Bifrost-Back-blue)](./)

[![link-readme-inglês](https://img.shields.io/badge/README-English/Inglês-red)](./README.md)
[![link-readme-Portugês](https://img.shields.io/badge/README-Portuguese/Portugês-green)](./README-PT.md)

This repository contains the BifrostPHP back-end source code base.

You can find more information about the project in the official repository on GitHub: [BifrostPHP](https://github.com/Felipe-Cavalca/BifrostPHP)

## Installation

To run the project, you will need to have Docker and Docker Compose installed on your machine.

Follow the steps below to run Docker Compose:

1. Open a terminal and navigate to the project's `api` folder.

2. Run the following command to build and start the Docker containers:

    ```bash
    docker-compose up -d
    ```

    This will download the images easier, build the containers and start the services.

3. Once complete, you can access the api in your browser using the following URL:

    ```http
    http://localhost:28080
    ```

## Documentation

To generate project documentation in HTML format, run the following command:

```bash
docker run --rm -v "$(pwd):/data" "phpdoc/phpdoc:3" run -d ./api -t ./docs/api
```

## Sponsored by

* @Felipe-Cavalca
