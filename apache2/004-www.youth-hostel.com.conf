
<VirtualHost *:80>
        ServerName      www.youth-hostel.com
        ServerAlias     www.youth-hostel.com
        ServerAlias     youth-hostel.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
	<Directory "/opt/web">
        	Options Indexes FollowSymLinks MultiViews
        	AllowOverride All
        	Order allow,deny
        	Allow from all
	</Directory>

        CustomLog /opt/logs/youth-hostel.com-access.log combined
        ErrorLog /opt/logs/youth-hostel.com-error.log
        LogLevel warn

</VirtualHost>

