NameVirtualHost 95.142.165.226:80

<VirtualHost 95.142.165.226:80>
        ServerName      www.pousadasdejuventude.com
        ServerAlias     www.pousadasdejuventude.com
        ServerAlias     pousadasdejuventude.com
        ServerAdmin     gbourdages@graphem.ca

        DocumentRoot "/srv/d_mcweb5/www/ajroot/htdocs"
    <Directory "/srv/d_mcweb5/www/ajroot/htdocs">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    ScriptAlias /cgi-bin/ /srv/d_mcweb5/www/ajroot/cgi-bin/
    <Directory "/srv/d_mcweb5/www/ajroot/cgi-bin/">
        AllowOverride None
        Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
        Order allow,deny
        Allow from all
    </Directory>

    ErrorLog /srv/d_mcweb5/www/ajroot/logs/www.pousadasdejuventude.com-error.log
    LogLevel warn

    SetEnvIf Remote_Addr "127\.0\.0\.1" loopback
    #CustomLog /srv/d_mcweb5/www/ajroot/logs/www.pousadasdejuventude.com-access.log combined env=!loopback
    ServerSignature On

    <IfModule mod_dav.c>
        DAVLockDB /srv/d_mcweb5/www/ajroot/db/DAVLock
    </IfModule>

    <IfModule mpm_peruser_module>
        ServerEnvironment adminftp_www-adminftp
        MaxProcessors 40
    </IfModule>
    

</VirtualHost>

NameVirtualHost 95.142.165.226:443

<VirtualHost 95.142.165.226:443>

        ServerName      www.pousadasdejuventude.com
        ServerAlias     www.pousadasdejuventude.com
        ServerAlias     pousadasdejuventude.com
        ServerAdmin     gbourdages@graphem.ca

        DocumentRoot "/srv/d_mcweb5/www/ajroot/htdocs"
    <Directory "/srv/d_mcweb5/www/ajroot/htdocs">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    ScriptAlias /cgi-bin/ /srv/d_mcweb5/www/ajroot/cgi-bin/
    <Directory "/srv/d_mcweb5/www/ajroot/cgi-bin/">
        AllowOverride None
        Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
        Order allow,deny
        Allow from all
    </Directory>

    ErrorLog /srv/d_mcweb5/www/ajroot/logs/www.pousadasdejuventude.com-error.log
    LogLevel warn

    SetEnvIf Remote_Addr "127\.0\.0\.1" loopback
    #CustomLog /srv/d_mcweb5/www/ajroot/logs/www.pousadasdejuventude.com-access.log combined env=!loopback
    ServerSignature On

    <IfModule mod_dav.c>
        DAVLockDB /srv/d_mcweb5/www/ajroot/db/DAVLock
    </IfModule>

    <IfModule mpm_peruser_module>
        ServerEnvironment adminftp_www-adminftp
        MaxProcessors 40
    </IfModule>

    
    SSLEngine on

    SSLCertificateFile      /etc/apache2/ssl-cert/pousadasdejuventude.com.crt
    SSLCACertificateFile    /etc/apache2/ssl-cert/pousadasdejuventude.com.pem
    SSLCertificateKeyFile   /etc/apache2/ssl-cert/pousadasdejuventude.com.key

</VirtualHost>

