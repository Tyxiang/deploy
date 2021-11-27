xcopy src demo /y
start msedge  http://localhost:8000/deploy.php
cd demo
php -S localhost:8000
