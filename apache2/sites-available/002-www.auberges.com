NameVirtualHost 95.142.170.8:80

<VirtualHost 95.142.170.8:80>
        ServerName      www.auberges.com
        ServerAlias     www.auberges.com
        ServerAlias     auberges.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
	<Directory "/opt/web">
		Options Indexes FollowSymLinks MultiViews
		AllowOverride All
		Order allow,deny
		Allow from all
	</Directory>

	CustomLog /opt/logs/auberges.com-access.log combined
        ErrorLog /opt/logs/auberges.com-error.log
        LogLevel warn

</VirtualHost>

NameVirtualHost 95.142.170.8:443

<VirtualHost 95.142.170.8:443>

        ServerName      www.auberges.com
        ServerAlias     www.auberges.com
        ServerAlias     auberges.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
	<Directory "/opt/web">
        	Options Indexes FollowSymLinks MultiViews
        	AllowOverride All
        	Order allow,deny
        	Allow from all
	</Directory>

        CustomLog /opt/logs/auberges.com-access.log combined
        ErrorLog /opt/logs/auberges.com-error.log
        LogLevel warn

	SSLEngine on
	
	SSLCertificateFile      /opt/certificates/auberges.com.crt
	SSLCACertificateFile    /opt/certificates/auberges.com.pem
	SSLCertificateKeyFile   /opt/certificates/auberges.com.key

</VirtualHost>
