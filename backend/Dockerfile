# Cloud Run requires linux/amd64
# Build with: docker build --platform linux/amd64 ...
# ============================================================
# Stage 1: Composer dependencies
# ============================================================
FROM --platform=linux/amd64 composer:2 AS composer-deps

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist --optimize-autoloader

# ============================================================
# Stage 2: Production image
# ============================================================
FROM --platform=linux/amd64 php:8.4-cli-alpine

# Install system dependencies for SQLite and S3 (Cloudflare R2)
RUN apk add --no-cache \
    sqlite-dev \
    && docker-php-ext-install pdo_sqlite

# Install Litestream for SQLite replication to GCS
ARG LITESTREAM_VERSION=v0.3.13
RUN wget -O /tmp/litestream.tar.gz \
    "https://github.com/benbjohnson/litestream/releases/download/${LITESTREAM_VERSION}/litestream-${LITESTREAM_VERSION}-linux-amd64.tar.gz" \
    && tar -C /usr/local/bin -xzf /tmp/litestream.tar.gz \
    && rm /tmp/litestream.tar.gz

# Create non-root user
RUN addgroup -S appgroup && adduser -S appuser -G appgroup

WORKDIR /app

# Copy Composer dependencies from build stage
COPY --from=composer-deps /app/vendor vendor/

# Copy application source
COPY . .

# Clear bootstrap cache from local dev (may reference dev-only packages like laravel/pail)
RUN rm -f bootstrap/cache/packages.php bootstrap/cache/services.php

# Run Composer post-install scripts (package:discover, etc.)
RUN php artisan package:discover --ansi 2>/dev/null || true

# Cache routes and views (NOT config — config uses runtime env vars from Cloud Run)
RUN php artisan route:cache || true \
    && php artisan view:cache || true

# Create SQLite database directory with correct permissions
RUN mkdir -p database storage/app storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && touch database/database.sqlite \
    && chown -R appuser:appgroup /app/database /app/storage

# Ensure entrypoint is executable
RUN chmod +x /app/docker-entrypoint.sh

# Switch to non-root user
USER appuser

# Cloud Run injects PORT env var (default 8080)
ENV PORT=8080
EXPOSE 8080

# Health check
HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
    CMD wget -qO- http://localhost:${PORT}/api/health || exit 1

# Use php artisan serve for simplicity on Cloud Run
# For higher traffic, switch to php-fpm + nginx or FrankenPHP
# Litestream handles: restore from GCS → migrate → replicate + serve
ENTRYPOINT ["/app/docker-entrypoint.sh"]
