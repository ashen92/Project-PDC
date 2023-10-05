# Introduction

This folder contains the source code for the website.

## Getting Started

To start the development process you can follow the instructions given here.

## Windows

There are multiple ways to do this. This is the recommended way.

Download Apache Server from [here](https://www.apachelounge.com/download/VS17/binaries/httpd-2.4.57-win64-VS17.zip). Extract the content to `C:\Apache24`.
Download PHP thread safe version from [here](https://windows.php.net/downloads/releases/php-8.1.23-Win32-vs16-x64.zip). Extract the zip file to
`C:\php`

Copy `php.ini` file in the `Configs` folder to `C:\php` folder. Copy `httpd.conf` file to `C:\Apache24\conf` folder.

To automatically start the Apache Server on Windows startup, open Windows PowerShell or Command Prompt in administrator mode and run the
following command inside the `C:\Apache24\bin` folder.

``` cmd
httpd.exe -k install
```

To install Composer download the setup from [here](https://getcomposer.org/Composer-Setup.exe) and run it.

To install MySQL download the installer from [here](https://dev.mysql.com/downloads/installer/) and run it accepting the defaults. Make sure to use `root` as the default user
and password.

### Installing PHP Extensions

Type the following command in terminal and copy its output.

``` cmd
php -i
```

Go to [this](https://xdebug.org/wizard) website and paste it in the text box area and click `Analyse my phpinfo() output`. In the next page there is a file similar to `php_xdebug-3.2.2-8.1-vs16-x86_64.dll`, download it and copy it to `C:\php\ext` folder and rename it to `php_xdebug.dll`

Then go to [this link](https://windows.php.net/downloads/pecl/releases/apcu/5.1.21/php_apcu-5.1.21-8.1-ts-vs16-x64.zip). In the zip file that is downloaded, copy the file `php_apcu.dll` and paste it to `C:\php\ext` folder.

Then restart the Apache Server by searching `services` in windows search and selecting the apache server service.

If you haven't already, download and install Visual Studio Code.

### Setting up the Project

Create a folder called `src` in `C:/`. Open that folder in terminal and clone the repository to it using following command.

``` cmd
git clone https://ashenhasanka12@dev.azure.com/ashenhasanka12/GroupProject/_git/GroupProject
```

In vscode select `open workspace from file` and open the following file. `C:/src/GroupProject/Src/Web/App/.vscode/App.code-workspace`. Install all the recommended extensions.

Open terminal inside vscode and run the following command.

``` cmd
composer install
```

This will download and install all the dependencies.

**_NOTE:_** Always run `composer install` in `GroupProject/Src/Web/App` folder. Do not run it inside any other folder.

After that is finished, open the web browser and navigate to `localhost`. The website should successfully load.

## macOS

Install HomeBrew package manager using following commands.

``` bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

xcode-select --install
```

Install Apache and PHP using following commands.

``` bash
brew update

# Apache
sudo apachectl stop
sudo launchctl unload -w /System/Library/LaunchDaemons/org.apache.httpd.plist 2>/dev/null
brew install httpd
sudo apachectl start
sudo brew services start httpd

# PHP
brew unlink php
brew install php@8.1
brew link --force --overwrite php@8.1
brew install php-apcu
brew install php-xdebug
brew services restart php
```

`todo` config and ini

### To install Composer

Open a new terminal and run the following commands.

``` bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"

sudo mv composer.phar /usr/local/bin/composer
```

### Installing MySQL

Download the installer from [here](https://dev.mysql.com/downloads/installer/) and run it accepting the defaults. Make sure to use `root` as the default user
and password.

### Setting up the project

Create a folder call `src` in `todo`. Open that folder in terminal and clone the repository to it using following command.

``` bash
git clone https://ashenhasanka12@dev.azure.com/ashenhasanka12/GroupProject/_git/GroupProject
```

In vscode select open workspace from file and open this file. `GroupProject/Src/Web/App/.vscode/App.code-workspace`. Install all the recommended extensions.

Open terminal inside vscode and run the following command.

``` bash
composer install
```

This will download and install all the dependencies.

**_NOTE:_** Always run `composer install` in `GroupProject/Src/Web/App` folder. Do not run it inside any other folder.

After that is finished, open the web browser and navigate to `localhost`. The website should successfully load.
