del /s /q demo-dest
xcopy src demo-dest /y
start msedge  http://localhost:8000/deploy.php
cd demo-dest
php -S localhost:8000
