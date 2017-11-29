.PHONY: all test build

all: test build

test:
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php composer-setup.php
	php -r "unlink('composer-setup.php');"

	php composer.phar install
	php -r "unlink('composer.phar');"

	php -d mbstring.func_overload=0 -d phar.readonly=0 ./vendor/bin/phpunit

build:
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php composer-setup.php
	php -r "unlink('composer-setup.php');"

	php composer.phar global require kherge/box
	php composer.phar install --no-dev
	php -r "unlink('composer.phar');"

	php -d mbstring.func_overload=0 -d phar.readonly=0 ~/.composer/vendor/bin/box build
