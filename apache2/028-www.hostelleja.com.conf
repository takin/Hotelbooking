
<VirtualHost *:80>
        ServerName      www.hostelleja.com
        ServerAlias     www.hostelleja.com
        ServerAlias     hostelleja.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hostelleja.com-access.log combined
        ErrorLog /opt/logs/hostelleja.com-error.log
        LogLevel warn

</VirtualHost>


