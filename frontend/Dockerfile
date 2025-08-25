# Stage 1: Build the React app using Vite
FROM node:18-alpine AS builder

# Set working directory
WORKDIR /app

# Copy package.json and package-lock.json
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy all files and build the app
COPY . .
RUN npm run build

# Stage 2: Serve the app using a lightweight server
FROM node:18-alpine

# Install 'serve' globally
RUN npm install -g serve

# Set working directory
WORKDIR /app

# Copy built files from builder stage
COPY --from=builder /app/dist .

# Expose port 8080 inside the container
EXPOSE 8080

# Start the server
CMD ["serve", "-s", ".", "-l", "8080"]
