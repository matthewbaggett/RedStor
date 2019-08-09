#FROM gone/php:nginx AS redstor
FROM gone/php:cli AS redstor

RUN apt-get -qq update && \
    apt-get -yq install --no-install-recommends \
        redis-tools \
        && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY redstor.runit /etc/service/redstor/run

RUN sed -i 's|disable_functions|#disabled_functions|g' /etc/php/7.3/cli/php.ini && \
    #sed -i 's|cat /etc/php/.*/fpm/conf.d/env.conf||g' /etc/service/php-fpm/run && \
    chmod +x \
        /app/bin/redstor \
        /etc/service/*/run

CMD bin/redstor

HEALTHCHECK NONE