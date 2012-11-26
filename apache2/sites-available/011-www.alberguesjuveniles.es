NameVirtualHost 92.243.23.95:80

<VirtualHost 92.243.23.95:80>
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

NameVirtualHost 92.243.23.95:443
<VirtualHost 92.243.23.95:443>

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

    	SSLEngine on

    	SSLCertificateFile      /opt/certificates/alberguesjuveniles.es.crt
    	SSLCACertificateFile    /opt/certificates/alberguesjuveniles.es.pem
    	SSLCertificateKeyFile   /opt/certificates/alberguesjuveniles.es.key

</VirtualHost>

