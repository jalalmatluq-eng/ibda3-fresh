# المرحلة الأولى: بناء ملفات الواجهة (Vite)
FROM node:20-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
RUN npm install
# انسخ كل الملفات اللازمة للبناء (بما في ذلك tailwind config و postcss إذا وجدا)
COPY . .
RUN npm run build

# المرحلة الثانية: إعداد بيئة PHP و Apache
FROM php:8.2-apache

# تثبيت الإضافات والمتطلبات
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
        zip \
        unzip \
        git \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo_mysql gd zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# تثبيت Composer
COPY --from=composer:latest /usr/local/bin/composer /usr/local/bin/composer

# 1. انسخ ملفات المشروع كاملة أولاً (لكي يجد الملحق ملف artisan)
COPY . .

# 2. انسخ ملفات الـ Assets المبنية من المرحلة الأولى
COPY --from=node_builder /app/public/build ./public/build

# 3. تثبيت مكتبات PHP
# لاحظ أننا وضعنا هذا الأمر بعد COPY لضمان وجود ملف artisan
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# إعدادات Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri 's!DocumentRoot /var/www/html!DocumentRoot /var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!<Directory /var/www/html>!<Directory /var/www/html/public>!g' /etc/apache2/apache2.conf /etc/apache2/sites-available/*.conf

# ضبط الصلاحيات
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# عمل Cache (استخدام || true لتفادي توقف البناء إذا لم تكن قاعدة البيانات متصلة)
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

EXPOSE 80
CMD ["apache2-foreground"]
