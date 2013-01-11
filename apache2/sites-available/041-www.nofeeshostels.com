NameVirtualHost 95.142.167.245:80

<VirtualHost 95.142.167.245:80>
        ServerName      www.nofeeshostels.com
        ServerAlias     www.nofeeshostels.com nofeeshostels.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/nofeeshostels.com-access.log combined
        ErrorLog /opt/logs/nofeeshostels.com-error.log
        LogLevel warn    

</VirtualHost>

NameVirtualHost 95.142.167.245:443

<VirtualHost 95.142.167.245:443>

        ServerName      www.nofeeshostels.com
        ServerAlias     www.nofeeshostels.com nofeeshostels.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/nofeeshostels.com-access.log combined
        ErrorLog /opt/logs/nofeeshostels.com-error.log
        LogLevel warn

    	SSLEngine on

        SSLCertificateFile      /opt/certificates/nofeeshostels.com.crt
        SSLCertificateKeyFile   /opt/certificates/nofeeshostels.com.key
        SSLCertificateChainFile /opt/certificates/nofeeshostels.com.ca-bundle

</VirtualHost>

