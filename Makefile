# See https://tech.davis-hansson.com/p/make/
MAKEFLAGS += --warn-undefined-variables
MAKEFLAGS += --no-builtin-rules

IS_PHP8=$(shell php -r "echo version_compare(PHP_VERSION, '8.0.0', '>=') ? 'true' : 'false';")
SRC_FILES=$(shell find bin/ src/ vendor-hotfix/ -type f)

PHP_SCOPER_BIN=bin/php-scoper.phar
PHP_SCOPER=$(PHP_SCOPER_BIN)

COMPOSER_BIN_PLUGIN_VENDOR=vendor/bamarni/composer-bin-plugin

CODE_SNIFFER=vendor-bin/code-sniffer/vendor/bin/phpcs
CODE_SNIFFER_FIX=vendor-bin/code-sniffer/vendor/bin/phpcbf

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

BLACKFIRE=blackfire


.DEFAULT_GOAL := help


.PHONY: help
help:
	@echo "\033[33mUsage:\033[0m\n  make TARGET\n\n\033[32m#\n# Commands\n#---------------------------------------------------------------------------\033[0m\n"
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//' | awk 'BEGIN {FS = ":"}; {printf "\033[33m%s:\033[0m%s\n", $$1, $$2}'


#
# Commands
#---------------------------------------------------------------------------

.PHONY: clean
clean:	 ## Clean all created artifacts
clean:
	git clean --exclude=.idea/ -ffdx

update_root_version: ## Check the latest GitHub release and update COMPOSER_ROOT_VERSION accordingly
update_root_version:
	rm .composer-root-version || true
	$(MAKE) .composer-root-version

.PHONY: cs
cs:	 ## Fixes CS
cs: $(CODE_SNIFFER) $(CODE_SNIFFER_FIX)
	php $(CODE_SNIFFER_FIX) || true
	php $(CODE_SNIFFER)

.PHONY: cs-check
cs-check:	## Checks CS
cs-check: $(CODE_SNIFFER)
	php $(CODE_SNIFFER)

.PHONY: phpstan
phpstan: ## Runs PHPStan
phpstan: $(PHPSTAN_BIN)
	$(PHPSTAN)

.PHONY: build
build:	## Builds the PHAR
build:
	rm $(PHP_SCOPER_BIN) || true
	$(MAKE) $(PHP_SCOPER_BIN)

.PHONY: outdated_fixtures
outdated_fixtures:	## Reports outdated dependencies
outdated_fixtures:
	find fixtures -name 'composer.json' -type f -depth 2 -exec dirname '{}' \; | xargs -I % sh -c 'echo "Checking %;" $$(composer install --working-dir=% --ansi && composer outdated --direct --working-dir=% --ansi)'


#
# Tests
#---------------------------------------------------------------------------

.PHONY: test
test:	 ## Runs all the tests
test: check_composer_root_version validate_package covers_validator phpunit e2e

.PHONY: validate_package
validate_package:	## Validates the composer.json
validate_package:
	composer validate --strict

.PHONY: check_composer_root_version
check_composer_root_version:	## Checks that the COMPOSER_ROOT_VERSION is up to date
check_composer_root_version: .composer-root-version
	php bin/check-composer-root-version.php

.PHONY: covers_validator
covers_validator:	 ## Checks PHPUnit @coves tag
covers_validator: $(COVERS_VALIDATOR_BIN)
	$(COVERS_VALIDATOR)

.PHONY: phpunit
phpunit:	## Runs PHPUnit tests
phpunit: $(PHPUNIT_BIN) vendor
	$(PHPUNIT)

.PHONY: phpunit_coverage_infection
phpunit_coverage_infection:	## Runs PHPUnit tests with test coverage
phpunit_coverage_infection: $(PHPUNIT_BIN) vendor
	$(PHPUNIT_COVERAGE_INFECTION)

.PHONY: phpunit_coverage_html
phpunit_coverage_html:		## Runs PHPUnit with code coverage with HTML report
phpunit_coverage_html: $(PHPUNIT_BIN) vendor
	$(PHPUNIT_COVERAGE_HTML)

.PHONY: infection
infection:	## Runs Infection
infection: $(COVERAGE_XML) vendor
#infection: $(INFECTION_BIN) $(COVERAGE_XML) vendor
	if [ -d $(COVERAGE_XML) ]; then $(INFECTION); fi

.PHONY: blackfire
blackfire:	## Runs Blackfire profiling
blackfire: $(PHP_SCOPER_BIN) vendor
	$(BLACKFIRE) --new-reference run php $(PHP_SCOPER_BIN) add-prefix --output-dir=build/php-scoper --force --quiet

