NameVirtualHost 92.243.21.2:80

<VirtualHost 92.243.21.2:80>
        ServerName      www.alberguesjuveniles.com
        ServerAlias     www.alberguesjuveniles.com
        ServerAlias     alberguesjuveniles.com
        ServerAdmin     gbourdages@graphem.ca

        DocumentRoot "/srv/d_mcweb1/www/ajroot/htdocs"
    <Directory "/srv/d_mcweb1/www/ajroot/htdocs">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    ScriptAlias /cgi-bin/ /srv/d_mcweb1/www/ajroot/cgi-bin/
    <Directory "/srv/d_mcweb1/www/ajroot/cgi-bin/">
        AllowOverride None
        Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
        Order allow,deny
        Allow from all
    </Directory>

    ErrorLog /srv/d_mcweb1/www/ajroot/logs/www.alberguesjuveniles.com-error.log
    LogLevel warn

    SetEnvIf Remote_Addr "127\.0\.0\.1" loopback
    #CustomLog /srv/d_mcweb1/www/ajroot/logs/www.alberguesjuveniles.com-access.log combined env=!loopback
    ServerSignature On

    <IfModule mod_dav.c>
        DAVLockDB /srv/d_mcweb1/www/ajroot/db/DAVLock
    </IfModule>

    <IfModule mpm_peruser_module>
        ServerEnvironment adminftp_www-adminftp
        MaxProcessors 40
    </IfModule>

    Include /etc/apache2/sites-includes/www.alberguesjuveniles.com/

</VirtualHost>

NameVirtualHost 92.243.21.2:443

<VirtualHost 92.243.21.2:443>

        ServerName      www.alberguesjuveniles.com
        ServerAlias     www.alberguesjuveniles.com
        ServerAlias     alberguesjuveniles.com
        ServerAdmin     gbourdages@graphem.ca

        DocumentRoot "/srv/d_mcweb1/www/ajroot/htdocs"
    <Directory "/srv/d_mcweb1/www/ajroot/htdocs">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    ScriptAlias /cgi-bin/ /srv/d_mcweb1/www/ajroot/cgi-bin/
    <Directory "/srv/d_mcweb1/www/ajroot/cgi-bin/">
        AllowOverride None
        Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
        Order allow,deny
        Allow from all
    </Directory>

    ErrorLog /srv/d_mcweb1/www/ajroot/logs/www.alberguesjuveniles.com-error.log
    LogLevel warn

    SetEnvIf Remote_Addr "127\.0\.0\.1" loopback
    #CustomLog /srv/d_mcweb1/www/ajroot/logs/www.alberguesjuveniles.com-access.log combined env=!loopback
    ServerSignature On

    <IfModule mod_dav.c>
        DAVLockDB /srv/d_mcweb1/www/ajroot/db/DAVLock
    </IfModule>

    <IfModule mpm_peruser_module>
        ServerEnvironment adminftp_www-adminftp
        MaxProcessors 40
    </IfModule>

    Include /etc/apache2/sites-includes/www.alberguesjuveniles.com/
       
    SSLEngine on
       
    SSLCertificateFile      /etc/apache2/ssl-cert/alberguesjuveniles.crt
    SSLCACertificateFile    /etc/apache2/ssl-cert/alberguesjuveniles.pem
    SSLCertificateKeyFile   /etc/apache2/ssl-cert/alberguesjuveniles.key

</VirtualHost>
