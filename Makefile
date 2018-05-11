.DEFAULT_GOAL := help

PHPNOGC=php -d zend.enable_gc=0

.PHONY: help
help:
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'


##
## Build
##---------------------------------------------------------------------------

.PHONY: clean
clean:		## Clean all created artifacts
clean:
	git clean --exclude=.idea/ -ffdx

.PHONY: build
build:		## Build the PHAR
BOX=vendor-bin/box/vendor/bin/box
build: bin/php-scoper.phar


##
## Tests
##---------------------------------------------------------------------------

.PHONY: test
test:		## Run all the tests
test: tc e2e

.PHONY: tu
PHPUNIT=vendor/bin/phpunit
tu:		## Run PHPUnit tests
tu: vendor/bin/phpunit
	$(PHPNOGC) $(PHPUNIT)

.PHONY: tc
COVERS_VALIDATOR=$(PHPNOGC) vendor-bin/covers-validator/bin/covers-validator
tc:		## Run PHPUnit tests with test coverage
tc: vendor/bin/phpunit vendor-bin/covers-validator/vendor
	$(COVERS_VALIDATOR)
	phpdbg -qrr -d zend.enable_gc=0 $(PHPUNIT) --coverage-html=dist/coverage --coverage-text --coverage-clover=clover.xml --coverage-xml=dist/infection-coverage/coverage-xml --log-junit=dist/infection-coverage/phpunit.junit.xml

.PHONY: tm
tm:		## Run Infection (Mutation Testing)
tm: vendor/bin/phpunit
	$(MAKE) e2e_020

.PHONY: e2e
e2e:		## Run end-to-end tests
e2e: e2e_004 e2e_005 e2e_011 e2e_013 e2e_014 e2e_015 e2e_016 e2e_017 e2e_018 e2e_019 e2e_020 e2e_021

PHPSCOPER=bin/php-scoper.phar

.PHONY: e2e_004
e2e_004:	## Run end-to-end tests for the fixture set 004: source code case
e2e_004: bin/php-scoper.phar
	$(PHPNOGC) $(BOX) compile --working-dir fixtures/set004

	php build/set004/bin/greet.phar > build/set004/output
	diff fixtures/set004/expected-output build/set004/output

.PHONY: e2e_005
e2e_005:	## Run end-to-end tests for the fixture set 005: third-party code case
e2e_005: bin/php-scoper.phar fixtures/set005/vendor
	$(PHPNOGC) $(BOX) compile --working-dir fixtures/set005

	php build/set005/bin/greet.phar > build/set005/output
	diff fixtures/set005/expected-output build/set005/output

.PHONY: e2e_011
e2e_011:	## Run end-to-end tests for the fixture set 011: whitelist case
e2e_011: bin/php-scoper.phar fixtures/set011/vendor
	$(PHPNOGC) $(BOX) compile --working-dir fixtures/set011

	php build/set011/bin/greet.phar > build/set011/output
	diff fixtures/set011/expected-output build/set011/output

.PHONY: e2e_013
e2e_013:	# Run end-to-end tests for the fixture set 013: the init command
e2e_013: bin/php-scoper.phar
	rm -rf build/set013
	cp -R fixtures/set013 build/set013
	$(PHPSCOPER) init --working-dir=build/set013 --no-interaction
	diff src/scoper.inc.php.tpl build/set013/scoper.inc.php

.PHONY: e2e_014
e2e_014:	## Run end-to-end tests for the fixture set 014: source code case with psr-0
e2e_014: bin/php-scoper.phar
	$(PHPNOGC) $(BOX) compile --working-dir fixtures/set014

	php build/set014/bin/greet.phar > build/set014/output
	diff fixtures/set014/expected-output build/set014/output

.PHONY: e2e_015
e2e_015:	## Run end-to-end tests for the fixture set 015: third-party code case with psr-0
e2e_015: bin/php-scoper.phar fixtures/set015/vendor
	$(PHPNOGC) $(BOX) compile --working-dir fixtures/set015

	php build/set015/bin/greet.phar > build/set015/output
	diff fixtures/set015/expected-output build/set015/output

