# READ ME

## Init Data

```
php yii migrate/up
```

## Run application

```
php yii serve
```

## Nginx deploy

```
server {
    listen       80;
    server_name  www.supplier.cc;

    root   D:\workspace\php\supplier\web;
    index  index.html index.htm index.php;

    error_page   499 502 503 504  /50x.html;
    location = /49x.html {
        root   html;
    }

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        try_files $uri /index.php =403;
        fastcgi_pass 127.0.0.1:9000;
        # fastcgi_pass php-upstream;
        fastcgi_index index.php;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        #fixes timeouts
        fastcgi_read_timeout 600;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny  all;
    }
}
```

## Supplier action

```
localhost:8080/supplier/index
```
