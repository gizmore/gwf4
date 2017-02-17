# gwf4

## gwf4 is the successor of gwf3.

## https://github.com/gizmore/gwf3

## DEMO: https://gwf4.gizmore.org


### What changed?

1. Got rid of "Smarty", a template engine which is not fitting the gwf usecase.
2. Apply a default angular material template.
3. Cleanup the folder structure to make one folder per module.
4. Being way more ajax and modular now.
5. Finally, the MIT License is official for all gwf4 modules on github.


### Install

sudo apt-get install nodejs
sudo npm install -g bower

git clone https://github.com/gizmore/gwf4
cd gwf4
bower install

Point your webserver to the gwf4 folder.
Open localhost/install/wizard.php
Follow the instructions.


### Install modules

cd gwf4/module

git clone <repo> as ModuleName.

It is important you change the repo name to the module name!


### New Modules

- https://github.com/gizmore/gwf4-avatar       Avatar
- https://github.com/gizmore/gwf4-friendlist   Friendlist
- https://github.com/gizmore/gwf4-gallery      Gallery
- https://github.com/gizmore/gwf4-maps         Maps
- https://github.com/gizmore/gwf4-websockets   Websockets


### Ported and free Modules

Core modules included in this repo are: GWF, Login, Register, Language, PasswordForgot and Admin.

- https://github.com/gizmore/gwf4-account          Account
- https://github.com/gizmore/gwf4-contact          Contact
- https://github.com/gizmore/gwf4-downloads        Download
- https://github.com/gizmore/gwf4-payment          Payment
- https://github.com/gizmore/gwf4-payment-paypal   PaymentPaypal
- https://github.com/gizmore/gwf4-pm               PM
- https://github.com/gizmore/gwf4-votes            Votes

### Properitary Modules

- TBA


### Facebook Login

You need to clone the facebook-graph-sdk into the login module.

    cd module/Login
    git clone https://github.com/facebook/php-graph-sdk



### Infrastructure Tutorials


### TLS with acme.sh

##### Install acme.sh

    https://github.com/Neilpang/acme.sh


##### Issue a cert

	sudo acme.sh --issue --domain gwf4.gizmore.org -w ~/gwf4/www/gwf4


##### Convert to often used formats

    sudo cd ~/.acme.sh/gwf4.gizmore.org
	# Create PFX
    openssl pkcs12 -export -out gwf4.gizmore.org.pfx -inkey gwf4.gizmore.org.key -in fullchain.cer -nodes
	# Create chain PEM
    openssl pkcs12 -in gwf4.gizmore.org.pfx -out gwf4.gizmore.org.public.pem -nodes -nokeys


### Apache config

	<VirtualHost *:80>
	        ServerName gwf4.gizmore.org
	        DocumentRoot /home/gwf4/www/gwf4
	        <Directory "/home/gwf4/www/gwf4">
	                Options +Indexes +FollowSymLinks -MultiViews
	                AllowOverride All
	                Require all granted
	        </Directory>
	        AssignUserID gwf4 gwf4
	        ErrorLog /home/gwf4/www/apache.error.log
	        CustomLog /home/gwf4/www/apache.access.log combined
	</VirtualHost>
	
	<VirtualHost *:443>
	        ServerName gwf4.gizmore.org
	        DocumentRoot /home/gwf4/www/gwf4
	        <Directory "/home/gwf4/www/gwf4">
	                Options +Indexes +FollowSymLinks -MultiViews
	                AllowOverride All
	                Require all granted
	        </Directory>
	        AssignUserID gwf4 gwf4
	        ErrorLog /home/gwf4/www/apache.error.log
	        CustomLog /home/gwf4/www/apache.access.log combined
	        SSLProtocol all -SSLv2
	        SSLCipherSuite HIGH:!aNULL:!MD5
	        SSLCertificateFile /root/.acme.sh/gwf4.gizmore.org/gwf4.gizmore.org.cer
	        SSLCertificateKeyFile /root/.acme.sh/gwf4.gizmore.org/gwf4.gizmore.org.key
	</VirtualHost>

	
### Nginx config

	TODO