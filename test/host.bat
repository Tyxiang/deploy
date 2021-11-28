del /s /q dest
cd ..
xcopy src test\dest /Y /E
start msedge  http://localhost:8000/updater/update.php
cd test\dest
php -S localhost:8000
