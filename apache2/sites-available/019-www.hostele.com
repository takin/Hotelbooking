NameVirtualHost 95.142.168.86:80

<VirtualHost 95.142.168.86:80>
        ServerName      www.hostele.com
        ServerAlias     www.hostele.com
        ServerAlias     hostele.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hostele.com-access.log combined
        ErrorLog /opt/logs/hostele.com-error.log
        LogLevel warn

</VirtualHost>

NameVirtualHost 95.142.168.86:443

<VirtualHost 95.142.168.86:443>

        ServerName      www.hostele.com
        ServerAlias     www.hostele.com
        ServerAlias     hostele.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hostele.com-access.log combined
        ErrorLog /opt/logs/hostele.com-error.log
        LogLevel warn
    
    	SSLEngine on

    	SSLCertificateFile      /opt/certificates/hostele.com.crt
    	SSLCACertificateFile    /opt/certificates/hostele.com.pem
    	SSLCertificateKeyFile   /opt/certificates/hostele.com.key

</VirtualHost>

