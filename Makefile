.PHONY: tests redstor-restart redis-ping clean redis-benchmark redis-benchmark-real redstor-cli

clean:
	-@vendor/bin/php-cs-fixer fix

tests: clean
	docker-compose scale redstor=1
	docker-compose run --rm redstor \
		vendor/bin/phpunit \
			--stop-on-error \
			--stop-on-failure \
			--no-coverage \
			--testsuite=StaticModels

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