# Production Deployment Guide (Docker + VPS + Host Nginx + Cloudflare SSL)

This guide details how to deploy your Bagisto application to a Linux VPS using Docker, while utilizing the VPS's native Nginx server (instead of a Dockerized Nginx) and securing it with Cloudflare Origin Certificates.

This approach uses the **"Push to GitHub -> Clone on VPS"** workflow, which avoids cross-platform build issues (macOS vs Linux) by building the Docker image directly on the Linux VPS.

## Prerequisites

*   **Local Machine**:
    *   Your project pushed to a Git repository (GitHub, GitLab, etc.).
*   **VPS (Linux)**:
    *   Ubuntu 20.04/22.04 or Debian 11/12 recommended.
    *   **Docker** and **Docker Compose** installed.
    *   **Git** installed.
    *   **Nginx** installed directly on the host (`sudo apt install nginx`).
*   **Cloudflare**:
    *   Domain DNS managed by Cloudflare (Proxied status: Orange Cloud).
    *   SSL/TLS encryption mode set to **Full (Strict)**.

---

## Step 1: Prepare Project Configuration (Local)

Create these files in your project root on your local machine, then commit and push them to GitHub.

### 1. Create `Dockerfile`
Create a file named `Dockerfile` in the root of your project. This defines your PHP application image.

```dockerfile
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zip \
    unzip \
    nodejs \
    npm \
    default-mysql-client \
    librsvg2-bin

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by Bagisto
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        zip \
        soap \
        calendar

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# We do NOT copy the code here because we will mount it as a volume in docker-compose.
# This allows the Host Nginx to see the same files (like public/build) as the container.

# Expose port 9000 (PHP-FPM default)
EXPOSE 9000

CMD ["php-fpm"]
```

### 2. Create `docker-compose.prod.yml`
Create a production-specific Docker Compose file.

**Key Changes:**
*   **No Nginx Service**: We removed the Nginx container since we are using the Host's Nginx.
*   **Port Mapping**: We map the container's PHP-FPM port (9000) to the Host's port `9000` (or `8080` if you prefer, but 9000 is standard for FPM).
*   **Bind to Localhost**: `127.0.0.1:9000:9000` ensures the database and PHP are only accessible from the VPS itself, not the outside world.

```yaml
services:
    app:
        build:
            context: .
            dockerfile: Dockerfile
        image: bagisto-app
        container_name: bagisto-app
        restart: unless-stopped
        working_dir: /var/www
        ports:
            - "127.0.0.1:9000:9000" # Expose PHP-FPM to Host on port 9000
        volumes:
            - ./:/var/www
        networks:
            - bagisto-network
        depends_on:
            - db
            - redis
            - elasticsearch

    db:
        image: mysql:8.0
        container_name: bagisto-db
        restart: unless-stopped
        environment:
            MYSQL_DATABASE: ${DB_DATABASE}
            MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
            MYSQL_PASSWORD: ${DB_PASSWORD}
            MYSQL_USER: ${DB_USERNAME}
            MYSQL_ALLOW_EMPTY_PASSWORD: 1
        volumes:
            - dbdata:/var/lib/mysql
        networks:
            - bagisto-network

    redis:
        image: redis:alpine
        container_name: bagisto-redis
        restart: unless-stopped
        networks:
            - bagisto-network

    elasticsearch:
        image: docker.elastic.co/elasticsearch/elasticsearch:7.17.0
        container_name: bagisto-elastic
        restart: unless-stopped
        environment:
            - xpack.security.enabled=false
            - discovery.type=single-node
            - "ES_JAVA_OPTS=-Xms512m -Xmx512m"
        volumes:
            - esdata:/usr/share/elasticsearch/data
        networks:
            - bagisto-network

networks:
    bagisto-network:
        driver: bridge

volumes:
    dbdata:
    esdata:
```

### 3. Push to GitHub
```bash
git add Dockerfile docker-compose.prod.yml
git commit -m "Add production docker configuration"
git push origin main
```

---

## Step 2: VPS Setup & Deployment

Perform these steps on your **VPS**.

### 1. Clone the Repository
Clone your project to a directory (e.g., `/var/www/bagisto`).

```bash
cd /var/www
sudo git clone https://github.com/yourusername/vpp_bagisto.git bagisto
cd bagisto
```

*Note: Ensure your user has permission to write to this directory.*

### 2. Configure Environment (.env)
Copy the example `.env` and configure it for production.

```bash
cp .env.example .env
nano .env
```

**Critical `.env` Settings:**
```ini
APP_NAME=Bagisto
APP_ENV=production
APP_KEY=base64:... (Will generate later if empty)
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database (matches docker-compose service name 'db')
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=bagisto
DB_USERNAME=bagisto
DB_PASSWORD=secret_password_here

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Elasticsearch
ELASTICSEARCH_HOST=elasticsearch:9200
```

