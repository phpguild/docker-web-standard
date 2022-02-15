# __dwsmyapp__

## Environments

* Live: https://__dwsmyapp__.tld
* Staging: https://staging.__dwsmyapp__.tld
* Testing: https://__dwsmyapp__.test
* Local: https://__dwsmyapp__.local.test

## Prerequisites

 - Nginx
 - Openssl

## Installation

Create `.env.local`

    ###> phpguild/docker-web-standard ###
    APP_ENV=dev
    APP_DEBUG=1
    APP_PORT={custom_local_port}
    APP_INSTANCE=local
    ###< phpguild/docker-web-standard ###

Run command

    make -e APP_NAME=__dwsmyapp__ install

## Usage

### Restart

    make restart

### Down

    make down