.PHONY: e2e
e2e:	 ## Runs end-to-end tests
e2e: e2e_004 e2e_005 e2e_011 e2e_013 e2e_014 e2e_015 e2e_016 e2e_017 e2e_018 e2e_019 e2e_020 e2e_022 e2e_023 e2e_024 e2e_025 e2e_026 e2e_027 e2e_028 e2e_029 e2e_030 e2e_031 e2e_032

.PHONY: e2e_004
e2e_004: ## Runs end-to-end tests for the fixture set 004 — Minimalistic codebase
e2e_004: $(PHP_SCOPER_BIN)
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
e2e_005: $(PHP_SCOPER_BIN) fixtures/set005/vendor
	$(BOX) compile --no-parallel --working-dir fixtures/set005

	php build/set005/bin/greet.phar > build/set005/output
	diff fixtures/set005/expected-output build/set005/output

.PHONY: e2e_011
e2e_011: ## Runs end-to-end tests for the fixture set 011 — Codebase with exposed symbols
e2e_011: $(PHP_SCOPER_BIN) fixtures/set011/vendor
	$(BOX) compile --no-parallel --working-dir fixtures/set011
	cp -R fixtures/set011/tests/ build/set011/tests/

	php build/set011/bin/greet.phar > build/set011/output
	diff fixtures/set011/expected-output build/set011/output

.PHONY: e2e_013
e2e_013: ## Runs end-to-end tests for the fixture set 013 — Test the init command
e2e_013: $(PHP_SCOPER_BIN)
	rm -rf build/set013 || true
	mkdir -p build
	cp -R fixtures/set013 build/set013

	$(PHP_SCOPER_BIN) init --working-dir=build/set013 --no-interaction

	diff src/scoper.inc.php.tpl build/set013/scoper.inc.php

.PHONY: e2e_014
e2e_014: ## Runs end-to-end tests for the fixture set 014 — Codebase with PSR-0 autoloading
e2e_014: $(PHP_SCOPER_BIN)
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
e2e_015: $(PHP_SCOPER_BIN) fixtures/set015/vendor
	$(BOX) compile --no-parallel --working-dir fixtures/set015

	php build/set015/bin/greet.phar > build/set015/output
	diff fixtures/set015/expected-output build/set015/output

.PHONY: e2e_016
e2e_016: ## Runs end-to-end tests for the fixture set 016 — Scoping of the Symfony Finder component
e2e_016: $(PHP_SCOPER_BIN) fixtures/set016-symfony-finder/vendor
	$(PHP_SCOPER) add-prefix \
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
e2e_017: $(PHP_SCOPER_BIN) fixtures/set017-symfony-di/vendor
	$(PHP_SCOPER) add-prefix \
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
e2e_018: $(PHP_SCOPER_BIN) fixtures/set018-nikic-parser/vendor
	$(PHP_SCOPER) add-prefix \
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
e2e_019: $(PHP_SCOPER_BIN) fixtures/set019-symfony-console/vendor
	$(PHP_SCOPER) add-prefix --working-dir=fixtures/set019-symfony-console \
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
e2e_020: $(PHP_SCOPER_BIN) fixtures/set020-infection/vendor
# Skip it for now: there is autoloading issues with the Safe functions
#	$(PHP_SCOPER) add-prefix --working-dir=fixtures/set020-infection \
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

.PHONY: e2e_022
e2e_022: ## Runs end-to-end tests for the fixture set 022 — Codebase with excluded symbols via the legacy namespace whitelisting setting
e2e_022: $(PHP_SCOPER_BIN) fixtures/set022/vendor
	$(BOX) compile --no-parallel --working-dir fixtures/set022
	cp -R fixtures/set022/tests/ build/set022/tests/

	php build/set022/bin/greet.phar > build/set022/output

	diff fixtures/set022/expected-output build/set022/output

.PHONY: e2e_023
e2e_023: ## Runs end-to-end tests for the fixture set 023 — Codebase with excluded symbols via the legacy component whitelisting setting
e2e_023: $(PHP_SCOPER_BIN) fixtures/set023/vendor
	$(PHP_SCOPER) add-prefix --working-dir=fixtures/set023 \
		--output-dir=../../build/set023 \
		--force \
		--no-interaction \
		--stop-on-failure
	composer --working-dir=build/set023 dump-autoload

	php build/set023/main.php > build/set023/output
	diff fixtures/set023/expected-output build/set023/output

