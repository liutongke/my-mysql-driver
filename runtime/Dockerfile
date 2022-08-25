FROM nginx:latest AS nginx
EXPOSE 80 443
COPY ./public/chatroom/ /usr/share/nginx/html
#docker run --name nginx1 -p 14006:80 -v /c/Users/Administrator/docker/lnmp1/public:/usr/share/nginx/html -d nginx
#docker run --name nginx2 -p 15000:80 -d nginx-t:v2

FROM php:7.4-cli AS php7
RUN pecl install redis-5.1.1 \
    && pecl install xdebug-2.8.1 \
    && pecl install swoole \
    && docker-php-ext-enable redis xdebug swoole \
#    && php -r "copy('https://install.phpcomposer.com/installer', 'composer-setup.php');" \
#    && php composer-setup.php \
#    && php -r "unlink('composer-setup.php');" \
#    && mv composer.phar /usr/local/bin/composer \
#运行dockerfile创建镜像：docker build -t php7.4-cli:v1 .
#docker run -it --name php7.4-cli-v1 -d -p 13338:9500 0a15a3bb6aef
#公用Dockerfile文件：https://blog.csdn.net/u013272009/article/details/83901713