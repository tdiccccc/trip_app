FROM php:8.4-cli

ARG NODE_MAJOR=22
ARG USERNAME=vscode
ARG USER_UID=1000
ARG USER_GID=${USER_UID}

# System dependencies + SQLite
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        curl \
        unzip \
        zip \
        libzip-dev \
        libsqlite3-dev \
        sqlite3 \
        ca-certificates \
        gnupg \
    && docker-php-ext-install \
        pdo_sqlite \
        zip \
        pcntl \
    && rm -rf /var/lib/apt/lists/*

# Node.js 22
RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_MAJOR}.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Non-root user
RUN groupadd --gid ${USER_GID} ${USERNAME} \
    && useradd --uid ${USER_UID} --gid ${USER_GID} -m ${USERNAME} \
    && apt-get update && apt-get install -y --no-install-recommends sudo \
    && echo "${USERNAME} ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers.d/${USERNAME} \
    && chmod 0440 /etc/sudoers.d/${USERNAME} \
    && rm -rf /var/lib/apt/lists/*

# Claude CLI のインストール
RUN npm install -g @anthropic-ai/claude-code

USER ${USERNAME}

WORKDIR /workspace
