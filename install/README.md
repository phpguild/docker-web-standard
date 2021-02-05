# Project

## Installation

### 1. Configure project

Edit application name `APP_NAME := myapp` into `Makefile`

    sed -i'' "s/myapp/new_app_name/g" Makefile

### 2. Install local server

If you want install a local server (with reverse proxy)

    sudo make setup/server

### 3. Install project stack

    make install
    
## Usage

### Restart project stack

    make restart

### Down docker containers

    make docker/down
