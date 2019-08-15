FROM gone/php:cli AS redstor

RUN apt-get -qq update && \
    apt-get -yq install --no-install-recommends \
        redis-tools \
        && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY redstor.runit /etc/service/redstor/run

RUN sed -i 's|disable_functions|#disabled_functions|g' /etc/php/7.3/cli/php.ini
    #sed -i 's|cat /etc/php/.*/fpm/conf.d/env.conf||g' /etc/service/php-fpm/run

HEALTHCHECK --interval=10s --timeout=3s \
    CMD redis-cli PING

ADD . /app

RUN chmod +x \
        /app/bin/redstor \
        /etc/service/*/run

FROM gone/php:nginx AS gateway
RUN apt-get -qq update && \
    apt-get -yq install --no-install-recommends \
        redis-tools \
        && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

#RUN sed -i 's|/app/public|/app/vendor/benzine/benzine-html/public|g' /etc/nginx/sites-enabled/default

# Create a healthcheck that makes sure our httpd is up
HEALTHCHECK --interval=30s --timeout=3s \
    CMD curl -f http://localhost/v1/ping || exit 1

ADD . /app