
<VirtualHost *:80>
        ServerName      www.hostelek.com
        ServerAlias     www.hostelek.com
        ServerAlias     hostelek.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hostelek.com-access.log combined
        ErrorLog /opt/logs/hostelek.com-error.log
        LogLevel warn

</VirtualHost>

