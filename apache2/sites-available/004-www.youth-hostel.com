NameVirtualHost 92.243.23.150:80

<VirtualHost 92.243.23.150:80>
        ServerName      www.youth-hostel.com
        ServerAlias     www.youth-hostel.com
        ServerAlias     youth-hostel.com
        ServerAdmin     technical@mcwebmanagement.com

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

    ErrorLog /srv/d_mcweb1/www/ajroot/logs/www.youth-hostel.com-error.log
    LogLevel warn

    SetEnvIf Remote_Addr "127\.0\.0\.1" loopback
    CustomLog /srv/d_mcweb1/www/ajroot/logs/www.youth-hostel.com-access.log combined env=!loopback
    ServerSignature On

    <IfModule mod_dav.c>
        DAVLockDB /srv/d_mcweb1/www/ajroot/db/DAVLock
    </IfModule>

    <IfModule mpm_peruser_module>
        ServerEnvironment adminftp_www-adminftp
        MaxProcessors 40
    </IfModule>

    Include /etc/apache2/sites-includes/www.youth-hostel.com/

</VirtualHost>

NameVirtualHost 92.243.23.150:443

<VirtualHost 92.243.23.150:443>

        ServerName      www.youth-hostel.com
        ServerAlias     www.youth-hostel.com
        ServerAlias     youth-hostel.com
        ServerAdmin     technical@mcwebmanagement.com

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

    ErrorLog /srv/d_mcweb1/www/ajroot/logs/www.youth-hostel.com-error.log
    LogLevel warn

    SetEnvIf Remote_Addr "127\.0\.0\.1" loopback
    CustomLog /srv/d_mcweb1/www/ajroot/logs/www.youth-hostel.com-access.log combined env=!loopback
    ServerSignature On

    <IfModule mod_dav.c>
        DAVLockDB /srv/d_mcweb1/www/ajroot/db/DAVLock
    </IfModule>

    <IfModule mpm_peruser_module>
        ServerEnvironment adminftp_www-adminftp
        MaxProcessors 40
    </IfModule>

    Include /etc/apache2/sites-includes/www.youth-hostel.com/

    SSLEngine on

    SSLCertificateFile      /etc/apache2/ssl-cert/youth-hostel.com.crt
    SSLCACertificateFile    /etc/apache2/ssl-cert/youth-hostel.com.pem
    SSLCertificateKeyFile   /etc/apache2/ssl-cert/youth-hostel.com.key

</VirtualHost>
