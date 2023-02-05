#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ] || [ "$1" = 'composer' ] || [ "$1" = 'bin/phpunit' ]; then  

  if [ "$APP_ENV" = 'prod' ]; then
	  setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	  setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
  fi

	if [ "$APP_ENV" != 'prod' ]; then
    if [ ! -f config/jwt/private.pem ]; then
      jwt_passphrase=${JWT_PASSPHRASE:-$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='')}
      if ! echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -noout > /dev/null 2>&1; then
        echo "Generating public / private keys for JWT"
        mkdir -p config/jwt
        echo "$jwt_passphrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
        echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout
        setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
        setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
      fi
    fi
    
    export COMPOSER_HOME="/tmp/composer"
		composer install --prefer-dist --no-progress --no-interaction

    if grep -q DATABASE_URL= .env; then
      migrate-database || exit 1
      migrate-database -e test || exit 1
		fi
	fi
fi

exec docker-php-entrypoint "$@"
