test:
	./vendor/bin/phpunit tests/ApiTest.php

install:
	composer install && npm install

openapi:
	./vendor/bin/openapi --output public/swagger-ui/swagger.yaml ./App/Controllers/