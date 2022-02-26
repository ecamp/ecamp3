FROM browserless/chrome:1.50.0-puppeteer-12.0.1

USER 0
RUN apt-get update -y && apt-get install -y fonts-open-sans && rm -rf /var/lib/apt/lists/*

USER 1000
