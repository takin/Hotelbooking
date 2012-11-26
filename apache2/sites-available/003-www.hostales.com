NameVirtualHost 95.142.170.13:80

<VirtualHost 95.142.170.13:80>
        ServerName      www.hostales.com
        ServerAlias     www.hostales.com
        ServerAlias     hostales.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
	<Directory "/opt/web">
        	Options Indexes FollowSymLinks MultiViews
        	AllowOverride All
        	Order allow,deny
        	Allow from all
	</Directory>

        CustomLog /opt/logs/hostales.com-access.log combined
        ErrorLog /opt/logs/hostales.com-error.log
        LogLevel warn

</VirtualHost>

NameVirtualHost 95.142.170.13:443

<VirtualHost 95.142.170.13:443>

        ServerName      www.hostales.com
        ServerAlias     www.hostales.com
        ServerAlias     hostales.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
	<Directory "/opt/web">
        	Options Indexes FollowSymLinks MultiViews
        	AllowOverride All
        	Order allow,deny
        	Allow from all
    	</Directory>

        CustomLog /opt/logs/hostales.com-access.log combined
        ErrorLog /opt/logs/hostales.com-error.log
        LogLevel warn

	SSLEngine on

	SSLCertificateFile      /opt/certificates/hostales.com.crt
	SSLCACertificateFile    /opt/certificates/hostales.com.pem
	SSLCertificateKeyFile   /opt/certificates/hostales.com.key

</VirtualHost>
