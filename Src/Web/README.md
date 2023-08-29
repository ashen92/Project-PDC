# Introduction
This folder contains the source code for the website. 

# Getting Started
To start the development process you can follow the instructions given here.
1.	Setting up the environment
2.  Setting up the project

## 1. Setting up the environment

### Setting up the Apache and PHP
#### Windows

There are multiple ways to set up these. This is a one way to do.

Download Apache Server from [here](https://www.apachelounge.com/download/VS17/binaries/httpd-2.4.57-win64-VS17.zip). Extract the content to `C:\Apache24`

Download PHP from [here](https://windows.php.net/download/). Download the thread safe version. Extract the zip file to 
`C:\php`

Copy `php.ini` file in the `Configs` to `C:\php` folder. Copy `httpd.conf` file to `C:\Apache24\conf` folder.

To automatically start the Apache Server on Windows startup, open Windows PowerShell or Command Prompt and run the 
following command inside the `C:\Apache24\bin` folder.

`httpd.exe -k install`

#### macOS

To activate the pre-installed Apache server run,

`sudo apachectl start`

Then copy the `httpd.conf` file to following location. `/etc/apache2/`. Then following to restart the Apache.

`sudo apachectl restart`

PHP is bundled with macOS in recent versions. No actions needed. Copy `php.ini` to `/etc/`.

### Installing Composer 
#### Windows

Download the setup from [here](https://getcomposer.org/Composer-Setup.exe) and run it.

#### macOS

Open a new terminal and run the following commands.

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"

sudo mv composer.phar /usr/local/bin/composer
```

### Installing MySQL

Download the installer from [here](https://dev.mysql.com/downloads/installer/) and run it accepting the defaults. Make sure to use `root` as the default user 
and password.

### Setting up Visual Studio Code

[Install VSCode](https://code.visualstudio.com/) then install the following extensions
- [PHP from DEVSENSE](https://marketplace.visualstudio.com/items?itemName=DEVSENSE.phptools-vscode)
- [Live Sass Compiler](https://marketplace.visualstudio.com/items?itemName=glenn2223.live-sass)
- [Twig braces helper](https://marketplace.visualstudio.com/items?itemName=zepich.twig-braces-helper)

```
code --install-extension DEVSENSE.phptools-vscode
code --install-extension glenn2223.live-sass
code --install-extension zepich.twig-braces-helper
```

## 2. Setting up the project

Create a folder call `src` in `C:/` or `/`. Open that folder in terminal and clone the repository to it using following command.

```
git clone https://ashenhasanka12@dev.azure.com/ashenhasanka12/GroupProject/_git/GroupProject
```

Open `C:/src/GroupProject` or `/src/GroupProject` folder in VSCode. Then open terminal and in the terminal change the folder to `Src/Web/App` using following command.

`cd ./Src/Web/App`

Then run following command.

`composer install`

This will download and install all the dependencies.

**_NOTE:_** Always run `composer install` in `Src/Web/App` folder. Do not run it inside any other folder.

After that is finished, open the web browser and navigate to `localhost`. The website should successfully load.