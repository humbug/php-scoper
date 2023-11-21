FROM php:8.2-cli-alpine

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions zlib phar sodium tokenizer filter

COPY bin/php-scoper.phar /php-scoper.phar

ENTRYPOINT ["/php-scoper.phar"]
