NameVirtualHost 92.243.20.50:80

<VirtualHost 92.243.20.50:80>
        ServerName      www.herbergen.com
        ServerAlias     www.herbergen.com
        ServerAlias     herbergen.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hebergen.com-access.log combined
        ErrorLog /opt/logs/hebergen.com-error.log
        LogLevel warn

</VirtualHost>

NameVirtualHost 92.243.20.50:443

<VirtualHost 92.243.20.50:443>

        ServerName      www.herbergen.com
        ServerAlias     www.herbergen.com
        ServerAlias     herbergen.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hebergen.com-access.log combined
        ErrorLog /opt/logs/hebergen.com-error.log
        LogLevel warn

    	SSLEngine on

    	SSLCertificateFile      /opt/certificates/herbergen.com.crt
    	SSLCACertificateFile    /opt/certificates/herbergen.com.pem
    	SSLCertificateKeyFile   /opt/certificates/herbergen.com.key

</VirtualHost>

