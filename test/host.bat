del /s /q dest
cd ..
xcopy /y src test/dest 
start msedge  http://localhost:8000/update.php
cd test/dest
php -S localhost:8000
