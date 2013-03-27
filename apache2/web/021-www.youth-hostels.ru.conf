
<VirtualHost *:80>
        ServerName      www.youth-hostels.ru
        ServerAlias     www.youth-hostels.ru
        ServerAlias     youth-hostels.ru
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/youth-hostels.ru-access.log combined
        ErrorLog /opt/logs/youth-hostels.ru-error.log
        LogLevel warn

</VirtualHost>

