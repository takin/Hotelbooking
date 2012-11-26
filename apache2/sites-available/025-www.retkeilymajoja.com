NameVirtualHost 95.142.168.81:80

<VirtualHost 95.142.168.81:80>
        ServerName      www.retkeilymajoja.com
        ServerAlias     www.retkeilymajoja.com
        ServerAlias     retkeilymajoja.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/retkeilymajoja.com-access.log combined
        ErrorLog /opt/logs/retkeilymajoja.com-error.log
        LogLevel warn

</VirtualHost>

NameVirtualHost 95.142.168.81:443

<VirtualHost 95.142.168.81:443>

        ServerName      www.retkeilymajoja.com
        ServerAlias     www.retkeilymajoja.com
        ServerAlias     retkeilymajoja.com
        ServerAdmin     technical@mcwebmanagement.com

       	DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/retkeilymajoja.com-access.log combined
        ErrorLog /opt/logs/retkeilymajoja.com-error.log
        LogLevel warn

   	SSLEngine on

    	SSLCertificateFile      /opt/certificates/retkeilymajoja.com.crt
   	SSLCACertificateFile    /opt/certificates/retkeilymajoja.com.pem
    	SSLCertificateKeyFile   /opt/certificates/retkeilymajoja.com.key

</VirtualHost>

