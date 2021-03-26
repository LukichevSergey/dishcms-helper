location = /robots.txt {
    if ($http_host ~* "\.dishman\.ru$") {
        root /etc/apache2/files/dishman.ru;
        break;
    }
    try_files $uri /robots.txt /site.robots.txt;
}
