# Site vitrine BAB'S-BAT — image PHP + Apache pour Dokploy
FROM php:8.3-apache

# Modules Apache utilisés par .htaccess + autoriser .htaccess dans /var/www
RUN a2enmod rewrite headers expires \
 && sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf \
 && echo 'ServerTokens Prod\nServerSignature Off' > /etc/apache2/conf-enabled/zz-security.conf \
 && echo 'ServerName localhost' > /etc/apache2/conf-enabled/zz-servername.conf

# Réglages PHP de production
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
 && { \
      echo 'expose_php = Off'; \
      echo 'date.timezone = Africa/Abidjan'; \
      echo 'session.cookie_httponly = 1'; \
      echo 'session.cookie_samesite = Lax'; \
      echo 'session.use_strict_mode = 1'; \
    } > "$PHP_INI_DIR/conf.d/zz-site.ini"

WORKDIR /var/www/html
COPY --chown=www-data:www-data . .

# Dossier des demandes de devis (à monter en volume dans Dokploy pour le conserver)
RUN mkdir -p storage && chown -R www-data:www-data storage && chmod 750 storage
VOLUME ["/var/www/html/storage"]

EXPOSE 80
