
<VirtualHost *:80>
        ServerName      www.alberguesjuveniles.es
        ServerAlias     www.alberguesjuveniles.es
        ServerAlias     alberguesjuveniles.es
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/alberguesjuveniles.es-access.log combined
        ErrorLog /opt/logs/alberguesjuveniles.es-error.log
        LogLevel warn

</VirtualHost>

