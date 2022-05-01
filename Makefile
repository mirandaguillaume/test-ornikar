vendor:
	docker run --rm --name test-back-ornikar -v ${PWD}:/usr/src/app thecodingmachine/php:8.1-v4-cli composer install


.PHONY: test
test: vendor
	docker run --rm --name test-back-ornikar -v ${PWD}:/usr/src/app thecodingmachine/php:8.1-v4-cli phpunit
