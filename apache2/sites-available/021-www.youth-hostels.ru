NameVirtualHost 95.142.168.80:80

<VirtualHost 95.142.168.80:80>
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

NameVirtualHost 95.142.168.80:443

<VirtualHost 95.142.168.80:443>

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

    	SSLEngine on

    	SSLCertificateFile      /opt/certificates/youth-hostels.ru.crt
    	SSLCACertificateFile    /opt/certificates/youth-hostels.ru.pem
    	SSLCertificateKeyFile   /opt/certificates/youth-hostels.ru.key

</VirtualHost>

