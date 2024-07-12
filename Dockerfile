FROM dunglas/frankenphp

ARG HOST_USER_ID
ARG HOST_GROUP_ID
ARG HOST_USER_NAME
ARG HOST_GROUP_NAME

RUN install-php-extensions \
    pcntl

COPY . /app

RUN groupadd -g ${HOST_GROUP_ID} ${HOST_GROUP_NAME} && \
    useradd -u ${HOST_USER_ID} -g ${HOST_GROUP_NAME} -m ${HOST_USER_NAME} && \
    chown -R ${HOST_USER_NAME}:${HOST_GROUP_NAME} /app

USER ${HOST_USER_NAME}

WORKDIR /app

ENTRYPOINT ["php", "artisan", "octane:frankenphp"]
