ARG VERSION=9.1.0

FROM varnish:${VERSION}

USER root

RUN set -e; \
    apt-get update; \
    apt-get -y install prometheus-varnish-exporter;

RUN rm -rf /var/lib/apt/lists/*;

# From https://github.com/varnish/docker-varnish/blob/835e048a302313efad1c9be5f09061303792083f/stable/debian/Dockerfile#L30
USER 1000
