# About

> [简体中文](doc/README-cn.md)

## 1. Overview

- This is an update tool designed for php;
- It can cooperate with GitHub Hook function to realize simple automatic deployment;

## 2. Function

1. Download the zip file to the server;
1. Automatic unzip;
1. Clear the target file (directory);
1. Copy the new file (directory) to the target file (directory);
1. The file (directory) to be protected can be set;

## 3. Application

### 3.1. Manually deploy the website on github to the virtual host

- Configure `deploy.json` in the same directory as `deploy.php`;
- Visit the `deploy.php` on the virtual host through a browser;
- Complete the deployment;

### 3.2. Automatically update markdown content files to the website

- Configure `deploy.json` in the same directory as `deploy.php`;
- Use the hook function of github to monitor specific branches of the source code library;
- Set the hook's push event to trigger an http request to access the `deploy.php` on the virtual host;
- Complete the deployment;

## 4. Others

- When copying, existing files will be overwritten;
- Chinese file names may not be deleted;