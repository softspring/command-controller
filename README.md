# CommandController

[![Latest Stable Version](https://poser.pugx.org/softspring/command-controller/v/stable.svg)](https://packagist.org/packages/softspring/command-controller)
[![Latest Unstable Version](https://poser.pugx.org/softspring/command-controller/v/unstable.svg)](https://packagist.org/packages/softspring/command-controller)
[![License](https://poser.pugx.org/softspring/command-controller/license.svg)](https://packagist.org/packages/softspring/command-controller)
[![Total Downloads](https://poser.pugx.org/softspring/command-controller/downloads)](https://packagist.org/packages/softspring/command-controller)
[![Build status](https://travis-ci.com/softspring/command-controller.svg?branch=master)](https://app.travis-ci.com/github/softspring/command-controller)

This library allows running commands from Symfony controllers.

## Installation

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require softspring/command-controller
```

## Usage

### Basic usage

A basic command will be like this:

```yaml
# config/routes/commands.yaml
command_cache_clear:
    controller: Softspring\Component\CommandController\Controller\CommandController::run
    path: /_command/cache-clear
    defaults:
        command: cache:clear
```

This route will run as $ bin/console cache:clear

### Command with arguments

If you need to pass arguments, you must add an arguments array in route defaults to 
 set witch arguments are allowed to pass to the command

```yaml
# config/routes/commands.yaml
create_reports:
    controller: Softspring\Component\CommandController\Controller\CommandController::run
    path: /_command/create-report
    defaults:
        command: app:report:create
        report: daily
        arguments: ["report"]
```

This route will run as $ bin/console app:report:create daily

You can also set arguments in route paths:

```yaml
# config/routes/commands.yaml
send_welcome_email:
    controller: Softspring\Component\CommandController\Controller\CommandController::run
    path: /_command/emails/welcome/{to}/send
    defaults:
        command: app:emails:send-welcome
        arguments: ["to"]
```

A /_command/emails/welcome/test@example.com/send request will run as $ bin/console app:emails:send-welcome test@example.com

### Command with options

If you need to set any command option, you must add an options array in route defaults to
set witch options are allowed to pass to the command

```yaml
# config/routes/commands.yaml
create_reports:
    controller: Softspring\Component\CommandController\Controller\CommandController::run
    path: /_command/create-report
    defaults:
        command: app:report:create
        dry-run: true
        options: ["dry-run"]
```

This route will run as $ bin/console app:report:create --dry-run

### Configure output

By default, you will get the output in http response body.

```yaml
# config/routes/commands.yaml
cron_send_emails:
    controller: Softspring\Component\CommandController\Controller\CommandController::run
    path: /_cron/send-emails
    defaults:
        command: swiftmailer:spool:send
```

The HTTP response will look like this:

```
[2000-01-01 00:00:00] Processing default mailer spool...
0 emails sent
```

You can also set a logger to be the output of the command.

```yaml
# config/routes/commands.yaml
cron_send_emails:
    controller: Softspring\Component\CommandController\Controller\CommandController::run
    path: /_cron/send-emails
    defaults:
        command: swiftmailer:spool:send
        loggerOutputService: 'monolog.logger.cron'
```

you will get this output in configured log:

```
[01-Jan-2000 00:00:00] [pool cron] [2000-01-01 00:00:00] Processing default mailer spool... "
[01-Jan-2000 00:00:00] [pool cron] 0 emails sent"
```

Command runner controller returns a StreamedResponse, but if you want to return a normal response you can do it with *stream* option.

```yaml
# config/routes/commands.yaml
cron_send_emails:
    controller: Softspring\Component\CommandController\Controller\CommandController::run
    path: /_cron/send-emails
    defaults:
        command: swiftmailer:spool:send
        stream: false
```

### Security

Important! Above configurations will make public the commands you configure, and could be a security hole in your system.

You must configure any security method you want to ensure that the commands are not public. 

## License

This bundle is under the MIT license. See the complete license in the bundle LICENSE file.