# See https://tech.davis-hansson.com/p/make/
MAKEFLAGS += --warn-undefined-variables
MAKEFLAGS += --no-builtin-rules

IS_PHP8=$(shell php -r "echo version_compare(PHP_VERSION, '8.0.0', '>=') ? 'true' : 'false';")
SRC_FILES=$(shell find bin/ src/ vendor-hotfix/ -type f)

PHP_SCOPER_PHAR_BIN=bin/php-scoper.phar
PHP_SCOPER_PHAR=$(PHP_SCOPER_PHAR_BIN)

COMPOSER_BIN_PLUGIN_VENDOR=vendor/bamarni/composer-bin-plugin

PHPSTAN_BIN=vendor-bin/phpstan/vendor/bin/phpstan
PHPSTAN=$(PHPSTAN_BIN) analyze src tests --level max --memory-limit=-1

BOX_BIN=bin/box
BOX=$(BOX_BIN)

COVERAGE_DIR = dist/coverage
COVERAGE_XML = $(COVERAGE_DIR)/xml
COVERAGE_HTML = $(COVERAGE_DIR)/html

PHPUNIT_BIN=bin/phpunit
PHPUNIT=$(PHPUNIT_BIN)
PHPUNIT_COVERAGE_INFECTION = XDEBUG_MODE=coverage $(PHPUNIT) --coverage-xml=$(COVERAGE_XML) --log-junit=$(COVERAGE_DIR)/phpunit.junit.xml
PHPUNIT_COVERAGE_HTML = XDEBUG_MODE=coverage $(PHPUNIT) --coverage-html=$(COVERAGE_HTML)

COVERS_VALIDATOR_BIN=vendor-bin/covers-validator/bin/covers-validator
COVERS_VALIDATOR=$(COVERS_VALIDATOR_BIN)

PHP_CS_FIXER_BIN = vendor-bin/php-cs-fixer/vendor/friendsofphp/php-cs-fixer/php-cs-fixer
PHP_CS_FIXER = $(PHP_CS_FIXER_BIN) fix

BLACKFIRE=blackfire


.DEFAULT_GOAL := help


.PHONY: help
help:
	@echo "\033[33mUsage:\033[0m\n  make TARGET\n\n\033[32m#\n# Commands\n#---------------------------------------------------------------------------\033[0m\n"
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//' | awk 'BEGIN {FS = ":"}; {printf "\033[33m%s:\033[0m%s\n", $$1, $$2}'


#
# Commands
#---------------------------------------------------------------------------

.PHONY: check
check:	 ## Runs all checks
check: update_root_version cs composer_normalize phpstan test

.PHONY: clean
clean:	 ## Cleans all created artifacts
clean:
	git clean --exclude=.idea/ -ffdx

update_root_version: ## Checks the latest GitHub release and update COMPOSER_ROOT_VERSION accordingly
update_root_version:
	rm .composer-root-version || true
	$(MAKE) .composer-root-version

.PHONY: cs
cs:	 ## Fixes CS
cs: composer_normalize php_cs_fixer

.PHONY: cs_lint
cs_lint: ## Checks CS
cs_lint: composer_normalize_lint php_cs_fixer_lint

.PHONY: php_cs_fixer
php_cs_fixer: $(PHP_CS_FIXER_BIN)
	$(PHP_CS_FIXER)

.PHONY: php_cs_fixer_lint
php_cs_fixer_lint: $(PHP_CS_FIXER_BIN)
	$(PHP_CS_FIXER) --dry-run

.PHONY: composer_normalize
composer_normalize: composer.json vendor
	composer normalize

.PHONY: composer_normalize_lint
composer_normalize_lint: composer.json vendor
	composer normalize --dry-run

.PHONY: phpstan
phpstan: ## Runs PHPStan
phpstan: $(PHPSTAN_BIN)
	$(PHPSTAN)

.PHONY: build
build:	 ## Builds the PHAR
build:
	rm $(PHP_SCOPER_PHAR_BIN) || true
	$(MAKE) $(PHP_SCOPER_PHAR_BIN)

