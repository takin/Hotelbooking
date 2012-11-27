NameVirtualHost 92.243.23.150:80

<VirtualHost 92.243.23.150:80>
        ServerName      www.youth-hostel.com
        ServerAlias     www.youth-hostel.com
        ServerAlias     youth-hostel.com
        ServerAdmin     technical@mcwebmanagement.com

        DocumentRoot "/opt/web"
	<Directory "/opt/web">
        	Options Indexes FollowSymLinks MultiViews
        	AllowOverride All
        	Order allow,deny
        	Allow from all
	</Directory>

        CustomLog /opt/logs/youth-hostel.com-access.log combined
        ErrorLog /opt/logs/youth-hostel.com-error.log
        LogLevel warn

</VirtualHost>

NameVirtualHost 92.243.23.150:443

<VirtualHost 92.243.23.150:443>

        ServerName      www.youth-hostel.com
        ServerAlias     www.youth-hostel.com
        ServerAlias     youth-hostel.com
        ServerAdmin     technical@mcwebmanagement.com

	DocumentRoot "/opt/web"
        <Directory "/opt/web">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        CustomLog /opt/logs/youth-hostel.com-access.log combined
        ErrorLog /opt/logs/youth-hostel.com-error.log
        LogLevel warn

	SSLEngine on

	SSLCertificateFile      /opt/certificates/youth-hostel.com.crt
	SSLCACertificateFile    /opt/certificates/youth-hostel.com.pem
	SSLCertificateKeyFile   /opt/certificates/youth-hostel.com.key

</VirtualHost>
