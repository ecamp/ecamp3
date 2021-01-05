FROM buildkite/puppeteer
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
CMD npm run print