.PHONY: outdated_fixtures
outdated_fixtures: ## Reports outdated dependencies
outdated_fixtures:
	find fixtures -name 'composer.json' -type f -depth 2 -exec dirname '{}' \; | xargs -I % sh -c 'echo "Checking %;" $$(composer install --working-dir=% --ansi && composer outdated --direct --working-dir=% --ansi)'


#
# Tests
#---------------------------------------------------------------------------

.PHONY: test
test:		   ## Runs all the tests
test: check_composer_root_version validate_package covers_validator phpunit e2e

.PHONY: validate_package
validate_package:  ## Validates the composer.json
validate_package:
	composer validate --strict

.PHONY: check_composer_root_version
check_composer_root_version: ## Checks that the COMPOSER_ROOT_VERSION is up to date
check_composer_root_version: .composer-root-version
	cd composer-root-version-checker; $(MAKE) --makefile Makefile check_root_version

.PHONY: covers_validator
covers_validator:  ## Checks PHPUnit @coves tag
covers_validator: $(COVERS_VALIDATOR_BIN)
	$(COVERS_VALIDATOR)

.PHONY: phpunit
phpunit:	   ## Runs PHPUnit tests
phpunit: $(PHPUNIT_BIN) vendor
	$(PHPUNIT)

.PHONY: phpunit_coverage_infection
phpunit_coverage_infection: ## Runs PHPUnit tests with test coverage
phpunit_coverage_infection: $(PHPUNIT_BIN) vendor
	$(PHPUNIT_COVERAGE_INFECTION)

.PHONY: phpunit_coverage_html
phpunit_coverage_html:	    ## Runs PHPUnit with code coverage with HTML report
phpunit_coverage_html: $(PHPUNIT_BIN) vendor
	$(PHPUNIT_COVERAGE_HTML)

.PHONY: infection
infection:	   ## Runs Infection
infection: $(COVERAGE_XML) vendor
#infection: $(INFECTION_BIN) $(COVERAGE_XML) vendor
	if [ -d $(COVERAGE_XML) ]; then $(INFECTION); fi

.PHONY: blackfire
blackfire:	   ## Runs Blackfire profiling
blackfire: vendor
	@echo "By https://blackfire.io"
	@echo "This might take a while (~2min)"
	$(BLACKFIRE) run php bin/php-scoper add-prefix --output-dir=build/php-scoper --force --quiet

.PHONY: e2e
e2e:	 ## Runs end-to-end tests
e2e: e2e_004 e2e_005 e2e_011 e2e_013 e2e_014 e2e_015 e2e_016 e2e_017 e2e_018 e2e_019 e2e_020 e2e_024 e2e_025 e2e_027 e2e_028 e2e_029 e2e_030 e2e_031 e2e_032 e2e_033 e2e_034 e2e_035

.PHONY: e2e_004
e2e_004: ## Runs end-to-end tests for the fixture set 004 — Minimalistic codebase
e2e_004: $(PHP_SCOPER_PHAR_BIN)
	# Having those composer files messes up the Box auto-loading detection. This
	# is a very special case where there is no dependency and for users in practice
	# it would be recommended to register the files themselves
	rm fixtures/set004/composer.lock || true
	rm -rf fixtures/set004/vendor || true

	$(BOX) compile --no-parallel --working-dir fixtures/set004

	php build/set004/bin/greet.phar > build/set004/output
	diff fixtures/set004/expected-output build/set004/output

.PHONY: e2e_005
e2e_005: ## Runs end-to-end tests for the fixture set 005 — Codebase with third-party code
e2e_005: $(PHP_SCOPER_PHAR_BIN) fixtures/set005/vendor
	$(BOX) compile --no-parallel --working-dir fixtures/set005

	php build/set005/bin/greet.phar > build/set005/output
	diff fixtures/set005/expected-output build/set005/output

