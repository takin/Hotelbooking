NameVirtualHost 95.142.170.14:80

<VirtualHost 95.142.170.14:80>
        ServerName      www.alberguesdajuventude.com
        ServerAlias     www.alberguesdajuventude.com
        ServerAlias     alberguesdajuventude.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/alberguesdajuventude.com-access.log combined
        ErrorLog /opt/logs/alberguesdajuventude.com-error.log
        LogLevel warn

</VirtualHost>

NameVirtualHost 95.142.170.14:443

<VirtualHost 95.142.170.14:443>

        ServerName      www.alberguesdajuventude.com
        ServerAlias     www.alberguesdajuventude.com
        ServerAlias     alberguesdajuventude.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/alberguesdajuventude.com-access.log combined
        ErrorLog /opt/logs/alberguesdajuventude.com-error.log
        LogLevel warn

	SSLEngine on

	SSLCertificateFile      /opt/certificates/alberguesdajuventude.com.crt
	SSLCACertificateFile    /opt/certificates/alberguesdajuventude.com.pem
	SSLCertificateKeyFile   /opt/certificates/alberguesdajuventude.com.key

</VirtualHost>

