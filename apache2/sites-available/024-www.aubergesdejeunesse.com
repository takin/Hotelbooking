NameVirtualHost 92.243.23.28:80

<VirtualHost 92.243.23.28:80>
        ServerName      www.aubergesdejeunesse.com
        ServerAlias     www.aubergesdejeunesse.com
        ServerAlias     aubergesdejeunesse.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/aubergesdejeunesse.com-access.log combined
        ErrorLog /opt/logs/aubergesdejeunesse.com-error.log
        LogLevel warn
 

</VirtualHost>

NameVirtualHost 92.243.23.28:443

<VirtualHost 92.243.23.28:443>

        ServerName      www.aubergesdejeunesse.com
        ServerAlias     www.aubergesdejeunesse.com
        ServerAlias     aubergesdejeunesse.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/aubergesdejeunesse.com-access.log combined
        ErrorLog /opt/logs/aubergesdejeunesse.com-error.log
        LogLevel warn


  	SSLEngine on

    	SSLCertificateFile      /opt/certificates/aubergesdejeunesse.com.crt
    	SSLCACertificateFile    /opt/certificates/aubergesdejeunesse.com.pem
    	SSLCertificateKeyFile   /opt/certificates/aubergesdejeunesse.com.key

</VirtualHost>