.PHONY: e2e_011
e2e_011: ## Runs end-to-end tests for the fixture set 011 — Codebase with exposed symbols
e2e_011: $(PHP_SCOPER_PHAR_BIN) fixtures/set011/vendor
	$(BOX) compile --no-parallel --working-dir fixtures/set011
	cp -R fixtures/set011/tests/ build/set011/tests/

	php build/set011/bin/greet.phar > build/set011/output
	diff fixtures/set011/expected-output build/set011/output

.PHONY: e2e_013
e2e_013: ## Runs end-to-end tests for the fixture set 013 — Test the init command
e2e_013: $(PHP_SCOPER_PHAR_BIN)
	rm -rf build/set013 || true
	mkdir -p build
	cp -R fixtures/set013 build/set013

	$(PHP_SCOPER_PHAR_BIN) init --working-dir=build/set013 --no-interaction

	diff src/scoper.inc.php.tpl build/set013/scoper.inc.php

.PHONY: e2e_014
e2e_014: ## Runs end-to-end tests for the fixture set 014 — Codebase with PSR-0 autoloading
e2e_014: $(PHP_SCOPER_PHAR_BIN)
	# Having those composer files messes up the Box auto-loading detection. This
	# is a very special case where there is no dependency and for users in practice
	# it would be recommended to register the files themselves
	rm fixtures/set014/composer.lock || true
	rm -rf fixtures/set014/vendor || true

	$(BOX) compile --no-parallel --working-dir fixtures/set014

	php build/set014/bin/greet.phar > build/set014/output
	diff fixtures/set014/expected-output build/set014/output

.PHONY: e2e_015
e2e_015: ## Runs end-to-end tests for the fixture set 015 — Codebase with third-party code using PSR-0 autoloading
e2e_015: $(PHP_SCOPER_PHAR_BIN) fixtures/set015/vendor
	$(BOX) compile --no-parallel --working-dir fixtures/set015

	php build/set015/bin/greet.phar > build/set015/output
	diff fixtures/set015/expected-output build/set015/output

.PHONY: e2e_016
e2e_016: ## Runs end-to-end tests for the fixture set 016 — Scoping of the Symfony Finder component
e2e_016: $(PHP_SCOPER_PHAR_BIN) fixtures/set016-symfony-finder/vendor
	$(PHP_SCOPER_PHAR) add-prefix \
		--working-dir=fixtures/set016-symfony-finder \
		--output-dir=../../build/set016-symfony-finder \
		--force \
		--no-config \
		--no-interaction \
		--stop-on-failure
	composer --working-dir=build/set016-symfony-finder dump-autoload

	php build/set016-symfony-finder/main.php > build/set016-symfony-finder/output
	diff fixtures/set016-symfony-finder/expected-output build/set016-symfony-finder/output

.PHONY: e2e_017
e2e_017: ## Runs end-to-end tests for the fixture set 017 — Scoping of the Symfony DependencyInjection component
e2e_017: $(PHP_SCOPER_PHAR_BIN) fixtures/set017-symfony-di/vendor
	$(PHP_SCOPER_PHAR) add-prefix \
		--working-dir=fixtures/set017-symfony-di \
		--output-dir=../../build/set017-symfony-di \
		--force \
		--no-config \
		--no-interaction \
		--stop-on-failure
	composer --working-dir=build/set017-symfony-di dump-autoload

	php build/set017-symfony-di/main.php > build/set017-symfony-di/output
	diff fixtures/set017-symfony-di/expected-output build/set017-symfony-di/output

.PHONY: e2e_018
e2e_018: ## Runs end-to-end tests for the fixture set 018 — Scoping of nikic/php-parser
e2e_018: $(PHP_SCOPER_PHAR_BIN) fixtures/set018-nikic-parser/vendor
	$(PHP_SCOPER_PHAR) add-prefix \
		--working-dir=fixtures/set018-nikic-parser \
		--prefix=_Prefixed \
		--output-dir=../../build/set018-nikic-parser \
		--force \
		--no-interaction \
		--stop-on-failure
	composer --working-dir=build/set018-nikic-parser dump-autoload

	php build/set018-nikic-parser/main.php > build/set018-nikic-parser/output
	diff fixtures/set018-nikic-parser/expected-output build/set018-nikic-parser/output

