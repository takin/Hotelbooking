
<VirtualHost *:80>
        ServerName      www.schroniskamlodziezowe.com
        ServerAlias     www.schroniskamlodziezowe.com
        ServerAlias     schroniskamlodziezowe.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/schroniskamlodziezowe.com-access.log combined
        ErrorLog /opt/logs/schroniskamlodziezowe.com-error.log
        LogLevel warn

</VirtualHost>

