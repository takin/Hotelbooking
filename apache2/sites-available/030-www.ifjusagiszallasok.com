NameVirtualHost 95.142.168.83:80

<VirtualHost 95.142.168.83:80>
        ServerName      www.ifjusagiszallasok.com
        ServerAlias     www.ifjusagiszallasok.com
        ServerAlias     ifjusagiszallasok.com
        ServerAdmin     gbourdages@graphem.ca

        DocumentRoot "/srv/d_mcweb6/www/ajroot/htdocs"
    <Directory "/srv/d_mcweb6/www/ajroot/htdocs">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    ScriptAlias /cgi-bin/ /srv/d_mcweb6/www/ajroot/cgi-bin/
    <Directory "/srv/d_mcweb6/www/ajroot/cgi-bin/">
        AllowOverride None
        Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
        Order allow,deny
        Allow from all
    </Directory>

    ErrorLog /srv/d_mcweb6/www/ajroot/logs/www.ifjusagiszallasok.com-error.log
    LogLevel warn

    SetEnvIf Remote_Addr "127\.0\.0\.1" loopback
    #CustomLog /srv/d_mcweb6/www/ajroot/logs/www.ifjusagiszallasok.com-access.log combined env=!loopback
    ServerSignature On

    <IfModule mod_dav.c>
        DAVLockDB /srv/d_mcweb6/www/ajroot/db/DAVLock
    </IfModule>

    <IfModule mpm_peruser_module>
        ServerEnvironment adminftp_www-adminftp
        MaxProcessors 40
    </IfModule>
    

</VirtualHost>

NameVirtualHost 95.142.168.83:443

<VirtualHost 95.142.168.83:443>

        ServerName      www.ifjusagiszallasok.com
        ServerAlias     www.ifjusagiszallasok.com
        ServerAlias     ifjusagiszallasok.com
        ServerAdmin     gbourdages@graphem.ca

        DocumentRoot "/srv/d_mcweb6/www/ajroot/htdocs"
    <Directory "/srv/d_mcweb6/www/ajroot/htdocs">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    ScriptAlias /cgi-bin/ /srv/d_mcweb6/www/ajroot/cgi-bin/
    <Directory "/srv/d_mcweb6/www/ajroot/cgi-bin/">
        AllowOverride None
        Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
        Order allow,deny
        Allow from all
    </Directory>

    ErrorLog /srv/d_mcweb6/www/ajroot/logs/www.ifjusagiszallasok.com-error.log
    LogLevel warn

    SetEnvIf Remote_Addr "127\.0\.0\.1" loopback
    #CustomLog /srv/d_mcweb6/www/ajroot/logs/www.ifjusagiszallasok.com-access.log combined env=!loopback
    ServerSignature On

    <IfModule mod_dav.c>
        DAVLockDB /srv/d_mcweb6/www/ajroot/db/DAVLock
    </IfModule>

    <IfModule mpm_peruser_module>
        ServerEnvironment adminftp_www-adminftp
        MaxProcessors 40
    </IfModule>

    
    SSLEngine on

    SSLCertificateFile      /etc/apache2/ssl-cert/ifjusagiszallasok.com.crt
    SSLCACertificateFile    /etc/apache2/ssl-cert/ifjusagiszallasok.com.pem
    SSLCertificateKeyFile   /etc/apache2/ssl-cert/ifjusagiszallasok.com.key

</VirtualHost>

