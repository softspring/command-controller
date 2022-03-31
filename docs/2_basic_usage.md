# Basic usage

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
