NameVirtualHost 95.142.165.140:80

<VirtualHost 95.142.165.140:80>
        ServerName      www.schroniskamlodziezowe.com
        ServerAlias     www.schroniskamlodziezowe.com
        ServerAlias     schroniskamlodziezowe.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/schroniskamlodziezowe.com-access.log combined
        ErrorLog /opt/logs/schroniskamlodziezowe.com-error.log
        LogLevel warn

</VirtualHost>

NameVirtualHost 95.142.165.140:443

<VirtualHost 95.142.165.140:443>

        ServerName      www.schroniskamlodziezowe.com
        ServerAlias     www.schroniskamlodziezowe.com
        ServerAlias     schroniskamlodziezowe.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/schroniskamlodziezowe.com-access.log combined
        ErrorLog /opt/logs/schroniskamlodziezowe.com-error.log
        LogLevel warn

    	SSLEngine on

    	SSLCertificateFile      /opt/certificates/schroniskamlodziezowe.com.crt
    	SSLCACertificateFile    /opt/certificates/schroniskamlodziezowe.com.pem
    	SSLCertificateKeyFile   /opt/certificates/schroniskamlodziezowe.com.key

</VirtualHost>
