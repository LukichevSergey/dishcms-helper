--------------------------------
Инструкция по установке заглушки
--------------------------------

1) Скопировать папку images и файл blank-flange.html в корневую папку
2) В файле .htaccess 
2.1) После строки:
RewriteEngine on

Вставить строки: 

RewriteCond %{REQUEST_FILENAME} !(logo-kontur.jpg)
RewriteRule ^(.*)$ blank-flange.html

2.2) Закомментарить все остальные строки содержащие 
RewriteCond
RewriteRule

Например:

#RewriteCond %{REQUEST_FILENAME} !-f
#RewriteCond %{REQUEST_FILENAME} !-d

# otherwise forward it to index.php
#RewriteRule . index.php