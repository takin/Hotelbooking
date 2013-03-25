
<VirtualHost *:80>
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

