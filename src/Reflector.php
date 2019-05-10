<?php

declare(strict_types=1);

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Humbug\PhpScoper;

use Roave\BetterReflection\Identifier\Exception\InvalidIdentifierName;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflector\Exception\IdentifierNotFound;
use function array_key_exists;
use function array_values;
use function get_defined_constants;
use Roave\BetterReflection\Reflector\Reflector as BetterReflectionReflector;
use function strtoupper;

/**
 * Main class used to determine if a given symbol is internal or not. As of the time of writing, it leverages
 * Roave\BetterReflection to determine if given class is internal or not which allows to do reflection:.
 *
 * - Without loading the code scanned code at all
 * - Do reliable reflection against non-loaded APIs, i.e. a class from a non-loaded extension will properly appear
 *   as internal whereas the regular reflection would not.
 *
 * However Roave\BetterReflection is still not supporting constants and functions hence requires some hacks here
 * meanwhile.
 *
 * @private
 */
final class Reflector
{
    /**
     * TODO: Migrate to Roave/BetterReflection once it supports constants
     */
    private const KNOWN_INTERNAL_CONSTANTS = [
        // PHP 7.0
        // Source: https://www.php.net/manual/en/migration70.constants.php
        'IMG_WEBP' => true,
        'JSON_ERROR_INVALID_PROPERTY_NAME' => true,
        'JSON_ERROR_UTF16' => true,
        'LIBXML_BIGLINES' => true,
        'PREG_JIT_STACKLIMIT_ERROR' => true,
        'POSIX_RLIMIT_AS' => true,
        'POSIX_RLIMIT_CORE' => true,
        'POSIX_RLIMIT_CPU' => true,
        'POSIX_RLIMIT_DATA' => true,
        'POSIX_RLIMIT_FSIZE' => true,
        'POSIX_RLIMIT_LOCKS' => true,
        'POSIX_RLIMIT_MEMLOCK' => true,
        'POSIX_RLIMIT_MSGQUEUE' => true,
        'POSIX_RLIMIT_NICE' => true,
        'POSIX_RLIMIT_NOFILE' => true,
        'POSIX_RLIMIT_NPROC' => true,
        'POSIX_RLIMIT_RSS' => true,
        'POSIX_RLIMIT_RTPRIO' => true,
        'POSIX_RLIMIT_RTTIME' => true,
        'POSIX_RLIMIT_SIGPENDING' => true,
        'POSIX_RLIMIT_STACK' => true,
        'POSIX_RLIMIT_INFINITY' => true,
        'ZLIB_ENCODING_RAW' => true,
        'ZLIB_ENCODING_DEFLATE' => true,
        'ZLIB_ENCODING_GZIP' => true,
        'ZLIB_FILTERED' => true,
        'ZLIB_HUFFMAN_ONLY' => true,
        'ZLIB_FIXED' => true,
        'ZLIB_RLE' => true,
        'ZLIB_DEFAULT_STRATEGY' => true,
        'ZLIB_BLOCK' => true,
        'ZLIB_FINISH' => true,
        'ZLIB_FULL_FLUSH' => true,
        'ZLIB_NO_FLUSH' => true,
        'ZLIB_PARTIAL_FLUSH' => true,
        'ZLIB_SYNC_FLUSH' => true,

        // PHP 7.1
        // Source: https://www.php.net/manual/en/migration71.constants.php
        'CURLMOPT_PUSHFUNCTION' => true,
        'CURL_PUSH_OK' => true,
        'CURL_PUSH_DENY' => true,
        'FILTER_FLAG_EMAIL_UNICODE' => true,
        'IMAGETYPE_WEBP' => true,
        'JSON_UNESCAPED_LINE_TERMINATORS' => true,
        'LDAP_OPT_X_SASL_NOCANON' => true,
        'LDAP_OPT_X_SASL_USERNAME' => true,
        'LDAP_OPT_X_TLS_CACERTDIR' => true,
        'LDAP_OPT_X_TLS_CACERTFILE' => true,
        'LDAP_OPT_X_TLS_CERTFILE' => true,
        'LDAP_OPT_X_TLS_CIPHER_SUITE' => true,
        'LDAP_OPT_X_TLS_KEYFILE' => true,
        'LDAP_OPT_X_TLS_RANDOM_FILE' => true,
        'LDAP_OPT_X_TLS_CRLCHECK' => true,
        'LDAP_OPT_X_TLS_CRL_NONE' => true,
        'LDAP_OPT_X_TLS_CRL_PEER' => true,
        'LDAP_OPT_X_TLS_CRL_ALL' => true,
        'LDAP_OPT_X_TLS_DHFILE' => true,
        'LDAP_OPT_X_TLS_CRLFILE' => true,
        'LDAP_OPT_X_TLS_PROTOCOL_MIN' => true,
        'LDAP_OPT_X_TLS_PROTOCOL_SSL2' => true,
        'LDAP_OPT_X_TLS_PROTOCOL_SSL3' => true,
        'LDAP_OPT_X_TLS_PROTOCOL_TLS1_0' => true,
        'LDAP_OPT_X_TLS_PROTOCOL_TLS1_1' => true,
        'LDAP_OPT_X_TLS_PROTOCOL_TLS1_2' => true,
        'LDAP_OPT_X_TLS_PACKAGE' => true,
        'LDAP_OPT_X_KEEPALIVE_IDLE' => true,
        'LDAP_OPT_X_KEEPALIVE_PROBES' => true,
        'LDAP_OPT_X_KEEPALIVE_INTERVAL' => true,
        'PGSQL_NOTICE_LAST' => true,
        'PGSQL_NOTICE_ALL' => true,
        'PGSQL_NOTICE_CLEAR' => true,
        'MT_RAND_PHP' => true,

        // PHP 7.2
        // Source: https://php.net/manual/en/migration72.constants.php
        'PHP_FLOAT_DIG' => true,
        'PHP_FLOAT_EPSILON' => true,
        'PHP_FLOAT_MIN' => true,
        'PHP_FLOAT_MAX' => true,
        'PHP_OS_FAMILY' => true,
        'FILEINFO_EXTENSION' => true,
        'JSON_INVALID_UTF8_IGNORE' => true,
        'JSON_INVALID_UTF8_SUBSTITUTE' => true,
        'IMG_EFFECT_MULTIPLY' => true,
        'IMG_BMP' => true,
        'LDAP_EXOP_START_TLS' => true,
        'LDAP_EXOP_MODIFY_PASSWD' => true,
        'LDAP_EXOP_REFRESH' => true,
        'LDAP_EXOP_WHO_AM_I' => true,
        'LDAP_EXOP_TURN' => true,
        'PASSWORD_ARGON2I' => true,
        'PASSWORD_ARGON2_DEFAULT_MEMORY_COST' => true,
        'PASSWORD_ARGON2_DEFAULT_TIME_COST' => true,
        'PASSWORD_ARGON2_DEFAULT_THREADS' => true,
        'PREG_UNMATCHED_AS_NULL' => true,
        'SODIUM_LIBRARY_VERSION' => true,
        'SODIUM_LIBRARY_MAJOR_VERSION' => true,
        'SODIUM_LIBRARY_MINOR_VERSION' => true,
        'SODIUM_CRYPTO_AEAD_AES256GCM_KEYBYTES' => true,
        'SODIUM_CRYPTO_AEAD_AES256GCM_NSECBYTES' => true,
        'SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES' => true,
        'SODIUM_CRYPTO_AEAD_AES256GCM_ABYTES' => true,
        'SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_KEYBYTES' => true,
        'SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_NSECBYTES' => true,
        'SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_NPUBBYTES' => true,
        'SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_ABYTES' => true,
        'SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_KEYBYTES' => true,
        'SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NSECBYTES' => true,
        'SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES' => true,
        'SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_ABYTES' => true,
        'SODIUM_CRYPTO_AUTH_BYTES' => true,
        'SODIUM_CRYPTO_AUTH_KEYBYTES' => true,
        'SODIUM_CRYPTO_BOX_SEALBYTES' => true,
        'SODIUM_CRYPTO_BOX_SECRETKEYBYTES' => true,
        'SODIUM_CRYPTO_BOX_PUBLICKEYBYTES' => true,
        'SODIUM_CRYPTO_BOX_KEYPAIRBYTES' => true,
        'SODIUM_CRYPTO_BOX_MACBYTES' => true,
        'SODIUM_CRYPTO_BOX_NONCEBYTES' => true,
        'SODIUM_CRYPTO_BOX_SEEDBYTES' => true,
        'SODIUM_CRYPTO_KDF_BYTES_MIN' => true,
        'SODIUM_CRYPTO_KDF_BYTES_MAX' => true,
        'SODIUM_CRYPTO_KDF_CONTEXTBYTES' => true,
        'SODIUM_CRYPTO_KDF_KEYBYTES' => true,
        'SODIUM_CRYPTO_KX_SEEDBYTES' => true,
        'SODIUM_CRYPTO_KX_SESSIONKEYBYTES' => true,
        'SODIUM_CRYPTO_KX_PUBLICKEYBYTES' => true,
        'SODIUM_CRYPTO_KX_SECRETKEYBYTES' => true,
        'SODIUM_CRYPTO_KX_KEYPAIRBYTES' => true,
        'SODIUM_CRYPTO_GENERICHASH_BYTES' => true,
        'SODIUM_CRYPTO_GENERICHASH_BYTES_MIN' => true,
        'SODIUM_CRYPTO_GENERICHASH_BYTES_MAX' => true,
        'SODIUM_CRYPTO_GENERICHASH_KEYBYTES' => true,
        'SODIUM_CRYPTO_GENERICHASH_KEYBYTES_MIN' => true,
        'SODIUM_CRYPTO_GENERICHASH_KEYBYTES_MAX' => true,
        'SODIUM_CRYPTO_PWHASH_ALG_ARGON2I13' => true,
        'SODIUM_CRYPTO_PWHASH_ALG_DEFAULT' => true,
        'SODIUM_CRYPTO_PWHASH_SALTBYTES' => true,
        'SODIUM_CRYPTO_PWHASH_STRPREFIX' => true,
        'SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE' => true,
        'SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE' => true,
        'SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE' => true,
        'SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE' => true,
        'SODIUM_CRYPTO_PWHASH_OPSLIMIT_SENSITIVE' => true,
        'SODIUM_CRYPTO_PWHASH_MEMLIMIT_SENSITIVE' => true,
        'SODIUM_CRYPTO_PWHASH_SCRYPTSALSA208SHA256_SALTBYTES' => true,
        'SODIUM_CRYPTO_PWHASH_SCRYPTSALSA208SHA256_STRPREFIX' => true,
        'SODIUM_CRYPTO_PWHASH_SCRYPTSALSA208SHA256_OPSLIMIT_INTERACTIVE' => true,
        'SODIUM_CRYPTO_PWHASH_SCRYPTSALSA208SHA256_MEMLIMIT_INTERACTIVE' => true,
        'SODIUM_CRYPTO_PWHASH_SCRYPTSALSA208SHA256_OPSLIMIT_SENSITIVE' => true,
        'SODIUM_CRYPTO_PWHASH_SCRYPTSALSA208SHA256_MEMLIMIT_SENSITIVE' => true,
        'SODIUM_CRYPTO_SCALARMULT_BYTES' => true,
        'SODIUM_CRYPTO_SCALARMULT_SCALARBYTES' => true,
        'SODIUM_CRYPTO_SHORTHASH_BYTES' => true,
        'SODIUM_CRYPTO_SHORTHASH_KEYBYTES' => true,
        'SODIUM_CRYPTO_SECRETBOX_KEYBYTES' => true,
        'SODIUM_CRYPTO_SECRETBOX_MACBYTES' => true,
        'SODIUM_CRYPTO_SECRETBOX_NONCEBYTES' => true,
        'SODIUM_CRYPTO_SIGN_BYTES' => true,
        'SODIUM_CRYPTO_SIGN_SEEDBYTES' => true,
        'SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES' => true,
        'SODIUM_CRYPTO_SIGN_SECRETKEYBYTES' => true,
        'SODIUM_CRYPTO_SIGN_KEYPAIRBYTES' => true,
        'SODIUM_CRYPTO_STREAM_NONCEBYTES' => true,
        'SODIUM_CRYPTO_STREAM_KEYBYTES' => true,

        // PHP 7.3
        // Source: https://www.php.net/manual/en/migration73.constants.php
        'PASSWORD_ARGON2ID' => true,
        'CURLAUTH_BEARER' => true,
        'CURLAUTH_GSSAPI' => true,
        'CURLE_WEIRD_SERVER_REPLY' => true,
        'CURLINFO_APPCONNECT_TIME_T' => true,
        'CURLINFO_CONNECT_TIME_T' => true,
        'CURLINFO_CONTENT_LENGTH_DOWNLOAD_T' => true,
        'CURLINFO_CONTENT_LENGTH_UPLOAD_T' => true,
        'CURLINFO_FILETIME_T' => true,
        'CURLINFO_HTTP_VERSION' => true,
        'CURLINFO_NAMELOOKUP_TIME_T' => true,
        'CURLINFO_PRETRANSFER_TIME_T' => true,
        'CURLINFO_PROTOCOL' => true,
        'CURLINFO_PROXY_SSL_VERIFYRESULT' => true,
        'CURLINFO_REDIRECT_TIME_T' => true,
        'CURLINFO_SCHEME' => true,
        'CURLINFO_SIZE_DOWNLOAD_T' => true,
        'CURLINFO_SIZE_UPLOAD_T' => true,
        'CURLINFO_SPEED_DOWNLOAD_T' => true,
        'CURLINFO_SPEED_UPLOAD_T' => true,
        'CURLINFO_STARTTRANSFER_TIME_T' => true,
        'CURLINFO_TOTAL_TIME_T' => true,
        'CURL_LOCK_DATA_CONNECT' => true,
        'CURL_LOCK_DATA_PSL' => true,
        'CURL_MAX_READ_SIZE' => true,
        'CURLOPT_ABSTRACT_UNIX_SOCKET' => true,
        'CURLOPT_DISALLOW_USERNAME_IN_URL' => true,
        'CURLOPT_DNS_SHUFFLE_ADDRESSES' => true,
        'CURLOPT_HAPPY_EYEBALLS_TIMEOUT_MS' => true,
        'CURLOPT_HAPROXYPROTOCOL' => true,
        'CURLOPT_KEEP_SENDING_ON_ERROR' => true,
        'CURLOPT_PRE_PROXY' => true,
        'CURLOPT_PROXY_CAINFO' => true,
        'CURLOPT_PROXY_CAPATH' => true,
        'CURLOPT_PROXY_CRLFILE' => true,
        'CURLOPT_PROXY_KEYPASSWD' => true,
        'CURLOPT_PROXY_PINNEDPUBLICKEY' => true,
        'CURLOPT_PROXY_SSLCERT' => true,
        'CURLOPT_PROXY_SSLCERTTYPE' => true,
        'CURLOPT_PROXY_SSL_CIPHER_LIST' => true,
        'CURLOPT_PROXY_SSLKEY' => true,
        'CURLOPT_PROXY_SSLKEYTYPE' => true,
        'CURLOPT_PROXY_SSL_OPTIONS' => true,
        'CURLOPT_PROXY_SSL_VERIFYHOST' => true,
        'CURLOPT_PROXY_SSL_VERIFYPEER' => true,
        'CURLOPT_PROXY_SSLVERSION' => true,
        'CURLOPT_PROXY_TLS13_CIPHERS' => true,
        'CURLOPT_PROXY_TLSAUTH_PASSWORD' => true,
        'CURLOPT_PROXY_TLSAUTH_TYPE' => true,
        'CURLOPT_PROXY_TLSAUTH_USERNAME' => true,
        'CURLOPT_REQUEST_TARGET' => true,
        'CURLOPT_SOCKS5_AUTH' => true,
        'CURLOPT_SSH_COMPRESSION' => true,
        'CURLOPT_SUPPRESS_CONNECT_HEADERS' => true,
        'CURLOPT_TIMEVALUE_LARGE' => true,
        'CURLOPT_TLS13_CIPHERS' => true,
        'CURLPROXY_HTTPS' => true,
        'CURLSSH_AUTH_GSSAPI' => true,
        'CURL_SSLVERSION_MAX_DEFAULT' => true,
        'CURL_SSLVERSION_MAX_NONE' => true,
        'CURL_SSLVERSION_MAX_TLSv1_0' => true,
        'CURL_SSLVERSION_MAX_TLSv1_1' => true,
        'CURL_SSLVERSION_MAX_TLSv1_2' => true,
        'CURL_SSLVERSION_MAX_TLSv1_3' => true,
        'CURL_SSLVERSION_TLSv1_3' => true,
        'CURL_VERSION_ALTSVC' => true,
        'CURL_VERSION_ASYNCHDNS' => true,
        'CURL_VERSION_BROTLI' => true,
        'CURL_VERSION_CONV' => true,
        'CURL_VERSION_CURLDEBUG' => true,
        'CURL_VERSION_DEBUG' => true,
        'CURL_VERSION_GSSAPI' => true,
        'CURL_VERSION_GSSNEGOTIATE' => true,
        'CURL_VERSION_HTTPS_PROXY' => true,
        'CURL_VERSION_IDN' => true,
        'CURL_VERSION_LARGEFILE' => true,
        'CURL_VERSION_MULTI_SSL' => true,
        'CURL_VERSION_NTLM' => true,
        'CURL_VERSION_NTLM_WB' => true,
        'CURL_VERSION_PS' => true,
        'CURL_VERSION_SPNEGO' => true,
        'CURL_VERSION_SSPI' => true,
        'CURL_VERSION_TLSAUTH_SRP' => true,
        'FILTER_SANITIZE_ADD_SLASHES' => true,
        'JSON_THROW_ON_ERROR' => true,
        'LDAP_CONTROL_ASSERT' => true,
        'LDAP_CONTROL_MANAGEDSAIT' => true,
        'LDAP_CONTROL_PROXY_AUTHZ' => true,
        'LDAP_CONTROL_SUBENTRIES' => true,
        'LDAP_CONTROL_VALUESRETURNFILTER' => true,
        'LDAP_CONTROL_PRE_READ' => true,
        'LDAP_CONTROL_POST_READ' => true,
        'LDAP_CONTROL_SORTREQUEST' => true,
        'LDAP_CONTROL_SORTRESPONSE' => true,
        'LDAP_CONTROL_PAGEDRESULTS' => true,
        'LDAP_CONTROL_AUTHZID_REQUEST' => true,
        'LDAP_CONTROL_AUTHZID_RESPONSE' => true,
        'LDAP_CONTROL_SYNC' => true,
        'LDAP_CONTROL_SYNC_STATE' => true,
        'LDAP_CONTROL_SYNC_DONE' => true,
        'LDAP_CONTROL_DONTUSECOPY' => true,
        'LDAP_CONTROL_PASSWORDPOLICYREQUEST' => true,
        'LDAP_CONTROL_PASSWORDPOLICYRESPONSE' => true,
        'LDAP_CONTROL_X_INCREMENTAL_VALUES' => true,
        'LDAP_CONTROL_X_DOMAIN_SCOPE' => true,
        'LDAP_CONTROL_X_PERMISSIVE_MODIFY' => true,
        'LDAP_CONTROL_X_SEARCH_OPTIONS' => true,
        'LDAP_CONTROL_X_TREE_DELETE' => true,
        'LDAP_CONTROL_X_EXTENDED_DN' => true,
        'LDAP_CONTROL_VLVREQUEST' => true,
        'LDAP_CONTROL_VLVRESPONSE' => true,
        'MB_CASE_FOLD' => true,
        'MB_CASE_LOWER_SIMPLE' => true,
        'MB_CASE_UPPER_SIMPLE' => true,
        'MB_CASE_TITLE_SIMPLE' => true,
        'MB_CASE_FOLD_SIMPLE' => true,
        'STREAM_CRYPTO_PROTO_SSLv3' => true,
        'STREAM_CRYPTO_PROTO_TLSv1_0' => true,
        'STREAM_CRYPTO_PROTO_TLSv1_1' => true,
        'STREAM_CRYPTO_PROTO_TLSv1_2' => true,
        'PGSQL_DIAG_SCHEMA_NAME' => true,
        'PGSQL_DIAG_TABLE_NAME' => true,
        'PGSQL_DIAG_COLUMN_NAME' => true,
        'PGSQL_DIAG_DATATYPE_NAME' => true,
        'PGSQL_DIAG_CONSTRAINT_NAME' => true,
        'PGSQL_DIAG_SEVERITY_NONLOCALIZED' => true,
    ];

