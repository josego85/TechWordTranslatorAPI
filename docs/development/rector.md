# Rector

Rector is a PHP reconstructor tool that helps with instant upgrades and automated refactoring of your code. In this project, it is used to maintain code quality and apply modern PHP features automatically.

## Usage

You can run Rector to analyze and refactor the codebase. The paths to be processed are already defined in the `rector.php` configuration file, so you don't need to specify them in the command line.

### Dry Run

To see what changes Rector would make without actually applying them, run a dry run. This command will show you a diff of the proposed changes.

```bash
vendor/bin/rector process --dry-run
```

### Applying Changes

To apply the changes directly to the files, run the `process` command without the `--dry-run` option:

```bash
vendor/bin/rector process
```

> **Note:** It's good practice to run a dry run first and review the changes before applying them. Always make sure your code is under version control before running Rector.

For more advanced usage and creating custom rules, refer to the official Rector documentation.