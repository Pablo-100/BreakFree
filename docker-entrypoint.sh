#!/bin/sh
set -eu

APP_ROOT="/app"
ENV_FILE="$APP_ROOT/.env"

emit_env() {
    key="$1"
    value="${2:-}"
    printf '%s=%s\n' "$key" "$value" >> "$ENV_FILE"
}

: > "$ENV_FILE"

emit_env "DB_HOST" "${DB_HOST:-localhost}"
emit_env "DB_PORT" "${DB_PORT:-5432}"
emit_env "DB_NAME" "${DB_NAME:-breakfree}"
emit_env "DB_USER" "${DB_USER:-postgres}"
emit_env "DB_PASS" "${DB_PASS:-}"
emit_env "DB_SSLMODE" "${DB_SSLMODE:-require}"

emit_env "APP_NAME" "${APP_NAME:-BreakFree}"
emit_env "APP_ENV" "${APP_ENV:-production}"
emit_env "APP_DEBUG" "${APP_DEBUG:-false}"

app_url="${APP_URL:-}"
if [ -z "$app_url" ] && [ -n "${RENDER_EXTERNAL_URL:-}" ]; then
    app_url="$RENDER_EXTERNAL_URL"
fi
if [ -z "$app_url" ] && [ -n "${RENDER_EXTERNAL_HOSTNAME:-}" ]; then
    app_url="https://${RENDER_EXTERNAL_HOSTNAME}"
fi
if [ -z "$app_url" ]; then
    app_url="http://localhost:${PORT:-10000}"
fi
case "$app_url" in
    http://localhost*|http://127.0.0.1*)
        ;;
    http://*)
        app_url="https://${app_url#http://}"
        ;;
esac
emit_env "APP_URL" "$app_url"

if [ -n "${APP_SECRET:-}" ]; then
    emit_env "APP_SECRET" "$APP_SECRET"
fi

emit_env "MAIL_HOST" "${MAIL_HOST:-}"
emit_env "MAIL_PORT" "${MAIL_PORT:-587}"
emit_env "MAIL_USER" "${MAIL_USER:-}"
emit_env "MAIL_PASS" "${MAIL_PASS:-}"
emit_env "MAIL_FROM" "${MAIL_FROM:-}"
emit_env "MAIL_FROM_NAME" "${MAIL_FROM_NAME:-BreakFree}"

exec php -S "0.0.0.0:${PORT:-10000}" -t "$APP_ROOT/public" "$APP_ROOT/public/router.php"
