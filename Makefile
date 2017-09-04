BOX=vendor-bin/box/vendor/bin/box
PHPUNIT=vendor/bin/phpunit
PHPSCOPER=bin/php-scoper.phar

.DEFAULT_GOAL := help
.PHONY: build test tu tc e2e tb


help:
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'


##
## Build
##---------------------------------------------------------------------------

build:		## Build the PHAR
build: src vendor vendor-bin/box/vendor
	# Cleanup existing artefacts
	rm -f bin/php-scoper.phar
	rm -rf build

	composer install --no-dev --prefer-dist

	php -d zend.enable_gc=0 bin/php-scoper add-prefix --force
	composer dump-autoload -d build  --classmap-authoritative
	php -d zend.enable_gc=0 -d phar.readonly=0 $(BOX) build $(CONFIG)

	# Install back all the dependencies
	composer install


##
## Tests
##---------------------------------------------------------------------------

test:		## Run all the tests
test: tu e2e

tu:		## Run PHPUnit tests
tu: vendor
	php -d zend.enable_gc=0 $(PHPUNIT)

tc:		## Run PHPUnit tests with test coverage
tc: vendor
	phpdbg -qrr -d zend.enable_gc=0 $(PHPUNIT) --coverage-html=dist/coverage --coverage-text

e2e:		## Run end-to-end tests
e2e: bin/scoper.phar fixtures/set005/vendor fixtures/set011/vendor
	# Set004
	php -d zend.enable_gc=0 $(PHPSCOPER) add-prefix fixtures/set004 -o build/set004 -f
	composer -d=build/set004 dump-autoload
	php -d zend.enable_gc=0 -d phar.readonly=0 $(BOX) build -c build/set004/box.json.dist

	php build/set004/bin/greet.phar > build/output
	diff fixtures/set004/expected-output build/output


	# Set005
	php -d zend.enable_gc=0 $(PHPSCOPER) add-prefix fixtures/set005 -o build/set005 -f
	composer -d=build/set005 dump-autoload
	php -d zend.enable_gc=0 -d phar.readonly=0 $(BOX) build -c build/set005/box.json.dist

	php build/set005/bin/greet.phar > build/output
	diff fixtures/set005/expected-output build/output


	# Set0011
	# Skipped for now
#	# TMP: replace bin/php-scoper by $(PHPSCOPER)
#	php -d zend.enable_gc=0 bin/php-scoper add-prefix -o build/set011 -f -c fixtures/set011/scoper.inc.php -p PhpScoper598627d709dd4
#	# Leave the `tests` autoloading of the `composer.json` intact
#	cp -f build/set011/composer.json.dist build/set011/composer.json
#	composer -d=build/set011 dump-autoload
#	php -d zend.enable_gc=0 -d phar.readonly=0 $(BOX) build -c build/set011/box.json.dist
#	cp -R fixtures/set011/tests build/set011
#
#	php build/set011/bin/greet.phar > build/output
#	diff fixtures/set011/expected-output build/output

tb:		## Run Blackfire profiling
tb: vendor
	rm -rf build

	composer install --no-dev --prefer-dist --classmap-authoritative
	blackfire --new-reference run bin/php-scoper add-prefix -f -q
	composer install


##
## Rules from files
##---------------------------------------------------------------------------

vendor: composer.lock
	composer install

vendor-bin/box/vendor: vendor-bin/box/composer.lock
	composer bin all install

fixtures/set005/vendor: fixtures/set005/composer.lock
	 composer -d=fixtures/set005 install

fixtures/set011/vendor: fixtures/set011/composer.lock
	 composer -d=fixtures/set011 install

composer.lock: composer.json
	@echo compose.lock is not up to date.

vendor-bin/box/composer.lock: vendor-bin/box/composer.json
	@echo compose.lock is not up to date.

bin/scoper.phar: bin/php-scoper src vendor scoper.inc.php
	$(MAKE) CONFIG="-c box.json.dist" build
