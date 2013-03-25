
<VirtualHost *:80>
        ServerName      www.hosteis.com
        ServerAlias     www.hosteis.com
        ServerAlias    hosteis.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hosteis.com-access.log combined
        ErrorLog /opt/logs/hosteis.com-error.log
        LogLevel warn

</VirtualHost>

