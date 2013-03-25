
<VirtualHost *:80>
        ServerName      www.mladeznickeubytovny.com
        ServerAlias     www.mladeznickeubytovny.com
        ServerAlias     mladeznickeubytovny.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/mladeznickeubytovny.com-access.log combined
        ErrorLog /opt/logs/mladeznickeubytovny.com-error.log
        LogLevel warn

</VirtualHost>

