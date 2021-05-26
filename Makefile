#Makefile

install:
	composer install
getdiff:
	php bin/gendiff
validate:
	composer validate
lint:
	composer run-script phpcs -- --standard=PSR12 src bin

