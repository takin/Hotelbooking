NameVirtualHost 95.142.167.247:80

<VirtualHost 95.142.167.247:80>

    ServerName      www.hbsitetest.com
    ServerAlias     hbsitetest.com
    ServerAdmin     technical@mcwebmanagement.com

    DocumentRoot "/opt/web"
    <Directory "/opt/web">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
	AuthUserFile /etc/apache2/users.conf
	AuthName "This is a protected area" 
	AuthGroupFile /dev/null 
	AuthType Basic 
	Require valid-user 
    </Directory>

    CustomLog /opt/logs/hbsitetest.com-access.log combined
    ErrorLog /opt/logs/hbsitetest.com-error.log
    LogLevel warn

</VirtualHost>

NameVirtualHost 95.142.167.247:443

<VirtualHost 95.142.167.247:443>

    ServerName      www.hbsitetest.com
    ServerAlias     hbsitetest.com
    ServerAdmin     technical@mcwebmanagement.com

    DocumentRoot "/opt/web"
    <Directory "/opt/web">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        AuthUserFile /etc/apache2/users.conf
        AuthName "This is a protected area" 
        AuthGroupFile /dev/null 
        AuthType Basic
        Require valid-user
    </Directory>

    CustomLog /opt/logs/hbsitetest.com-access.log combined
    ErrorLog /opt/logs/hbsitetest.com-error.log
    LogLevel warn

    SSLEngine on

#    SSLCertificateFile      /opt/certificates/hbsitetest.com.crt
#    SSLCACertificateFile    /opt/certificates/hbsitetest.com.pem
#    SSLCertificateKeyFile   /opt/certificates/hbsitetest.com.key

    SSLCertificateFile    /etc/ssl/certs/ssl-cert-snakeoil.pem
    SSLCertificateKeyFile /etc/ssl/private/ssl-cert-snakeoil.key

</VirtualHost>
