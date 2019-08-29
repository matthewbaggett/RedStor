.PHONY: tests redstor-restart redis-ping clean redis-benchmark redis-benchmark-real redstor-cli

SHELL:=/bin/bash

clean:
	if test -f vendor/bin/php-cs-fixer ; then \
		vendor/bin/php-cs-fixer fix; \
		echo "Cleaned!"; \
	fi;

tests: clean
	docker-compose up -d
	docker-compose scale redstor=1
	docker-compose run --rm redstor \
		vendor/bin/phpunit \
			--stop-on-error \
			--stop-on-failure \
			--no-coverage \
			--debug

redis-ping:
	docker-compose run --rm redis \
		redis-cli -h socat -p 6379 PING arse

redstor-restart:
	docker-compose run --rm redis \
		redis-cli -h socat -p 6379 RESTART

redstor-cli:
	docker-compose run --rm redis \
		redis-cli -h socat -p 6379

redis-benchmark:
	echo "RedStor:"
	docker-compose run --rm redis \
		redis-benchmark -h socat -c 10 -n 200 -q -t \
			ping,set,get,incr,lpush,rpush,lpop,rpop,sadd,hset,lrange,mset

redis-benchmark-real:
	echo "Real Redis:"
	docker-compose run --rm redis \
		redis-benchmark -h redis -c 10 -n 200 -q -t \
			ping,set,get,incr,lpush,rpush,lpop,rpop,sadd,hset,lrange,mset

build-redstor-prod:
	composer install --no-dev
	docker build -t redstor/redstor --target=redstor .

build-gateway-prod:
	composer install --no-dev
	docker build -t redstor/gateway --target=gateway .

build-prod: build-redstor-prod build-gateway-prod

push-redstor-prod:
	docker push redstor/redstor

push-gateway-prod:
	docker push redstor/gateway

push-prod: push-redstor-prod push-gateway-prod

deploy-demo-site:
	echo "I am a fish."

purge-demo-site:
	echo "I am another fish."

ngrok:
	ngrok \
		start \
			-config ~/.ngrok2/ngrok.yml \
			-config .ngrok.yml \
				gateway
