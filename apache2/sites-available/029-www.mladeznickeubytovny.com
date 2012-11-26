NameVirtualHost 95.142.168.82:80

<VirtualHost 95.142.168.82:80>
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

NameVirtualHost 95.142.168.82:443

<VirtualHost 95.142.168.82:443>

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

    	SSLEngine on

    	SSLCertificateFile      /opt/certificates/mladeznickeubytovny.com.crt
    	SSLCACertificateFile    /opt/certificates/mladeznickeubytovny.com.pem
    	SSLCertificateKeyFile   /opt/certificates/mladeznickeubytovny.com.key

</VirtualHost>

