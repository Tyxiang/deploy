# About

## 1. 概述

- 这是个基于 php 的更新工具；
- 可以配合 GitHub Hook 功能实现简单的自动部署；

## 2. 功能

1. 下载文件到服务器；
1. 清空目标（可配置）；
1. 复制新文件（目录）到目标文件（目录）；

## 3. 应用

### 3.1. 手动部署 github 上的网站到虚拟主机

- 通过浏览器手动发起 http 请求，访问虚拟主机上的 deploy.php；
- 通过 deploy.php 获取 Releases 中的 zip 文件；
- 通过 deploy.php 用 zip 文件中的文件（目录）更新虚拟主机的特定文件（目录）；
- 完成部署；

### 3.2. 自动更新 markdown 内容文件到网站

- 利用 github 的 hook 功能监听库的特定分支；
- 通过特定分支的 push 动作触发 http 请求；
- http 请求访问虚拟主机上的 deploy.php；
- 通过 deploy.php 获取 Download ZIP 中的 zip 文件；
- 通过 deploy.php 用 zip 文件中的文件（目录）更新虚拟主机的特定文件（目录）；
- 完成部署；
