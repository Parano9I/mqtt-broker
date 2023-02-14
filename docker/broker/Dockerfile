FROM node:18.13.0

# Set the working directory
WORKDIR /usr/src/app

# Copy source code
COPY --chown=node:node package*.json ./

RUN npm install -g nodemon


# Copy the rest of your app's source code from your host to your image filesystem.
COPY --chown=node:node . ./

RUN npm install

# Switch to 'node' user
USER node

# Open the mapped port
EXPOSE 8883

CMD ["npm", "run", "start"]