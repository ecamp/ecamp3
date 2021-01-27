FROM node:14.15.4-buster-slim@sha256:eb89a0a8d3d4572972fa0f247bec96d46e7b5da0b819b73857739788986bcbf5

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
