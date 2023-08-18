# Introduction
This folder contains the source code for the website. 

# Getting Started
To start the development process you can follow the instructions given here.
1.	Setting up the enviroment

## 1. Setting up the enviroment

### Setting up the Apache and PHP

There are multiple ways to setup these. This is a one way to do.

Download Apache Server from [here](https://www.apachelounge.com/download/VS17/binaries/httpd-2.4.57-win64-VS17.zip). Extract the content to `C:\Apache24`

Download PHP from [here](https://windows.php.net/downloads/releases/php-8.2.8-Win32-vs16-x64.zip). Extract the content to `C:\php`

Copy `php.ini` file in the `Configs` to `C:\php` folder. Copy `httpd.conf` file to `C:\Apache24\conf` folder.

To automatically start the Apache Server on Windows startup, open Windows PowerShell or Command Prompt and run the following command inside the `C:\Apache24\` folder.

`httpd.exe -k install`

### Installing MySQL

Download the installer from [here](https://dev.mysql.com/downloads/installer/) and run it accepting the defaults. Make sure to use `root` as the default user and password.

### Setting up Visual Studio Code

[Install VSCode](https://code.visualstudio.com/) then install the following extensions
- [PHP from DEVSENSE](https://marketplace.visualstudio.com/items?itemName=DEVSENSE.phptools-vscode)
- [Live Sass Compiler](https://marketplace.visualstudio.com/items?itemName=glenn2223.live-sass)
- [Twig braces helper](https://marketplace.visualstudio.com/items?itemName=zepich.twig-braces-helper)
- [YAML](https://marketplace.visualstudio.com/items?itemName=redhat.vscode-yaml)

`code --install-extension DEVSENSE.phptools-vscode`

`code --install-extension glenn2223.live-sass`

`code --install-extension zepich.twig-braces-helper`

`code --install-extension redhat.vscode-yaml`

Clone this whole repository to `C:\src` using `git clone https://ashenhasanka12@dev.azure.com/ashenhasanka12/GroupProject/_git/GroupProject`

Open `C:\src\GroupProject` folder in VSCode to start developing.
