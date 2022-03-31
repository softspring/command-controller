# Configure output

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