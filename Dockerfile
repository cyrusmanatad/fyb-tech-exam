FROM markhewitt/php-nginx:php8.4

# Switch to use a non-root user from here on
USER root

# Configure nginx
RUN rm /etc/nginx/nginx.conf
RUN rm /etc/nginx/conf.d/default.conf
COPY config/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
RUN ls /etc/php84/
RUN rm /etc/php84/php-fpm.d/www.conf
RUN rm /etc/php84/conf.d/custom.ini
COPY config/fpm-pool.conf /etc/php84/php-fpm.d/www.conf
COPY config/php.ini /etc/php84/conf.d/custom.ini

# Configure supervisord
RUN rm /etc/supervisor/conf.d/supervisord.conf
RUN rm /etc/supervisord.conf
COPY config/supervisord.conf /etc/supervisord.conf

# Install PHP8-LDAP module
RUN apk update && apk add php-ldap wget tzdata

# Switch to use a non-root user from here on
USER nobody

# Add application
WORKDIR /var/app

COPY --chown=nobody /src /var/app/
RUN mkdir -p /var/app/writable/cache

# Expose the port nginx is reachable on
EXPOSE 8080

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping
