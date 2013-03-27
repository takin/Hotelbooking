
<VirtualHost *:80>
        ServerName      www.ostellidellagioventu.com
        ServerAlias     www.ostellidellagioventu.com
        ServerAlias     ostellidellagioventu.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/ostellidellagioventu.com-access.log combined
        ErrorLog /opt/logs/ostellidellagioventu.com-error.log
        LogLevel warn

</VirtualHost>

