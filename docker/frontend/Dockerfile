FROM node:16.9-alpine

RUN apk update && apk upgrade && apk add curl py-pip make g++

WORKDIR /var/www/

RUN chown -R node:node /var/www/

COPY --chown=node:node package*.json .

RUN npm install -g @vue/cli-service && \
    npm install

COPY --chown=node:node . .

EXPOSE 8080

CMD ["npm", "run", "serve"]