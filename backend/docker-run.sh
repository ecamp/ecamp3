#!/bin/bash

chmod -R a+rw data

# Add hostname of docker host for XDebug to connect to, on Linux. On Win and Mac, Docker automatically resolves host.docker.internal
if [[ ! `getent hosts host.docker.internal | cut -d' ' -f1` ]]; then
    if ! grep "host.docker.internal" /etc/hosts > /dev/null ; then
        DOCKER_INTERNAL_IP=`/sbin/ip -4 route list match 0/0 | awk '{ print $3 }' | head -n 1`
        echo -e "$DOCKER_INTERNAL_IP\thost.docker.internal" >> /etc/hosts
        echo "Added host.docker.internal to /etc/hosts"
    fi
fi

apache2-foreground
