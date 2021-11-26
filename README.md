# About

## 1. 概述

这是个 php 自动部署方案。

## 2. 功能

1. 下载 zip 文件到服务器；
1. 解压 zip 文件到临时目录；
1. 清除目标目录中的文件；
1. 复制源目录中的文件到目标目录；

## 3. 应用

### 3.1. 部署 github 上的网站到虚拟主机

- 利用 github 的 hook 功能监听库的特定分支；
- 通过特定分支的 push 动作触发 http 请求；
- http 请求访问虚拟主机上的 deploy.php；
- 通过 deploy.php 获取 Download ZIP 或 Releases 中的 zip 文件，完成网站部署；

### 3.2. 更新 markdown 内容文件到网站
