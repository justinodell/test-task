# Word Density App

The App uses a message queue to run the jobs in the background. 

## Install

- Install dependencies with: `composer install`
- Run DB migrations: `doctrine:migrations:migrate --no-interaction`
- Load fixtures: `doctrine:fixtures:load --no-interaction`
- Run message queue consumer: `messenger:consume async`

## Usage

### Web server
Optional - if you already have local http server running, skip this.

- Start local server: `symfony server:start`
- Open browser: `symfony open:local`

### Login and add URLs

- Go to: `https://127.0.0.1:8000/login`
- Enter username/password: `admin@example.com` `password`
- From the left menu choose: `Word Density`
- Play ;)
