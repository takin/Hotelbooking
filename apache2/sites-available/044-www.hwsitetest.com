<VirtualHost 95.142.164.11:80>

    ServerName      www.hwsitetest.com
    ServerAlias     hwsitetest.com
    ServerAdmin     technical@mcwebmanagement.com

    DocumentRoot "/opt/web"
    <Directory "/opt/web">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    CustomLog /opt/logs/hwsitetest.com-access.log combined
    ErrorLog /opt/logs/hwsitetest.com-error.log
    LogLevel warn

</VirtualHost>

NameVirtualHost 95.142.164.11:443

<VirtualHost 95.142.164.11:443>

    ServerName      www.hwsitetest.com
    ServerAlias     hwsitetest.com
    ServerAdmin     technical@mcwebmanagement.com

    DocumentRoot "/opt/web"
    <Directory "/opt/web">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    CustomLog /opt/logs/hwsitetest.com-access.log combined
    ErrorLog /opt/logs/hwsitetest.com-error.log
    LogLevel warn

    SSLEngine on

#    SSLCertificateFile      /opt/certificates/hwsitetest.com.crt
#    SSLCACertificateFile    /opt/certificates/hwsitetest.com.pem
#    SSLCertificateKeyFile   /opt/certificates/hwsitetest.com.key

    SSLCertificateFile    /etc/ssl/certs/ssl-cert-snakeoil.pem
    SSLCertificateKeyFile /etc/ssl/private/ssl-cert-snakeoil.key

</VirtualHost>

