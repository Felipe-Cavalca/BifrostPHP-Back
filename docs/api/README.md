# BifrostPHP API Documentation

This folder contains reference information for the API module. A Portuguese version is available in [README-PT.md](README-PT.md).

## Overview

BifrostPHP is a minimalist framework aimed at building small APIs using Docker as the default development environment. This repository represents the back-end module written in PHP.

## Folder structure

```
api/
├── Attributes    # Custom PHP attributes
├── Class         # Core classes used by the framework
├── Controller    # Endpoint controllers
├── Core          # Request handling and utilities
├── Model         # Database models
├── DataTypes     # Strongly typed value objects
├── Enum          # Enum definitions
├── Interface     # Interface contracts
├── tests         # Example requests for functional testing
└── docs          # This documentation
```

## Running the API locally

1. Install [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/install/).

2. Create a `.env` file based on the `.env.example` file.

3. Navigate to the `api` directory.

4. Start the containers:
   ```bash
   docker-compose up -d
   ```

5. Access the API at [http://localhost:80](http://localhost:80).