.PHONY: e2e_019
e2e_019: ## Runs end-to-end tests for the fixture set 019 — Scoping of the Symfony Console component
e2e_019: $(PHP_SCOPER_PHAR_BIN) fixtures/set019-symfony-console/vendor
	$(PHP_SCOPER_PHAR) add-prefix --working-dir=fixtures/set019-symfony-console \
		--prefix=_Prefixed \
		--output-dir=../../build/set019-symfony-console \
		--force \
		--no-config \
		--no-interaction \
		--stop-on-failure
	composer --working-dir=build/set019-symfony-console dump-autoload

	php build/set019-symfony-console/main.php > build/set019-symfony-console/output
	diff fixtures/set019-symfony-console/expected-output build/set019-symfony-console/output

.PHONY: e2e_020
e2e_020: ## Runs end-to-end tests for the fixture set 020 — Scoping of Infection
e2e_020: $(PHP_SCOPER_PHAR_BIN) fixtures/set020-infection/vendor
# Skip it for now: there is autoloading issues with the Safe functions
#	$(PHP_SCOPER_PHAR) add-prefix --working-dir=fixtures/set020-infection \
#		--output-dir=../../build/set020-infection \
#		--force \
#		--no-interaction
#	composer --working-dir=build/set020-infection dump-autoload
#
#	# We generate the expected output file: we test that the scoping process
#	# does not alter it
#	cd fixtures/set020-infection && php vendor/infection/infection/bin/infection \
#		--coverage=../../dist/infection-coverage \
#		--skip-initial-tests \
#		--only-covered \
#		--no-progress
#		> build/set020-infection/expected-output
#	sed 's/Time.*//' build/set020-infection/expected-output > build/set020-infection/expected-output
#
#
#	cd build/set020-infection && php vendor/infection/infection/bin/infection \
#		--coverage=../../dist/infection-coverage \
#		--skip-initial-tests \
#		--only-covered \
#		--no-progress
#		> build/set020-infection/output
#	sed 's/Time.*//' build/set020-infection/output > build/set020-infection/output
#
#	diff build/set020-infection/expected-output build/set020-infection/output

.PHONY: e2e_024
e2e_024: ## Runs end-to-end tests for the fixture set 024 — Scoping of a codebase with global functions exposed
e2e_024: $(PHP_SCOPER_PHAR_BIN) fixtures/set024/vendor
	$(PHP_SCOPER_PHAR) add-prefix \
		--working-dir=fixtures/set024 \
		--output-dir=../../build/set024 \
		--force \
		--no-interaction \
		--stop-on-failure
	composer --working-dir=build/set024 dump-autoload

	php build/set024/main.php > build/set024/output
	diff fixtures/set024/expected-output build/set024/output

.PHONY: e2e_025
e2e_025: ## Runs end-to-end tests for the fixture set 025 — Scoping of a codebase using third-party exposed functions
e2e_025: $(PHP_SCOPER_PHAR_BIN) fixtures/set025/vendor
	$(PHP_SCOPER_PHAR) add-prefix \
		--working-dir=fixtures/set025 \
		--output-dir=../../build/set025 \
		--force \
		--no-interaction \
		--stop-on-failure
	composer --working-dir=build/set025 dump-autoload

	php build/set025/main.php > build/set025/output
	diff fixtures/set025/expected-output build/set025/output

.PHONY: e2e_027
e2e_027: ## Runs end-to-end tests for the fixture set 027 — Scoping of a Laravel
ifeq ("$(IS_PHP8)", "true")
e2e_027: $(PHP_SCOPER_PHAR_BIN) fixtures/set027-laravel/vendor
	$(PHP_SCOPER_PHAR) add-prefix \
		--working-dir=fixtures/set027-laravel \
		--output-dir=../../build/set027-laravel \
		--force \
		--no-interaction \
		--stop-on-failure
	composer --working-dir=build/set027-laravel dump-autoload --no-dev

	php build/set027-laravel/artisan -V > build/set027-laravel/output
	diff fixtures/set027-laravel/expected-output build/set027-laravel/output
