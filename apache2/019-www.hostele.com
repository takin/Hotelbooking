
<VirtualHost *:80>
        ServerName      www.hostele.com
        ServerAlias     www.hostele.com
        ServerAlias     hostele.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hostele.com-access.log combined
        ErrorLog /opt/logs/hostele.com-error.log
        LogLevel warn

</VirtualHost>

