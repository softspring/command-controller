# Command with arguments

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