# Deployment assistant
[![Build Status](https://travis-ci.org/worksolutions/deployment-assistant.svg?branch=master)](https://travis-ci.org/worksolutions/deployment-assistant)

[Russian](README.ru.md)

Assistant safe deployment on production server.


## Install

Assistant is an executable phar-archive.
To install, enter the command:

```bash
php -r "copy('http://dep.worksolutions.ru/dep.phar', 'dep.phar');"
chmod +x ./dep.phar
```

Assistant phar-archive is downloaded and saved in your current directory.

Assistant supports php versions from 5.3 to 7.1


## Usage

### Deploy

Deployment command will check the state of the production and remote branches.
After a successful check, you will receive the changes from the remote branch to the production.


Syntax for the deployment command is:

```bash
php ./dep.phar deploy [<remote>] [<branch>]
```

To start deploy, type:

```bash
php ./dep.phar deploy
```

By default, changes will pull from the remote branch `origin/master`, 
to change it is required to specify from which branch of the remote source 
you need to pull the changes:

```bash
php ./dep.phar deploy origin master
```

### Update

Update command will check if there is a new version of the helper and, 
if there is one, will update it.

```bash
php ./dep.phar self-update
```

## Contributing

To make a contribution to the assistant - send pool-requests.
