#FROM nginx:latest
#EXPOSE 80 443
#-----
#WORKDIR /usr/share/nginx/html
#CMD 运行以下命令
#CMD ["nginx"]
#docker  run --name some-nginx2 -d -p 13337:80 256f90fc81f5
#------
FROM php:8.0-cli
RUN pecl install redis-5.3.4 \
    && pecl install swoole \
    && docker-php-ext-enable redis swoole
#    && php -r "copy('https://install.phpcomposer.com/installer', 'composer-setup.php');" \
#    && php composer-setup.php \
#    && php -r "unlink('composer-setup.php');" \
#    && mv composer.phar /usr/local/bin/composer \
COPY / /var/www/html
WORKDIR /var/www/html
CMD ["php","./index.php"]
#运行dockerfile创建镜像：docker build -t php7.4-cli:v1 .
#docker run -it --name php7.4-cli-v1 -d -p 13338:9500 0a15a3bb6aef
#docker run -it --rm --name php001 -p 13338:9501 b93e8f3904b2 dockerfile启动cmd