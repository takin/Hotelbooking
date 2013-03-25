
<VirtualHost *:80>
        ServerName      www.hostely.com
        ServerAlias     www.hostely.com
        ServerAlias     hostely.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hostely.com-access.log combined
        ErrorLog /opt/logs/hostely.com-error.log
        LogLevel warn

</VirtualHost>

