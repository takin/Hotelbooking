NameVirtualHost 95.142.168.84:80

<VirtualHost 95.142.168.84:80>
        ServerName      www.hostelek.com
        ServerAlias     www.hostelek.com
        ServerAlias     hostelek.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hostelek.com-access.log combined
        ErrorLog /opt/logs/hostelek.com-error.log
        LogLevel warn

</VirtualHost>

NameVirtualHost 95.142.168.84:443

<VirtualHost 95.142.168.84:443>

        ServerName      www.hostelek.com
        ServerAlias     www.hostelek.com
        ServerAlias     hostelek.com
        ServerAdmin     technical@mcwebmanagement.com
    
        DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/hostelek.com-access.log combined
        ErrorLog /opt/logs/hostelek.com-error.log
        LogLevel warn

    	SSLEngine on

    	SSLCertificateFile      /opt/certificates/hostelek.com.crt
    	SSLCACertificateFile    /opt/certificates/hostelek.com.pem
    	SSLCertificateKeyFile   /opt/certificates/hostelek.com.key

</VirtualHost>

