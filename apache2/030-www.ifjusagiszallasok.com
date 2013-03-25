
<VirtualHost *:80>
        ServerName      www.ifjusagiszallasok.com
        ServerAlias     www.ifjusagiszallasok.com
        ServerAlias     ifjusagiszallasok.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/ifjusagiszallasok.com-access.log combined
        ErrorLog /opt/logs/ifjusagiszallasok.com-error.log
        LogLevel warn 

</VirtualHost>

