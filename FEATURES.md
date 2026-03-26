# Command Controller Features

Functional definition for `softspring/command-controller`.

This file defines the expected behavior and functional scope of the component. It describes what the component must provide to Symfony applications that expose command execution through HTTP.

## Purpose

- Provide a controlled way to execute Symfony console commands from controllers.
- Let applications expose operational actions through HTTP when a full queue, worker, or manual shell access is not the right fit.
- Support both streamed and buffered command output.

## Main Features

- Provide a controller entry point that receives a command name.
- Allow applications to whitelist request attributes and query parameters as command arguments.
- Allow applications to whitelist request attributes and query parameters as command options.
- Support streamed plain-text responses for long-running commands.
- Support buffered plain-text responses for short commands.
- Support optional command output logging through a logger service.
- Provide output adapters for streamed output and logger-based output.

## Expected Usage

- Use the controller only for explicitly allowed commands.
- Pass a command name from routing or controller configuration.
- Declare which request values can become command arguments.
- Declare which request values can become command options.
- Choose streamed output for long-running tasks and buffered output for short tasks.

## Integration Expectations

- The component should integrate with Symfony `Request` and `Response`.
- It should compose well with controller routes defined in application config.
- It should work with Symfony console commands already present in the application.
- It should allow applications to attach a logger when command output must be persisted or monitored.

## Extension Expectations

- Applications should be able to extend the controller or wrap it with their own access control rules.
- Applications should be able to replace output handling with their own logging or streaming strategy.
- Applications should be able to build higher-level controllers on top of the provided runners.

## Current Limits

- Running commands over HTTP is an operational shortcut and should be protected carefully.
- The component assumes an application kernel is available for the command runners.
- It is most suitable for controlled internal endpoints, admin tools, or maintenance actions, not for public user-facing workflows.
