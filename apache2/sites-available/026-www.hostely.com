NameVirtualHost 95.142.168.87:80

<VirtualHost 95.142.168.87:80>
        ServerName      www.hostely.com
        ServerAlias     www.hostely.com
        ServerAlias     hostely.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hostely.com-access.log combined
        ErrorLog /opt/logs/hostely.com-error.log
        LogLevel warn

</VirtualHost>

NameVirtualHost 95.142.168.87:443

<VirtualHost 95.142.168.87:443>

        ServerName      www.hostely.com
        ServerAlias     www.hostely.com
        ServerAlias     hostely.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hostely.com-access.log combined
        ErrorLog /opt/logs/hostely.com-error.log
        LogLevel warn
    
    	SSLEngine on

    	SSLCertificateFile      /opt/certificates/hostely.com.crt
    	SSLCACertificateFile    /opt/certificates/hostely.com.pem
    	SSLCertificateKeyFile   /opt/certificates/hostely.com.key

</VirtualHost>