### 3. Build and Start Containers
Since we are on the VPS, Docker will build the image using the VPS's architecture (Linux).

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

---

## Step 3: Cloudflare SSL & Host Nginx Configuration

Since we are using the **Host's Nginx**, we need to configure it to serve static files directly and pass PHP requests to our Docker container on port `9000`.

### 1. Save Cloudflare Origin Certificates

1.  Go to **Cloudflare Dashboard > SSL/TLS > Origin Server**.
2.  Click **Create Certificate**.
3.  Keep default options (RSA 2048, generic validities) and click **Create**.
4.  You will see "Origin Certificate" and "Private Key".
5.  On your VPS, save these to files:

    **Origin Certificate:**
    ```bash
    sudo nano /etc/ssl/certs/cf_origin_cert.pem
    # Paste the "Origin Certificate" content here
    ```

    **Private Key:**
    ```bash
    sudo nano /etc/ssl/private/cf_origin_key.pem
    # Paste the "Private Key" content here
    ```

### 2. Create Nginx Config
Create a new configuration file.

```bash
sudo nano /etc/nginx/sites-available/bagisto
```

### 3. Paste Configuration
Replace `yourdomain.com` with your actual domain and `/var/www/bagisto` with your actual project path.

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    
    # Redirect all HTTP traffic to HTTPS
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    
    # Cloudflare Origin Certificates
    ssl_certificate /etc/ssl/certs/cf_origin_cert.pem;
    ssl_certificate_key /etc/ssl/private/cf_origin_key.pem;
    
    # SSL Optimization (Optional, but recommended)
    ssl_session_timeout 1d;
    ssl_session_cache shared:BagistoSSL:10m;
    ssl_session_tickets off;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # Root points to the cloned project's public folder on the Host
    root /var/www/bagisto/public;
    
    index index.php index.html;

    # Logs
    error_log  /var/log/nginx/bagisto-error.log;
    access_log /var/log/nginx/bagisto-access.log;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    # Gzip Compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Pass PHP scripts to Docker Container via FastCGI
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        
        # Connect to localhost:9000 (which is mapped to the Docker container)
        fastcgi_pass 127.0.0.1:9000;
        
        fastcgi_index index.php;
        include fastcgi_params;
        
        # IMPORTANT: SCRIPT_FILENAME must resolve to the path INSIDE the container.
        # Since we mounted ./:/var/www, the path /var/www/bagisto/public/index.php on host 
        # maps to /var/www/public/index.php in container.
        # We need to manually set this because $document_root on host (/var/www/bagisto/public) 
        # might differ from container (/var/www/public).
        
        fastcgi_param SCRIPT_FILENAME /var/www/public$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    # Static assets caching
    location ~* \.(jpg|jpeg|gif|png|css|js|ico|xml|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        access_log off;
        add_header Cache-Control "public";
    }
}
```

### 4. Enable Site and Restart Nginx

```bash
sudo ln -s /etc/nginx/sites-available/bagisto /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## Step 4: Publish Resources & Final Setup

Now that the containers are running and Nginx is configured, we need to initialize the application and generate the assets.

Run these commands **inside the Docker container**:

```bash
# 1. Enter the container
docker compose -f docker-compose.prod.yml exec app bash

# --- NOW INSIDE CONTAINER ---

# 2. Install PHP Dependencies
composer install --optimize-autoloader --no-dev

# 3. Fix Directory Permissions (Critical for Storage)
chown -R www-data:www-data /var/www
chmod -R 775 storage bootstrap/cache

# 4. Generate App Key (if not in .env)
php artisan key:generate

# 5. Run Database Migrations
php artisan migrate --force

# 6. Seed Database (ONLY for new installations)
php artisan db:seed --force

# 7. Publish Assets (Copies images/css/js from packages to public/)
php artisan vendor:publish --all --force

# 8. Link Storage (Makes storage/app/public accessible)
php artisan storage:link

# 9. Build Frontend Assets (Vite)
# This generates build files in /var/www/public/build
# Since /var/www is a volume, Host Nginx can see these files immediately.
npm install
npm run build

# 10. Cache Config for Performance
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Exit container
exit
```

## Summary of Architecture

1.  **Request Flow**: User -> Cloudflare -> Host Nginx (Port 443)
2.  **Encryption**: Traffic between Cloudflare and VPS is encrypted using Origin CA Certs.
3.  **Static Files**: Host Nginx serves `.css`, `.js`, `.jpg` directly from `/var/www/bagisto/public` (Host filesystem).
4.  **PHP Requests**: Host Nginx passes request via FastCGI to `127.0.0.1:9000`.
5.  **Docker Processing**: Docker maps Host:9000 -> Container:9000.
6.  **Execution**: `php-fpm` inside container executes code.
7.  **Database**: Container connects to `db` container via Docker network.