.PHONY: e2e_024
e2e_024: ## Runs end-to-end tests for the fixture set 024 — Scoping of a codebase with global functions exposed
e2e_024: $(PHP_SCOPER_BIN) fixtures/set024/vendor
	$(PHP_SCOPER) add-prefix \
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
e2e_025: $(PHP_SCOPER_BIN) fixtures/set025/vendor
	$(PHP_SCOPER) add-prefix \
		--working-dir=fixtures/set025 \
		--output-dir=../../build/set025 \
		--force \
		--no-interaction \
		--stop-on-failure
	composer --working-dir=build/set025 dump-autoload

	php build/set025/main.php > build/set025/output
	diff fixtures/set025/expected-output build/set025/output

.PHONY: e2e_026
e2e_026: ## Runs end-to-end tests for the fixture set 026 — Scoping of a codebase exposing symbols via the legacy whitelist setting
e2e_026: $(PHP_SCOPER_BIN) fixtures/set026/vendor
	$(PHP_SCOPER) add-prefix \
		--working-dir=fixtures/set026 \
		--output-dir=../../build/set026 \
		--force \
		--no-interaction \
		--stop-on-failure
	composer --working-dir=build/set026 dump-autoload

	php build/set026/main.php > build/set026/output
	diff fixtures/set026/expected-output build/set026/output

.PHONY: e2e_027
e2e_027: ## Runs end-to-end tests for the fixture set 027 — Scoping of a Laravel
ifeq ("$(IS_PHP8)", "true")
e2e_027: $(PHP_SCOPER_BIN) fixtures/set027-laravel/vendor
	$(PHP_SCOPER) add-prefix \
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
e2e_028: $(PHP_SCOPER_BIN) fixtures/set028-symfony/vendor
	php $(PHP_SCOPER_BIN) add-prefix \
		--working-dir=fixtures/set028-symfony \
		--output-dir=../../build/set028-symfony \
		--no-config \
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
e2e_029: $(PHP_SCOPER_BIN) fixtures/set029-easy-rdf/vendor
	php $(PHP_SCOPER_BIN) add-prefix \
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
e2e_030: $(PHP_SCOPER_BIN) fixtures/set030/vendor
	php $(PHP_SCOPER_BIN) add-prefix \
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
e2e_031: $(PHP_SCOPER_BIN)
	php $(PHP_SCOPER_BIN) add-prefix \
		--working-dir=fixtures/set031-extension-symbol \
		--output-dir=../../build/set031-extension-symbol \
		--force \
		--no-interaction \
		--stop-on-failure

	diff fixtures/set031-extension-symbol/expected-main.php build/set031-extension-symbol/main.php

.PHONY: e2e_032
e2e_032: ## Runs end-to-end tests for the fixture set 032 — Scoping of a codebase using the isolated finder in the configuration
e2e_032: $(PHP_SCOPER_BIN)
	php $(PHP_SCOPER_BIN) add-prefix \
		--working-dir=fixtures/set032-isolated-finder \
		--output-dir=../../build/set032-isolated-finder \
		--force \
		--no-interaction \
		--stop-on-failure

	tree build/set032-isolated-finder > build/set032-isolated-finder/actual-tree

	diff fixtures/set032-isolated-finder/expected-tree build/set032-isolated-finder/actual-tree


#
# Rules from files
#---------------------------------------------------------------------------

.composer-root-version:
	php bin/dump-composer-root-version.php
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
	touch -c $@

composer.lock: composer.json
	@echo composer.lock is not up to date.
	touch -c $@	# We likely do not want that message repeated over and over

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
	@echo covers-validator composer.lock is not up to date

$(PHPSTAN_BIN): vendor-bin/phpstan/vendor
vendor-bin/phpstan/vendor: vendor-bin/phpstan/composer.lock $(COMPOSER_BIN_PLUGIN_VENDOR)
	composer bin phpstan install
	touch -c $@
vendor-bin/phpstan/composer.lock: vendor-bin/phpstan/composer.json
	@echo phpstan composer.lock is not up to date

vendor-bin/code-sniffer/vendor: vendor-bin/code-sniffer/composer.lock $(COMPOSER_BIN_PLUGIN_VENDOR)
	composer bin code-sniffer install
	touch -c $@

vendor-bin/code-sniffer/composer.lock: vendor-bin/code-sniffer/composer.json
	@echo code-sniffer composer.lock is not up to date

$(PHP_SCOPER_BIN): $(BOX) bin/php-scoper $(SRC_FILES) vendor-hotfix vendor scoper.inc.php box.json.dist
	$(BOX) compile
	touch -c $@

$(COVERAGE_XML): $(PHPUNIT_BIN) $(SRC_FILES)
	$(MAKE) phpunit_coverage_infection
	touch -c "$@"

