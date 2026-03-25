# Xdebug

Xdebug 3 is pre-installed in the Docker `app` container. Port **9003** (Xdebug 3 default).

## Configuration

**`docker/dev/php/xdebug.ini`**
```ini
xdebug.mode=develop,debug
xdebug.start_with_request=yes
xdebug.client_host=host.docker.internal
xdebug.client_port=9003
xdebug.log_level=0
xdebug.idekey=VSCODE
```

`host.docker.internal` resolves to the host machine via the `extra_hosts` entry in `docker-compose.yml`.

## VS Code

Create `.vscode/launch.json`:

```json
{
  "version": "0.2.0",
  "configurations": [
    {
      "name": "Listen for Xdebug",
      "type": "php",
      "request": "launch",
      "port": 9003,
      "pathMappings": {
        "/var/www": "${workspaceFolder}"
      }
    }
  ]
}
```

Install the [PHP Debug extension](https://marketplace.visualstudio.com/items?itemName=xdebug.php-debug) (`xdebug.php-debug`), then press **F5**.

## PHPStorm

1. **Settings → PHP → Debug** — set debug port to `9003`
2. **Settings → PHP → Servers** — add server:
   - Host: `localhost`, Port: `8000`
   - Path mapping: project root → `/var/www`
3. Click the **Listen for PHP Debug Connections** button (phone icon)

## Coverage Report

```bash
composer test-coverage
# Output: coverage/ directory (HTML report)
```

Requires `XDEBUG_MODE=coverage` (already set in the `test-coverage` composer script).

## Disable in Production

Xdebug is only installed in the dev Docker image. Never enable it in production — it has a significant performance impact.
