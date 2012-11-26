NameVirtualHost 95.142.168.83:80

<VirtualHost 95.142.168.83:80>
        ServerName      www.ifjusagiszallasok.com
        ServerAlias     www.ifjusagiszallasok.com
        ServerAlias     ifjusagiszallasok.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/ifjusagiszallasok.com-access.log combined
        ErrorLog /opt/logs/ifjusagiszallasok.com-error.log
        LogLevel warn 

</VirtualHost>

NameVirtualHost 95.142.168.83:443

<VirtualHost 95.142.168.83:443>

        ServerName      www.ifjusagiszallasok.com
        ServerAlias     www.ifjusagiszallasok.com
        ServerAlias     ifjusagiszallasok.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/ifjusagiszallasok.com-access.log combined
        ErrorLog /opt/logs/ifjusagiszallasok.com-error.log
        LogLevel warn
   
    	SSLEngine on

    	SSLCertificateFile      /opt/certificates/ifjusagiszallasok.com.crt
    	SSLCACertificateFile    /opt/certificates/ifjusagiszallasok.com.pem
    	SSLCertificateKeyFile   /opt/certificates/ifjusagiszallasok.com.key

</VirtualHost>

