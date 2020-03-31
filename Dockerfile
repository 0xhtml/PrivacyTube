FROM php:7.2-cli
RUN apt-get update && apt-get install -y default-mysql-client cron
RUN docker-php-ext-install mysqli

COPY . /usr/src/PrivacyTube
WORKDIR /usr/src/PrivacyTube

ENV mysql_host=localhost \
    mysql_user=root \
    mysql_pass= \
    mysql_db=privacytube \
    api_key=

RUN touch /var/log/cron.log
RUN echo "0 * * * * cd /usr/src/PrivacyTube && php utils/cron.php >> /var/log/cron.log" > /etc/cron.d/privacytube
RUN chmod 0644 /etc/cron.d/privacytube
RUN crontab /etc/cron.d/privacytube

CMD ./utils/wait-for-mysql.sh && php utils/setup.php && cron && php -S 0.0.0.0:80 -t web
