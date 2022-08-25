#FROM nginx:latest
#EXPOSE 80 443
#-----
#WORKDIR /usr/share/nginx/html
#CMD 运行以下命令
#CMD ["nginx"]
#docker  run --name some-nginx2 -d -p 13337:80 256f90fc81f5
#------
FROM php:7.4-cli
RUN pecl install redis-5.1.1 \
    && pecl install xdebug-2.8.1 \
    && pecl install swoole \
    && docker-php-ext-enable redis xdebug swoole \
    && php -r "copy('https://install.phpcomposer.com/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer \
#运行dockerfile创建镜像：docker build -t php7.4-cli:v1 .
#docker run -it --name php7.4-cli-v1 -d -p 13338:9500 0a15a3bb6aef