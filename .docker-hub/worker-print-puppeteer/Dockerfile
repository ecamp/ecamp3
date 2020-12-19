FROM buildkite/puppeteer
WORKDIR /app
COPY workers/print-puppeteer/package*.json ./
RUN npm install
COPY workers/print-puppeteer .
CMD npm run print
