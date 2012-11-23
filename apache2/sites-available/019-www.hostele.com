NameVirtualHost 95.142.168.86:80

<VirtualHost 95.142.168.86:80>
        ServerName      www.hostele.com
        ServerAlias     www.hostele.com
        ServerAlias     hostele.com
        ServerAdmin     gbourdages@graphem.ca

        DocumentRoot "/srv/d_mcweb7/www/ajroot/htdocs"
    <Directory "/srv/d_mcweb7/www/ajroot/htdocs">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    ScriptAlias /cgi-bin/ /srv/d_mcweb7/www/ajroot/cgi-bin/
    <Directory "/srv/d_mcweb7/www/ajroot/cgi-bin/">
        AllowOverride None
        Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
        Order allow,deny
        Allow from all
    </Directory>

    ErrorLog /srv/d_mcweb7/www/ajroot/logs/www.hostele.com-error.log
    LogLevel warn

    SetEnvIf Remote_Addr "127\.0\.0\.1" loopback
    CustomLog /srv/d_mcweb7/www/ajroot/logs/www.hostele.com-access.log combined env=!loopback
    ServerSignature On

    <IfModule mod_dav.c>
        DAVLockDB /srv/d_mcweb7/www/ajroot/db/DAVLock
    </IfModule>

    <IfModule mpm_peruser_module>
        ServerEnvironment adminftp_www-adminftp
        MaxProcessors 40
    </IfModule>
    

</VirtualHost>

NameVirtualHost 95.142.168.86:443

<VirtualHost 95.142.168.86:443>

        ServerName      www.hostele.com
        ServerAlias     www.hostele.com
        ServerAlias     hostele.com
        ServerAdmin     gbourdages@graphem.ca

        DocumentRoot "/srv/d_mcweb7/www/ajroot/htdocs"
    <Directory "/srv/d_mcweb7/www/ajroot/htdocs">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    ScriptAlias /cgi-bin/ /srv/d_mcweb7/www/ajroot/cgi-bin/
    <Directory "/srv/d_mcweb7/www/ajroot/cgi-bin/">
        AllowOverride None
        Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
        Order allow,deny
        Allow from all
    </Directory>

    ErrorLog /srv/d_mcweb7/www/ajroot/logs/www.hostele.com-error.log
    LogLevel warn

    SetEnvIf Remote_Addr "127\.0\.0\.1" loopback
    CustomLog /srv/d_mcweb7/www/ajroot/logs/www.hostele.com-access.log combined env=!loopback
    ServerSignature On

    <IfModule mod_dav.c>
        DAVLockDB /srv/d_mcweb7/www/ajroot/db/DAVLock
    </IfModule>

    <IfModule mpm_peruser_module>
        ServerEnvironment adminftp_www-adminftp
        MaxProcessors 40
    </IfModule>

    
    SSLEngine on

    SSLCertificateFile      /etc/apache2/ssl-cert/hostele.com.crt
    SSLCACertificateFile    /etc/apache2/ssl-cert/hostele.com.pem
    SSLCertificateKeyFile   /etc/apache2/ssl-cert/hostele.com.key

</VirtualHost>

