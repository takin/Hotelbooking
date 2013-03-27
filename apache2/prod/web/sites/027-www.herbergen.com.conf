
<VirtualHost *:80>
        ServerName      www.herbergen.com
        ServerAlias     www.herbergen.com
        ServerAlias     herbergen.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hebergen.com-access.log combined
        ErrorLog /opt/logs/hebergen.com-error.log
        LogLevel warn

</VirtualHost>