    private $classReflector;
    private $functionReflector;

    /**
     * @var array<string, mixed> Lazily instantiated internal constants.
     */
    private $constants;

    public function __construct(
        BetterReflectionReflector $classReflector,
        BetterReflectionReflector $functionReflector
    ) {
        $this->classReflector = $classReflector;
        $this->functionReflector = $functionReflector;
    }

    public function isClassInternal(string $name): bool
    {
        try {
            /** @var ReflectionClass $classReflection */
            $classReflection = $this->classReflector->reflect($name);

            return $classReflection->isInternal();
        } catch (IdentifierNotFound | InvalidIdentifierName $exception) {
            return false;
        }
    }

    public function isFunctionInternal(string $name): bool
    {
        try {
            /** @var ReflectionFunction $functionReflection */
            $functionReflection = $this->functionReflector->reflect($name);

            return $functionReflection->isInternal();
        } catch (IdentifierNotFound | InvalidIdentifierName $exception) {
            return false;
        }
    }

    public function isConstantInternal(string $name): bool
    {
        if (array_key_exists($name, self::KNOWN_INTERNAL_CONSTANTS)) {
            return true;
        }

        return array_key_exists(strtoupper($name), $this->getInternalConstants());
    }

    private function getInternalConstants(): array
    {
        if (null !== $this->constants) {
            return $this->constants;
        }

        $constants = get_defined_constants(true);

        unset($constants['user']);

        $this->constants = array_merge(...array_values($constants));

        return $this->constants;
    }
}
