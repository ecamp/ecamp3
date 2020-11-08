FROM node:12.18
WORKDIR /app
COPY print/package*.json ./
RUN npm install
COPY print .
RUN npm run build
EXPOSE 80
CMD npm run start
