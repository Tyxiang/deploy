::del /f /a /q dest
::rd /s /q dest
::md dest
cd ..
xcopy src test\dest /Y /E
start msedge  http://localhost:8000/update.php
cd test\dest
php -S localhost:8000
