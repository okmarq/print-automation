FROM richarvey/nginx-php-fpm:3.1.6

COPY . .

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
ENV APP_NAME="Print Automation"
ENV FILESYSTEM_DISK=public
ENV MAIL_MAILER=smtp
ENV MAIL_HOST=sandbox.smtp.mailtrap.io
ENV MAIL_PORT=2525
ENV MAIL_USERNAME=5bd6dbcc14ec57
ENV MAIL_PASSWORD=3c8a132fbdf2bf
ENV MAIL_ENCRYPTION=tls
ENV MAIL_FROM_ADDRESS=okmarq@gmail.com
ENV MAIL_FROM_NAME="${APP_NAME}"
ENV QUEUE_CONNECTION=database


# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]
