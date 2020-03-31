FROM php:7.2-cli
RUN apt-get update && apt-get install -y default-mysql-client
RUN docker-php-ext-install mysqli

COPY . /usr/src/PrivacyTube
WORKDIR /usr/src/PrivacyTube

ENV mysql_host=localhost \
    mysql_user=root \
    mysql_pass= \
    mysql_db=privacytube \
    api_key=

CMD ./utils/wait-for-mysql.sh && php utils/setup.php && php -S 0.0.0.0:80 -t web
