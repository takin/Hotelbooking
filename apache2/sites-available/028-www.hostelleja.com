NameVirtualHost 95.142.168.85:80

<VirtualHost 95.142.168.85:80>
        ServerName      www.hostelleja.com
        ServerAlias     www.hostelleja.com
        ServerAlias     hostelleja.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hostelleja.com-access.log combined
        ErrorLog /opt/logs/hostelleja.com-error.log
        LogLevel warn

</VirtualHost>

NameVirtualHost 95.142.168.85:443

<VirtualHost 95.142.168.85:443>

        ServerName      www.hostelleja.com
        ServerAlias     www.hostelleja.com
        ServerAlias     hostelleja.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hostelleja.com-access.log combined
        ErrorLog /opt/logs/hostelleja.com-error.log
        LogLevel warn

    
    	SSLEngine on

    	SSLCertificateFile      /opt/certificates/hostelleja.com.crt
    	SSLCACertificateFile    /opt/certificates/hostelleja.com.pem
    	SSLCertificateKeyFile   /opt/certificates/hostelleja.com.key

</VirtualHost>