else
e2e_027:
	echo "SKIP e2e_027: PHP version not supported"
endif

.PHONY: e2e_028
e2e_028: ## Runs end-to-end tests for the fixture set 028 — Scoping of a Symfony project
e2e_028: $(PHP_SCOPER_PHAR_BIN) fixtures/set028-symfony/vendor
	$(PHP_SCOPER_PHAR) add-prefix \
		--working-dir=fixtures/set028-symfony \
		--output-dir=../../build/set028-symfony \
		--force \
		--no-interaction \
		--stop-on-failure

	APP_ENV=dev composer --working-dir=fixtures/set028-symfony dump-autoload --no-dev
	APP_ENV=dev php fixtures/set028-symfony/bin/console -V > fixtures/set028-symfony/expected-output

	APP_ENV=dev composer --working-dir=build/set028-symfony dump-autoload --no-dev
	APP_ENV=dev php build/set028-symfony/bin/console -V > build/set028-symfony/output

	diff fixtures/set028-symfony/expected-output build/set028-symfony/output

.PHONY: e2e_029
e2e_029: ## Runs end-to-end tests for the fixture set 029 — Scoping of the EasyRdf project
e2e_029: $(PHP_SCOPER_PHAR_BIN) fixtures/set029-easy-rdf/vendor
	$(PHP_SCOPER_PHAR) add-prefix \
		--working-dir=fixtures/set029-easy-rdf \
		--output-dir=../../build/set029-easy-rdf \
		--no-config \
		--force \
		--no-interaction \
		--stop-on-failure

	php fixtures/set029-easy-rdf/main.php > fixtures/set029-easy-rdf/expected-output

	composer --working-dir=build/set029-easy-rdf dump-autoload --no-dev
	php build/set029-easy-rdf/main.php > build/set029-easy-rdf/output

	diff fixtures/set029-easy-rdf/expected-output build/set029-easy-rdf/output

.PHONY: e2e_030
e2e_030: ## Runs end-to-end tests for the fixture set 030 — Scoping of a codebase with globally registered functions
e2e_030: $(PHP_SCOPER_PHAR_BIN) fixtures/set030/vendor
	$(PHP_SCOPER_PHAR) add-prefix \
		--working-dir=fixtures/set030 \
		--output-dir=../../build/set030 \
		--no-config \
		--force \
		--no-interaction \
		--stop-on-failure

	php fixtures/set030/main.php > fixtures/set030/expected-output

	composer --working-dir=build/set030 dump-autoload --no-dev
	php build/set030/main.php > build/set030/output

	diff fixtures/set030/expected-output build/set030/output

.PHONY: e2e_031
e2e_031: ## Runs end-to-end tests for the fixture set 031 — Scoping of a codebase using symbols of a non-loaded PHP extension
e2e_031: $(PHP_SCOPER_PHAR_BIN)
	$(PHP_SCOPER_PHAR) add-prefix \
		--working-dir=fixtures/set031-extension-symbol \
		--output-dir=../../build/set031-extension-symbol \
		--force \
		--no-interaction \
		--stop-on-failure

	diff fixtures/set031-extension-symbol/expected-main.php build/set031-extension-symbol/main.php

.PHONY: e2e_032
e2e_032: ## Runs end-to-end tests for the fixture set 032 — Scoping of a codebase using the isolated finder in the configuration
e2e_032: $(PHP_SCOPER_PHAR_BIN)
	$(PHP_SCOPER_PHAR) add-prefix \
		--working-dir=fixtures/set032-isolated-finder \
		--output-dir=../../build/set032-isolated-finder \
		--force \
		--no-interaction \
		--stop-on-failure

	tree build/set032-isolated-finder > build/set032-isolated-finder/actual-tree

	diff fixtures/set032-isolated-finder/expected-tree build/set032-isolated-finder/actual-tree