.PHONY: e2e_016
e2e_016:	## Run end-to-end tests for the fixture set 016: Symfony Finder
e2e_016: bin/php-scoper.phar fixtures/set016-symfony-finder/vendor
	$(PHPNOGC) $(PHPSCOPER) add-prefix --working-dir=fixtures/set016-symfony-finder --output-dir=../../build/set016-symfony-finder --force --no-config --no-interaction --stop-on-failure
	composer --working-dir=build/set016-symfony-finder dump-autoload

	php build/set016-symfony-finder/main.php > build/set016-symfony-finder/output
	diff fixtures/set016-symfony-finder/expected-output build/set016-symfony-finder/output

.PHONY: e2e_017
e2e_017:	## Run end-to-end tests for the fixture set 017: Symfony DependencyInjection
e2e_017: bin/php-scoper.phar fixtures/set017-symfony-di/vendor
	$(PHPNOGC) $(PHPSCOPER) add-prefix --working-dir=fixtures/set017-symfony-di --output-dir=../../build/set017-symfony-di --force --no-config --no-interaction --stop-on-failure
	composer --working-dir=build/set017-symfony-di dump-autoload

	php build/set017-symfony-di/main.php > build/set017-symfony-di/output
	diff fixtures/set017-symfony-di/expected-output build/set017-symfony-di/output

.PHONY: e2e_018
e2e_018:	## Run end-to-end tests for the fixture set 018: nikic PHP-Parser
e2e_018: bin/php-scoper.phar fixtures/set018-nikic-parser/vendor
	$(PHPNOGC) $(PHPSCOPER) add-prefix --working-dir=fixtures/set018-nikic-parser --prefix=_Prefixed --output-dir=../../build/set018-nikic-parser --force --no-interaction --stop-on-failure
	composer --working-dir=build/set018-nikic-parser dump-autoload

	php build/set018-nikic-parser/main.php > build/set018-nikic-parser/output
	diff fixtures/set018-nikic-parser/expected-output build/set018-nikic-parser/output

.PHONY: e2e_019
e2e_019:	## Run end-to-end tests for the fixture set 019: Symfony Console
e2e_019: bin/php-scoper.phar fixtures/set019-symfony-console/vendor
	$(PHPNOGC) $(PHPSCOPER) add-prefix --working-dir=fixtures/set019-symfony-console --prefix=_Prefixed --output-dir=../../build/set019-symfony-console --force --no-config --no-interaction --stop-on-failure
	composer --working-dir=build/set019-symfony-console dump-autoload

	php build/set019-symfony-console/main.php > build/set019-symfony-console/output
	diff fixtures/set019-symfony-console/expected-output build/set019-symfony-console/output

.PHONY: e2e_020
e2e_020:	## Run end-to-end tests for the fixture set 020: Infection
e2e_020: bin/php-scoper.phar fixtures/set020-infection/vendor clover.xml
	$(PHPNOGC) $(PHPSCOPER) add-prefix --working-dir=fixtures/set020-infection --output-dir=../../build/set020-infection --force --no-interaction --stop-on-failure
	composer --working-dir=build/set020-infection dump-autoload

	php fixtures/set020-infection/vendor/infection/infection/bin/infection --coverage=dist/infection-coverage > build/set020-infection/expected-output
	php build/set020-infection/vendor/infection/infection/bin/infection --coverage=dist/infection-coverage > build/set020-infection/output

	diff build/set020-infection/expected-output build/set020-infection/output

.PHONY: e2e_021
e2e_021:	## Run end-to-end tests for the fixture set 020: Composer
e2e_021: bin/php-scoper.phar fixtures/set021-composer/vendor clover.xml
	$(PHPNOGC) $(PHPSCOPER) add-prefix --working-dir=fixtures/set021-composer --output-dir=../../build/set021-composer --force --no-interaction --stop-on-failure --no-config
	composer --working-dir=build/set021-composer dump-autoload

	php fixtures/set021-composer/vendor/composer/composer/bin/composer licenses --no-plugins > build/set021-composer/expected-output
	php build/set021-composer/vendor/composer/composer/bin/composer licenses --no-plugins > build/set021-composer/output

	diff build/set021-composer/expected-output build/set021-composer/output

