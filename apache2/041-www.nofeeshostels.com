
<VirtualHost *:80>
        ServerName      www.nofeeshostels.com
        ServerAlias     www.nofeeshostels.com nofeeshostels.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/nofeeshostels.com-access.log combined
        ErrorLog /opt/logs/nofeeshostels.com-error.log
        LogLevel warn    

</VirtualHost>


