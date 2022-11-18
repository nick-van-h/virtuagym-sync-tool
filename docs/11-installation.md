# Installation

## Download the project source code

Download the [latest release](https://github.com/nick-van-h/virtuagym-sync-tool/releases/latest) and extract to the project working directory.

## Obtain VirtuaGym API key

Request an API key (on this page)[https://api.virtuagym.com/public-api].

## Obtain Google Cloud API key

Set up a Google Workspace (on this page)[https://developers.google.com/workspace/guides/get-started]. Once completed, download the OAuth2.0 credentials json (from this page)[https://console.cloud.google.com/apis/credentials] and save it as _/path/to/config/oauth-credentials.json_

## Generate encryption key & iv

Generate two random strings for the encryption_key and encryption_iv.

Preferrably use a password manager, such as [Bitwarden password generator](https://bitwarden.com/password-generator/).

Recommended length for encryption_key: 32 characters

Recommended length for encryption_iv: 16 characters

## Server specific config files

Two templates are provided: _\_config_specific.php_ and _\_vst.ini_. Remove the leading underscore and update the content as following;

_config_specific.php_

```
const CONFIG_PATH = '/path/to/config'
const CONFIG_FILE = CONFIG_PATH . '/vst.ini';
const OAUTH_FILE = CONFIG_PATH . '/oauth_credentials.json';
```

_vst.ini_

```
host = "localhost"
username = "xxx"
password = "yyy"
database = "zzz"
encryption_key = "aaaaaa"
encryption_iv = "aaa"
virtuagym_api_key = "bbb"
google_api_key = "ccc"
```

Update according to the project and server config.

## Run Composer

Update vendor packages and dump autoload with the following 2 commands;

```
composer update
composer dump-autoload
```

## Update PHP include path

Add the vendor autoload directory to the include path in _php.ini_. Depending on your installation this file can usually be found under `/etc/php/x.x/apache2/` (Linux).

Find & update `include_path = ".:/full/path/to/project/root"`. The project root is the folder where composer.json resides.
