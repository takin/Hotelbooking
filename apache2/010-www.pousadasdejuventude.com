<VirtualHost *:80>
        ServerName      www.pousadasdejuventude.com
        ServerAlias     www.pousadasdejuventude.com
        ServerAlias     pousadasdejuventude.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/pousadasdejuventude.com-access.log combined
        ErrorLog /opt/logs/pousadasdejuventude.com-error.log
        LogLevel warn
    
</VirtualHost>

