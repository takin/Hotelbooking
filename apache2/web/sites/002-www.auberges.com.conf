
<VirtualHost *:80>
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

