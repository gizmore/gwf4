# gwf4

## gwf4 is the successor of gwf3.

## https://github.com/gizmore/gwf3


### What changed?

1. Got rid of Smarty template engine. It was not fitting the gwf usecase.
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

- https://github.com/gizmore/gwf4-maps         Maps
- https://github.com/gizmore/gwf4-websockets   Websockets
- https://github.com/gizmore/gwf4-avatar       Avatar
- https://github.com/gizmore/gwf4-friendlist   Friendlist


### Ported Modules

Core modules included in this repo are: GWF, Login, Register, Language, PasswordForgot and Admin.

- https://github.com/gizmore/gwf4-pm               PM
- https://github.com/gizmore/gwf4-downloads        Download
- https://github.com/gizmore/gwf4-votes            Votes
- https://github.com/gizmore/gwf4-Payment          Payment
- https://github.com/gizmore/gwf4-PaymentPaypal    PaymentPaypal

