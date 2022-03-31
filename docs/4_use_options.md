# Command with options

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