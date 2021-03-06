FROM ubuntu:latest
RUN sed -i 's/archive.ub/ru.archive.ub/g' /etc/apt/sources.list
RUN apt update && apt upgrade -f -y &&  apt install software-properties-common wget curl -f -y
RUN add-apt-repository ppa:ondrej/php -y -u
RUN apt update && apt install -f -y php7.4-bcmath php7.4-bz2 php7.4-intl php7.4-gd php7.4-mbstring php7.4-mysql php7.4-cli php7.4-zip -f -y
RUN apt install git php7.4-xml -f -y
RUN apt purge apache2 -f -y
RUN mkdir /telegram
RUN mkdir /tbin
WORKDIR /telegram
RUN git clone https://github.com/xtrime-ru/TelegramApiServer .
WORKDIR /tbin
RUN wget -O composer-setup.php https://getcomposer.org/installer && php composer-setup.php
WORKDIR /telegram
RUN php /tbin/composer.phar install -o
COPY run.sh /tbin/run.sh
COPY export.php /tbin/export.php
CMD ["/bin/bash","/tbin/run.sh"]

