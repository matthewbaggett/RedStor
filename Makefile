.PHONY: tests redstor-restart redis-ping

tests: redstor-restart
	docker-compose run --rm redstor \
		vendor/bin/phpunit \
			--stop-on-error \
			--stop-on-failure \
			--no-coverage

redis-ping:
	redis-cli -h 127.0.0.54 -p 6379 PING arse

redstor-restart:
	redis-cli -h 127.0.0.54 -p 6379 RESTART

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