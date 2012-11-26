NameVirtualHost 92.243.23.150:80

<VirtualHost 92.243.23.150:80>
        ServerName      www.ostellidellagioventu.com
        ServerAlias     www.ostellidellagioventu.com
        ServerAlias     ostellidellagioventu.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/ostellidellagioventu.com-access.log combined
        ErrorLog /opt/logs/ostellidellagioventu.com-error.log
        LogLevel warn

</VirtualHost>

NameVirtualHost 92.243.23.150:443

<VirtualHost 92.243.23.150:443>

        ServerName      www.ostellidellagioventu.com
        ServerAlias     www.ostellidellagioventu.com
        ServerAlias     ostellidellagioventu.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/ostellidellagioventu.com-access.log combined
        ErrorLog /opt/logs/ostellidellagioventu.com-error.log
        LogLevel warn

	SSLEngine on
       
	SSLCertificateFile      /opt/certificates/ostellidellagioventu.com.crt
	SSLCACertificateFile    /opt/certificates/ostellidellagioventu.com.pem
	SSLCertificateKeyFile   /opt/certificates/ostellidellagioventu.com.key

</VirtualHost>
