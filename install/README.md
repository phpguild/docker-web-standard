# Project

## Installation

### 1. Require from composer

    composer req phpguild/docker-web-standard

### 2. Configure project

Edit application name `APP_NAME := myapp` into `Makefile`

    sed -i "s/myapp/new_app_name/g" Makefile

### 3. Install local server

If you want install a local server (with reverse proxy)

    sudo make setup/server

### 4. Install project stack

    make install
    
## Usage

### Restart project stack

    make restart

### Down docker containers

    make docker/down
