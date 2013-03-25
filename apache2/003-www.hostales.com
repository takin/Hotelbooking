
<VirtualHost *:80>
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

