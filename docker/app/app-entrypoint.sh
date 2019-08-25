#!/usr/bin/env sh
set -e

APP_DIR="${APP_DIR:-/app}";
STARTUP_DELAY="${STARTUP_DELAY:-0}";
STARTUP_WAIT_FOR_SERVICES="${STARTUP_WAIT_FOR_SERVICES:-false}";
STARTUP_SETUP_RABBIT="${STARTUP_SETUP_RABBIT:-false}";
JWT_SECRET_KEY="${JWT_SECRET_KEY:-config/jwt/private.pem}"
JWT_PUBLIC_KEY="${JWT_PUBLIC_KEY:-config/jwt/public.pem}"

if [ "$STARTUP_DELAY" -gt 0 ]; then
  echo "[INFO] Wait $STARTUP_DELAY seconds before start ..";
  sleep "$STARTUP_DELAY";
fi;

if ! php "${APP_DIR}/bin/console" --version > /dev/null 2>&1; then
  (>&2 echo "[WARNING] Application probably broken down!");
fi;

if [ ! -f "${APP_DIR}/${JWT_SECRET_KEY}" ] || [ ! -f "${APP_DIR}/${JWT_PUBLIC_KEY}" ]; then
    openssl genrsa -out "${APP_DIR}/${JWT_SECRET_KEY}" 4096
    openssl rsa -pubout -in "${APP_DIR}/${JWT_SECRET_KEY}" -out "${APP_DIR}/${JWT_PUBLIC_KEY}"
fi

# Wait for services ready state
if [ "$STARTUP_WAIT_FOR_SERVICES" = "true" ]; then
  echo '[INFO] Wait for services ready state';
  counter=0;
  try_limit=60;

  while :; do
    counter=$(($counter + 1));

    if [ $counter -gt $try_limit ]; then
      echo '[ERROR] Errors limit reached'; sleep 10; exit 1;
    fi;

    # Call any commands here for make sure that all dependent services is up and ready
    ( 
      php "$APP_DIR/bin/console" doctrine:schema:update --force &&
      php "$APP_DIR/bin/console" --version 
    ) 2>&1 && break;

    echo '[INFO] Required for application starting services is not ready. Wait for 2 seconds ..'; sleep 2;
  done;
fi;

exec "$@";
