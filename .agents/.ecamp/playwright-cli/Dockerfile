FROM mcr.microsoft.com/playwright:v1.62.1-noble

WORKDIR /playwright-cli
COPY package.json package-lock.json ./

RUN PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=1 npm ci

ENV PLAYWRIGHT_BROWSERS_PATH=/ms-playwright
RUN npx --prefix node_modules/@playwright/cli playwright install chromium firefox webkit

ENV PATH="/playwright-cli/node_modules/.bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