.PHONY: e2e_033
e2e_033: ## Runs end-to-end tests for the fixture set 033 — Scoping of a codebase a function registered in the global namespace
e2e_033: $(PHP_SCOPER_PHAR_BIN) fixtures/set033-user-global-function/vendor
	$(PHP_SCOPER_PHAR) add-prefix \
		--working-dir=fixtures/set033-user-global-function \
		--output-dir=../../build/set033-user-global-function \
		--force \
		--no-interaction \
		--stop-on-failure

	php fixtures/set033-user-global-function/index.php > fixtures/set033-user-global-function/expected-output

	composer --working-dir=build/set033-user-global-function dump-autoload --no-dev
	php build/set033-user-global-function/index.php > build/set033-user-global-function/output

	diff fixtures/set033-user-global-function/expected-output build/set033-user-global-function/output

.PHONY: e2e_034
e2e_034: ## Runs end-to-end tests for the fixture set 034 — Leverage Composer InstalledVersions
e2e_034: $(PHP_SCOPER_PHAR_BIN) fixtures/set034-installed-versions/vendor
	$(PHP_SCOPER_PHAR) add-prefix \
		--working-dir=fixtures/set034-installed-versions \
		--output-dir=../../build/set034-installed-versions \
		--force \
		--no-interaction \
		--stop-on-failure

	php fixtures/set034-installed-versions/index.php > fixtures/set034-installed-versions/expected-output

	composer --working-dir=build/set034-installed-versions dump-autoload --no-dev
	php build/set034-installed-versions/index.php > build/set034-installed-versions/output

	diff fixtures/set034-installed-versions/expected-output build/set034-installed-versions/output

.PHONY: e2e_035
e2e_035: ## Runs end-to-end tests for the fixture set 035 — Tests tha composer autoloaded files are working fine
e2e_035: $(PHP_SCOPER_PHAR_BIN) fixtures/set035-composer-files-autoload/vendor fixtures/set035-composer-files-autoload/guzzle5-include/vendor
	rm -rf build/set035-composer-files-autoload || true
	cp -R fixtures/set035-composer-files-autoload build/set035-composer-files-autoload

	$(PHP_SCOPER_PHAR) add-prefix \
		--working-dir=fixtures/set035-composer-files-autoload/guzzle5-include \
		--output-dir=../../../build/set035-composer-files-autoload/scoped-guzzle5-include \
		--force \
		--no-config \
		--no-interaction \
		--stop-on-failure
	composer --working-dir=build/set035-composer-files-autoload/scoped-guzzle5-include dump-autoload
	rm -rf build/set035-composer-files-autoload/guzzle5-include || true

	php build/set035-composer-files-autoload/index.php &> build/set035-composer-files-autoload/output || true
	php build/set035-composer-files-autoload/test.php


#
# Rules from files
#---------------------------------------------------------------------------

.composer-root-version:
	cd composer-root-version-checker; $(MAKE) --makefile Makefile dump_root_version
	touch -c $@

vendor: composer.lock .composer-root-version
	$(MAKE) vendor_install

# Sometimes we need to re-install the vendor. Since it has a few dependencies
# we do not want to check over and over, as unlike re-installing dependencies
# which is fast, those might have a significant overhead (e.g. checking the
# composer root version), we do not want to repeat the step of checking the
# vendor dependencies.
.PHONY: vendor_install
vendor_install:
	/bin/bash -c 'source .composer-root-version && composer install'
	touch -c vendor
	touch -c $(COMPOSER_BIN_PLUGIN_VENDOR)
	touch -c $(PHPUNIT_BIN)
	touch -c $(BOX_BIN)

composer.lock: composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer update --lock && touch -c $(@)"

vendor-hotfix: vendor
	composer dump-autoload
	touch -c $@

$(COMPOSER_BIN_PLUGIN_VENDOR): composer.lock .composer-root-version
	$(MAKE) --always-make vendor_install
	touch -c $@

$(PHPUNIT_BIN): composer.lock .composer-root-version
	$(MAKE) --always-make vendor_install
	touch -c $@

