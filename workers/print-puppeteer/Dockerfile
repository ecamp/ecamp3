# A minimal Docker image with Node and Puppeteer
#
# Initially based upon:
# https://github.com/GoogleChrome/puppeteer/blob/master/docs/troubleshooting.md#running-puppeteer-in-docker

FROM node:14.17.2-buster-slim@sha256:415f59555c5327f15c0f1487755f4759fbffc57110a681fb3b4567ad78373937

RUN  apt-get update \
     && apt-get install -y wget gnupg \
     && wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add - \
     && sh -c 'echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list' \
     && apt-get update \
     # We install Chrome to get all the OS level dependencies, but Chrome itself
     # is not actually used as it's packaged in the node puppeteer library.
     # Alternatively, we could could include the entire dep list ourselves
     # (https://github.com/puppeteer/puppeteer/blob/master/docs/troubleshooting.md#chrome-headless-doesnt-launch-on-unix)
     # but that seems too easy to get out of date.
     && apt-get install -y google-chrome-stable ca-certificates libxss1 \
     && rm -rf /var/lib/apt/lists/*

WORKDIR /app