.PHONY: tb
BLACKFIRE=blackfire
tb:		## Run Blackfire profiling
tb: vendor
	rm -rf build
	rm -rf vendor-bin/*/vendor

	mv -f vendor tmp-back
	composer install --no-dev --prefer-dist --classmap-authoritative

	$(BLACKFIRE) --new-reference run $(PHPNOGC) bin/php-scoper add-prefix --output-dir=build/php-scoper --force --quiet

	rm -rf vendor
	mv -f tmp-back vendor

#
# Rules from files
#---------------------------------------------------------------------------

vendor: composer.lock
	composer install

vendor/bamarni: composer.lock
	composer install

vendor/bin/phpunit: composer.lock
	composer install

vendor-bin/box/vendor: vendor-bin/box/composer.lock vendor/bamarni
	composer bin all install

vendor-bin/covers-validator/vendor: vendor-bin/covers-validator/composer.lock vendor/bamarni
	composer bin covers-validator install

fixtures/set005/vendor: fixtures/set005/composer.lock
	composer --working-dir=fixtures/set005 install

fixtures/set011/vendor: fixtures/set011/vendor
	composer --working-dir=fixtures/set011 dump-autoload

fixtures/set015/vendor: fixtures/set015/composer.lock
	composer --working-dir=fixtures/set015 install

fixtures/set016-symfony-finder/vendor: fixtures/set016-symfony-finder/composer.lock
	composer --working-dir=fixtures/set016-symfony-finder install

fixtures/set017-symfony-di/vendor: fixtures/set017-symfony-di/composer.lock
	composer --working-dir=fixtures/set017-symfony-di install

fixtures/set018-nikic-parser/vendor: fixtures/set018-nikic-parser/composer.lock
	composer --working-dir=fixtures/set018-nikic-parser install

fixtures/set019-symfony-console/vendor: fixtures/set019-symfony-console/composer.lock
	composer --working-dir=fixtures/set019-symfony-console install

fixtures/set020-infection/vendor: fixtures/set020-infection/composer.lock
	composer --working-dir=fixtures/set020-infection install

fixtures/set021-composer/vendor: fixtures/set021-composer/composer.lock
	composer --working-dir=fixtures/set021-composer install

composer.lock: composer.json
	@echo composer.lock is not up to date.

vendor-bin/box/composer.lock: composer.lock
	@echo vendor-bin/box/composer.lock is not up to date.

vendor-bin/covers-validator/composer.lock: vendor-bin/covers-validator/composer.json
	@echo covers-validator composer.lock is not up to date

fixtures/set005/composer.lock: fixtures/set005/composer.json
	@echo fixtures/set005/composer.lock is not up to date.

fixtures/set015/composer.lock: fixtures/set015/composer.json
	@echo fixtures/set015/composer.lock is not up to date.

fixtures/set016-symfony-finder/composer.lock: fixtures/set016-symfony-finder/composer.json
	@echo fixtures/set016-symfony-finder/composer.lock is not up to date.

fixtures/set017-symfony-di/composer.lock: fixtures/set017-symfony-di/composer.json
	@echo fixtures/set017-symfony-di/composer.lock is not up to date.

fixtures/set018-nikic-parser/composer.lock: fixtures/set018-nikic-parser/composer.json
	@echo fixtures/set018-nikic-parser/composer.lock is not up to date.

fixtures/set019-symfony-console/composer.lock: fixtures/set019-symfony-console/composer.json
	@echo fixtures/set019-symfony-console/composer.lock is not up to date.

fixtures/set020-infection/composer.lock: fixtures/set020-infection/composer.json
	@echo fixtures/set020-infection/composer.lock is not up to date.

fixtures/set021-composer/composer.lock: fixtures/set021-composer/composer.json
	@echo fixtures/set021-composer/composer.lock is not up to date.

bin/php-scoper.phar: bin/php-scoper src vendor vendor-bin/box/vendor scoper.inc.php box.json
	$(BOX) compile

box.json: box.json.dist
	cat box.json.dist | sed -E 's/\"key\": \".+\",//g' | sed -E 's/\"algorithm\": \".+\",//g' > box.json

clover.xml: src
	$(MAKE) tc
