FROM node:18.15.0

ENV NPM_CONFIG_CACHE=/home/node/.npm

RUN mkdir -p /home/node/.cache
RUN chown node:node /home/node/.cache

RUN mkdir -p /home/node/.npm/_cacache
RUN chown -R node:node /home/node/.npm

WORKDIR /app

CMD ["./docker-setup.sh"]