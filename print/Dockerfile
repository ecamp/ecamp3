FROM node:18.12.1@sha256:9ebe9845301ca70e879f22180dfff496b856bb5f2ec52397462a9f5f903ee654

ENV NPM_CONFIG_CACHE=/home/node/.npm

RUN mkdir -p /home/node/.cache
RUN chown node:node /home/node/.cache

RUN mkdir -p /home/node/.npm/_cacache
RUN chown -R node:node /home/node/.npm

WORKDIR /app

CMD ["./docker-setup.sh"]