$(BOX_BIN): composer.lock .composer-root-version
	$(MAKE) --always-make vendor_install
	touch -c $@

$(COVERS_VALIDATOR_BIN): vendor-bin/covers-validator/vendor
vendor-bin/covers-validator/vendor: vendor-bin/covers-validator/composer.lock $(COMPOSER_BIN_PLUGIN_VENDOR)
	composer bin covers-validator install
	touch -c $@
vendor-bin/covers-validator/composer.lock: vendor-bin/covers-validator/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer bin covers-validator update --lock && touch -c $(@)"

.PHONY: install_php_cs_fixer
install_php_cs_fixer: $(PHP_CS_FIXER_BIN)

$(PHP_CS_FIXER_BIN): vendor-bin/php-cs-fixer/vendor
	touch -c $@
vendor-bin/php-cs-fixer/vendor: vendor-bin/php-cs-fixer/composer.lock $(COMPOSER_BIN_PLUGIN_VENDOR)
	composer bin php-cs-fixer install
	touch -c $@
vendor-bin/php-cs-fixer/composer.lock: vendor-bin/php-cs-fixer/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer bin php-cs-fixer update --lock && touch -c $(@)"

$(PHPSTAN_BIN): vendor-bin/phpstan/vendor
	touch -c $@
vendor-bin/phpstan/vendor: vendor-bin/phpstan/composer.lock $(COMPOSER_BIN_PLUGIN_VENDOR)
	composer bin phpstan install
	touch -c $@
vendor-bin/phpstan/composer.lock: vendor-bin/phpstan/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer bin phpstan update --lock && touch -c $(@)"

$(PHP_SCOPER_PHAR_BIN): $(BOX) bin/php-scoper $(SRC_FILES) vendor-hotfix vendor scoper.inc.php box.json.dist
	$(BOX) compile --no-parallel
	touch -c $@

$(COVERAGE_XML): $(PHPUNIT_BIN) $(SRC_FILES)
	$(MAKE) phpunit_coverage_infection
	touch -c "$@"

fixtures/set005/vendor: fixtures/set005/composer.lock
	composer --working-dir=fixtures/set005 install
	touch -c $@
fixtures/set005/composer.lock: fixtures/set005/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set005 update --lock && touch -c $(@)"

fixtures/set011/vendor:
	composer --working-dir=fixtures/set011 install
	# Dumping the autoload is not enough when there is a composer.lock. Indeed
	# without composer.lock, no vendor/composer/installed.json is installed
	# so Box sees composer.json without composer.lock & installed.json and can
	# dump the autoload just fine.
	# However, if there is only composer.lock, the dump-autoload will NOT add
	# the installed.json file and Box only sees that there is a composer.lock
	# without installed.json which prevents it to dump the autoload.
	# TL:DR; If you don't have composer.lock, dump-autoload is enough, otherwise you
	# need install.
	composer --working-dir=fixtures/set011 dump-autoload
	touch -c $@

fixtures/set015/vendor: fixtures/set015/composer.lock
	composer --working-dir=fixtures/set015 install
	touch -c $@
fixtures/set015/composer.lock: fixtures/set015/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set015 update --lock && touch -c $(@)"

fixtures/set016-symfony-finder/vendor: fixtures/set016-symfony-finder/composer.lock
	composer --working-dir=fixtures/set016-symfony-finder install
	touch -c $@
fixtures/set016-symfony-finder/composer.lock: fixtures/set016-symfony-finder/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set016-symfony-finder update --lock && touch -c $(@)"

fixtures/set017-symfony-di/vendor: fixtures/set017-symfony-di/composer.lock
	composer --working-dir=fixtures/set017-symfony-di install
	touch -c $@
fixtures/set017-symfony-di/composer.lock: fixtures/set017-symfony-di/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set017-symfony-di update --lock && touch -c $(@)"

fixtures/set018-nikic-parser/vendor: fixtures/set018-nikic-parser/composer.lock
	composer --working-dir=fixtures/set018-nikic-parser install
	touch -c $@
