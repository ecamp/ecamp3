FROM buildkite/puppeteer
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
CMD npm run print
