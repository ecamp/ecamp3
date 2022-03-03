FROM browserless/chrome:1.50.0-puppeteer-12.0.1@sha256:ca9c1a56ff88ba91c3aac908713cfe92f4e308c941a64e408d00d26bf1de036a

USER 0
RUN apt-get update -y && apt-get install -y fonts-open-sans && rm -rf /var/lib/apt/lists/*

USER 1000
