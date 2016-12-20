CommandSupervisorBundle
=======================

A simple tool for supervising your automatically executed commands.


Requirements
------------
- Symfony >= 2.8
- PHP >= 5.6


Installation
------------
Require the bundle with the composer:
```
composer require davidkmenta/command-supervisor-bundle
```


Enable the bundle in the kernel:
```php
<?php
// app/AppKernel.php
 
public function registerBundles()
{
    $bundles = [
        // ...
        new DavidKmenta\CommandSupervisorBundle\CommandSupervisorBundle(),
        // ...
    ];
}
```

Add configuration to your config.yml:
```yml
command_supervisor:
    cache_path: "%kernel.root_dir%/var/supervisor/"
    default_handler: "command_supervisor.handler.swift_mailer_handler"
    commands:
        - { name: "ftp:download:distraints", threshold: 60 }
        - { name: "command:supervisor:status", threshold: 5, handler: "my_handler" }
    handlers:
        my_handler: "handler_service_id"
```

| Parameter | Description |
|-----------|-------------|
| *cache_path* | where supervisor's files should be stored |
| *default_handler* (optional) | a default handler for the supervised commands without a custom handler |
| *commands* | list of supervised commands (see below) |
| *handlers* (optional) | list of custom handlers that can be used for supervised commands (a name followed by the service id) |


Definition of the supervised commands:

| Parameter | Description |
|-----------|-------------|
| *name* | the name of the supervised command |
| *threshold* | last successful run of the command in seconds |
| *handler* (optional) | name of a custom handler for a specific command |


Usage
-----
There are two commands you may execute

| Command | Description |
|---------|-------------|
| `command-supervisor:status` | Shows current status of the supervised commands in the console. This command DOES NOT call the handlers. |
| `command-supervisor:supervise` | Should be executed in a short interval by the Cron. This command DOES call the handlers. |


Documentation
-------------
CommandSupervisorBundle provides an interface for the custom handlers
```php
<?php

namespace DavidKmenta\CommandSupervisorBundle\Handler;

use DavidKmenta\CommandSupervisorBundle\Entity\CommandStatus;

interface UnsuccessfulCommandHandlerInterface
{
    public function handle(CommandStatus $commandStatus);
}

```

Any custom handler implementing this interface can be used in the configuration as the default handler
or as a custom handler for a specific command.

The `CommandStatus` entity contains useful information about last run of the supervised command and the current status.


License
-------
MIT


Contributing
------------
Any contribution is welcomed :-)
