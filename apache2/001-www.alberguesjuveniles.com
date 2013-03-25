
<VirtualHost *:80>
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


