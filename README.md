# demo url-shortner
Demo available on [AWS](http://18.202.239.19)

URL's should be in 'http://example.com' format

## Setup Instructions

### With Docker

```
git clone https://github.com/JackRawlinson/url-shortner.git
cd url-shortner

composer install
docker-compose build
docker-compose up
```

Navigate to 127.0.0.1:8090


### Other Method

make sure php-mysql php-bcmath are installed
edit .env with mysql credentials (DB_HOST has a non standard port set by default 3305)

```
git clone https://github.com/JackRawlinson/url-shortner.git
cd url-shortner

composer install
php -S 127.0.0.1:8090 -t public/
````
Navigate to 127.0.0.1:8090

Database import can be found inside images/mysql/import.sql
