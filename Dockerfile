# المرحلة الأولى: جلب الـ Composer وتسمية المرحلة بشكل صريح لضمان التحميل وحساب الـ Checksum
FROM docker.io/library/composer:latest AS composer_base

# المرحلة الثانية: بناء ملفات الواجهة الأمامية (Vite)
FROM docker.io/library/node:20-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# المرحلة الثالثة: إعداد بيئة PHP و Apache وتجميع كل شيء
FROM docker.io/library/php:8.2-apache

# تثبيت الإضافات والمتطلبات الأساسية للنظام
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

# جلب ملف الـ Composer من المرحلة الأولى المعرفة بالأعلى بدلاً من السحب الخارجي المباشر
COPY --from=composer_base /usr/bin/composer /usr/local/bin/composer

# 1. نسخ ملفات المشروع بالكامل أولاً
COPY . .

# 2. نسخ ملفات الـ Assets التي تم بناؤها بواسطة Node
COPY --from=node_builder /app/public/build ./public/build

# 3. تشغيل Composer Install لتثبيت مكتبات السيرفر
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# ضبط إعدادات الـ Document Root لـ Apache لتوجه إلى مجلد public الخاص بـ Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri 's!DocumentRoot /var/www/html!DocumentRoot /var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!<Directory /var/www/html>!<Directory /var/www/html/public>!g' /etc/apache2/apache2.conf /etc/apache2/sites-available/*.conf

# منح صلاحيات القراءة والكتابة لمجلدات Laravel الأساسية
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# إنشاء التخزين المؤقت لـ Laravel لتسريع الأداء
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

EXPOSE 80
CMD ["apache2-foreground"]
