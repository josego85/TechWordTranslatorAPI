# Nginx

Dev config: `docker/dev/nginx/default.conf`. Nginx runs as a separate container on port **8000**.

## Configuration

```nginx
server {
    listen 80;
    index index.php index.html;
    root /var/www/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

**Key points:**
- `try_files` routes all non-file requests to `index.php` (standard Laravel)
- `fastcgi_pass app:9000` — PHP-FPM in the `app` Docker service
- `.ht` files are denied (security)

## Ports

| Host | Container |
|------|-----------|
| 8000 | 80        |

Access the API at `http://localhost:8000`.

## SSL / Production

This config is for **local development only**. In production:
- Terminate SSL at a load balancer or a separate Nginx instance
- Add `Strict-Transport-Security` header (already sent by the `SecurityHeaders` middleware when on HTTPS)
- Set `APP_URL` to the HTTPS domain