$(CODE_SNIFFER): vendor-bin/code-sniffer/vendor
	composer bin code-sniffer install
	touch -c $@

$(CODE_SNIFFER_FIX): vendor-bin/code-sniffer/vendor
	composer bin code-sniffer install
	touch -c $@

fixtures/set005/vendor: fixtures/set005/composer.lock
	composer --working-dir=fixtures/set005 install
	touch -c $@
fixtures/set005/composer.lock: fixtures/set005/composer.json
	@echo fixtures/set005/composer.lock is not up to date.

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
	@echo fixtures/set015/composer.lock is not up to date.

fixtures/set016-symfony-finder/vendor: fixtures/set016-symfony-finder/composer.lock
	composer --working-dir=fixtures/set016-symfony-finder install
	touch -c $@
fixtures/set016-symfony-finder/composer.lock: fixtures/set016-symfony-finder/composer.json
	@echo fixtures/set016-symfony-finder/composer.lock is not up to date.

fixtures/set017-symfony-di/vendor: fixtures/set017-symfony-di/composer.lock
	composer --working-dir=fixtures/set017-symfony-di install
	touch -c $@
fixtures/set017-symfony-di/composer.lock: fixtures/set017-symfony-di/composer.json
	@echo fixtures/set017-symfony-di/composer.lock is not up to date.

fixtures/set018-nikic-parser/vendor: fixtures/set018-nikic-parser/composer.lock
	composer --working-dir=fixtures/set018-nikic-parser install
	touch -c $@
fixtures/set018-nikic-parser/composer.lock: fixtures/set018-nikic-parser/composer.json
	@echo fixtures/set018-nikic-parser/composer.lock is not up to date.

fixtures/set019-symfony-console/vendor: fixtures/set019-symfony-console/composer.lock
	composer --working-dir=fixtures/set019-symfony-console install
	touch -c $@
fixtures/set019-symfony-console/composer.lock: fixtures/set019-symfony-console/composer.json
	@echo fixtures/set019-symfony-console/composer.lock is not up to date.

fixtures/set020-infection/vendor: fixtures/set020-infection/composer.lock
	composer --working-dir=fixtures/set020-infection install
	touch -c $@
fixtures/set020-infection/composer.lock: fixtures/set020-infection/composer.json
	@echo fixtures/set020-infection/composer.lock is not up to date.

fixtures/set022/vendor: fixtures/set022/composer.json
	composer --working-dir=fixtures/set022 update
	touch -c $@

fixtures/set023/vendor: fixtures/set023/composer.lock
	composer --working-dir=fixtures/set023 install
	touch -c $@
fixtures/set023/composer.lock: fixtures/set023/composer.json
	@echo fixtures/set023/composer.lock is not up to date.

fixtures/set024/vendor: fixtures/set024/composer.lock
	composer --working-dir=fixtures/set024 install
	touch -c $@
fixtures/set024/composer.lock: fixtures/set024/composer.json
	@echo fixtures/set024/composer.lock is not up to date.

fixtures/set025/vendor: fixtures/set025/composer.lock
	composer --working-dir=fixtures/set025 install
	touch -c $@
fixtures/set025/composer.lock: fixtures/set025/composer.json
	@echo fixtures/set025/composer.lock is not up to date.

fixtures/set026/vendor:
	composer --working-dir=fixtures/set026 update
	touch -c $@

fixtures/set027-laravel/vendor: fixtures/set027-laravel/composer.lock
	composer --working-dir=fixtures/set027-laravel install --no-dev
	touch -c $@
fixtures/set027-laravel/composer.lock: fixtures/set027-laravel/composer.json
	@echo fixtures/set027-laravel/composer.lock is not up to date.

fixtures/set028-symfony/vendor: fixtures/set028-symfony/composer.lock
	composer --working-dir=fixtures/set028-symfony install --no-dev --no-scripts
	touch -c $@
fixtures/set028-symfony/composer.lock: fixtures/set028-symfony/composer.json
	@echo fixtures/set028-symfony/composer.lock is not up to date.

fixtures/set029-easy-rdf/vendor: fixtures/set029-easy-rdf/composer.lock
	composer --working-dir=fixtures/set029-easy-rdf install --no-dev
	touch -c $@
fixtures/set029-easy-rdf/composer.lock: fixtures/set029-easy-rdf/composer.json
	@echo fixtures/set029-easy-rdf/composer.lock is not up to date.

fixtures/set030/vendor: fixtures/set030/composer.json
	composer --working-dir=fixtures/set030 install --no-dev
	touch -c $@
















