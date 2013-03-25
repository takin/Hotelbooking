<VirtualHost *:80>

    ServerName      www.mcdev1.com
    ServerAlias     mcdev1.com
    ServerAdmin     technical@mcwebmanagement.com

    DocumentRoot "/var/www"
    <Directory "/var/www">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        AuthUserFile /etc/apache2/users.conf
        AuthName "This is a protected area"
        AuthGroupFile /dev/null
        AuthType Basic
        Require valid-user
    </Directory>

    CustomLog /opt/logs/mcdev1.com-access.log combined
    ErrorLog /opt/logs/mcdev1.com-error.log
    LogLevel warn

</VirtualHost>