fixtures/set018-nikic-parser/composer.lock: fixtures/set018-nikic-parser/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set018-nikic-parser update --lock && touch -c $(@)"

fixtures/set019-symfony-console/vendor: fixtures/set019-symfony-console/composer.lock
	composer --working-dir=fixtures/set019-symfony-console install
	touch -c $@
fixtures/set019-symfony-console/composer.lock: fixtures/set019-symfony-console/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set019-symfony-console update --lock && touch -c $(@)"

fixtures/set020-infection/vendor: fixtures/set020-infection/composer.lock
	composer --working-dir=fixtures/set020-infection install
	touch -c $@
fixtures/set020-infection/composer.lock: fixtures/set020-infection/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set020-infection update --lock && touch -c $(@)"

fixtures/set024/vendor: fixtures/set024/composer.lock
	composer --working-dir=fixtures/set024 install
	touch -c $@
fixtures/set024/composer.lock: fixtures/set024/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set024 update --lock && touch -c $(@)"

fixtures/set025/vendor: fixtures/set025/composer.lock
	composer --working-dir=fixtures/set025 install
	touch -c $@
fixtures/set025/composer.lock: fixtures/set025/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set025 update --lock && touch -c $(@)"

fixtures/set027-laravel/vendor: fixtures/set027-laravel/composer.lock
	composer --working-dir=fixtures/set027-laravel install --no-dev
	touch -c $@
fixtures/set027-laravel/composer.lock: fixtures/set027-laravel/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set027-laravel update --lock && touch -c $(@)"

fixtures/set028-symfony/vendor: fixtures/set028-symfony/composer.lock
	composer --working-dir=fixtures/set028-symfony install --no-dev --no-scripts
	touch -c $@
fixtures/set028-symfony/composer.lock: fixtures/set028-symfony/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set028-symfony update --lock && touch -c $(@)"

fixtures/set029-easy-rdf/vendor: fixtures/set029-easy-rdf/composer.lock
	composer --working-dir=fixtures/set029-easy-rdf install --no-dev
	touch -c $@
fixtures/set029-easy-rdf/composer.lock: fixtures/set029-easy-rdf/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set029-easy-rdf update --lock && touch -c $(@)"

fixtures/set030/vendor: fixtures/set030/composer.json
	composer --working-dir=fixtures/set030 install --no-dev
	touch -c $@

fixtures/set033-user-global-function/vendor: fixtures/set033-user-global-function/composer.lock
	composer --working-dir=fixtures/set033-user-global-function install --no-dev --no-scripts
	touch -c $@
fixtures/set033-user-global-function/composer.lock: fixtures/set033-user-global-function/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set033-user-global-function update --lock && touch -c $(@)"

fixtures/set034-installed-versions/vendor: fixtures/set034-installed-versions/composer.lock
	composer --working-dir=fixtures/set034-installed-versions install --no-dev --no-scripts
	touch -c $@
fixtures/set034-installed-versions/composer.lock: fixtures/set034-installed-versions/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set034-installed-versions update --lock && touch -c $(@)"

fixtures/set035-composer-files-autoload/vendor: fixtures/set035-composer-files-autoload/composer.lock
	composer --working-dir=fixtures/set035-composer-files-autoload install --no-dev --no-scripts
	touch -c $@
fixtures/set035-composer-files-autoload/composer.lock: fixtures/set035-composer-files-autoload/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set035-composer-files-autoload update --lock && touch -c $(@)"

fixtures/set035-composer-files-autoload/guzzle5-include/vendor: fixtures/set035-composer-files-autoload/guzzle5-include/composer.lock
	composer --working-dir=fixtures/set035-composer-files-autoload/guzzle5-include install --no-dev --no-scripts
	touch -c $@
fixtures/set035-composer-files-autoload/guzzle5-include/composer.lock: fixtures/set035-composer-files-autoload/guzzle5-include/composer.json
	@echo "$(@) is not up to date. You may want to run the following command:"
	@echo "$$ composer --working-dir=fixtures/set035-composer-files-autoload/guzzle5-include update --lock && touch -c $(@)"
