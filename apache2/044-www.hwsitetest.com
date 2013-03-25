<VirtualHost *:80>

    ServerName      www.hwsitetest.com
    ServerAlias     hwsitetest.com
    ServerAdmin     technical@mcwebmanagement.com

    DocumentRoot "/opt/web"
    <Directory "/opt/web">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        AuthUserFile /etc/apache2/users.conf
        AuthName "This is a protected area"
        AuthGroupFile /dev/null
        AuthType Basic
        Require valid-user
    </Directory>

    CustomLog /opt/logs/hwsitetest.com-access.log combined
    ErrorLog /opt/logs/hwsitetest.com-error.log
    LogLevel warn

</VirtualHost>

