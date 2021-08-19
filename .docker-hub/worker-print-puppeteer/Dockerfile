FROM node:16.7.0-buster-slim@sha256:9bafc78bbf0532721a30730961d96f6c4c57b4c0b16ea5bfe7db4ae2b8fc8e34

RUN  apt-get update \
     && apt-get install -y wget gnupg \
     && wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add - \
     && sh -c 'echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list' \
     && apt-get update \
     && apt-get install -y google-chrome-stable ca-certificates libxss1 \
     && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY workers/print-puppeteer/package*.json ./
RUN npm ci
COPY workers/print-puppeteer .
CMD npm run print
