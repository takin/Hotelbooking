NameVirtualHost 92.243.21.2:80

<VirtualHost 92.243.21.2:80>
	ServerName      www.alberguesjuveniles.com
	ServerAlias     www.alberguesjuveniles.com
        ServerAlias     alberguesjuveniles.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
	<Directory "/opt/web">
		Options Indexes FollowSymLinks MultiViews
        	AllowOverride All
        	Order allow,deny
        	Allow from all
	</Directory>

	CustomLog /opt/logs/alberguesjuveniles.com-access.log combined
	ErrorLog /opt/logs/alberguesjuveniles.com-error.log
	LogLevel warn

</VirtualHost>

NameVirtualHost 92.243.21.2:443

<VirtualHost 92.243.21.2:443>

        ServerName      www.alberguesjuveniles.com
        ServerAlias     www.alberguesjuveniles.com
        ServerAlias     alberguesjuveniles.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
	<Directory "/srv/d_mcweb1/www/ajroot/htdocs">
		Options Indexes FollowSymLinks MultiViews
		AllowOverride All
		Order allow,deny
		Allow from all
	</Directory>

	CustomLog /opt/logs/alberguesjuveniles.com-access.log combined
        ErrorLog /opt/logs/alberguesjuveniles.com-error.log
        LogLevel warn

	SSLEngine on 
	SSLCertificateFile      /opt/certificates/alberguesjuveniles.com.crt
	SSLCACertificateFile    /opt/certificates/alberguesjuveniles.com.pem
	SSLCertificateKeyFile   /opt/certificates/alberguesjuveniles.com.key

</VirtualHost>
