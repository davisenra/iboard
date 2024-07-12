FROM ubuntu:22.04

ARG HOST_USER_ID
ARG HOST_GROUP_ID
ARG HOST_USER_NAME
ARG HOST_GROUP_NAME

RUN apt update -y && \
    apt upgrade -y

RUN apt install -y \
    gpg \
    curl \
    zip \
    libgd3 \
    software-properties-common

# Install PHP
RUN LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php
RUN apt-get update -y && apt-get install -y php8.3-cli php8.3-curl php8.3-sqlite php8.3-dom php8.3-zip php8.3-mbstring php8.3-gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install Node.js
ENV NODE_VERSION=22.4.1
RUN curl -fsSL https://nodejs.org/dist/v${NODE_VERSION}/node-v${NODE_VERSION}-linux-x64.tar.xz | tar -xJ && \
    mv node-v${NODE_VERSION}-linux-x64 /usr/local/node && \
    ln -s /usr/local/node/bin/node /usr/local/bin/node && \
    ln -s /usr/local/node/bin/npm /usr/local/bin/npm && \
    ln -s /usr/local/node/bin/npx /usr/local/bin/npx

COPY . /app

RUN groupadd -g ${HOST_GROUP_ID} ${HOST_GROUP_NAME} && \
    useradd -u ${HOST_USER_ID} -g ${HOST_GROUP_NAME} -m ${HOST_USER_NAME} && \
    chown -R ${HOST_USER_NAME}:${HOST_GROUP_NAME} /app

WORKDIR /app

USER ${HOST_USER_NAME}
