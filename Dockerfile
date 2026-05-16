FROM php:8.2-apache

# تفعيل mod_rewrite الخاص بـ Apache
RUN a2enmod rewrite

# تثبيت الحزم الأساسية وإضافات PHP المطلوبة (شاملة دعم PostgreSQL و MySQL)
RUN apt-get update -y && apt-get install -y \
    libicu-dev \
    unzip \
    zip \
    libpq-dev \
    curl \
    && docker-php-ext-install gettext intl pdo_mysql pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# تثبيت Node.js لبناء أصول Vite / TailwindCSS
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# توجيه Apache للعمل من مجلد public الخاص بـ Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# نسخ ملفات المشروع
COPY . /var/www/html
WORKDIR /var/www/html

# تثبيت Composer والاعتماديات
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# بناء أصول الواجهة (TailwindCSS + Vite)
RUN npm ci && npm run build && rm -rf node_modules

# إنشاء ملف SQLite احتياطي
RUN touch database/database.sqlite

# ضبط صلاحيات المجلدات
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# سكريبت بدء التشغيل: كاش + هجرات + Apache
RUN printf '#!/bin/bash\nphp artisan config:cache\nphp artisan route:cache\nphp artisan view:cache\nphp artisan migrate --force\nphp artisan db:seed --force\napache2-foreground\n' > /var/www/html/start.sh \
    && chmod +x /var/www/html/start.sh

EXPOSE 80

CMD ["/var/www/html/start.sh"]
