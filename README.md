# About

## 1. 概述

这是个 github 库的自动部署方案；

## 2. 思路

- 利用 github 的 hook 功能监听特定分支；
- 通过特定分支的 push 动作触发 http 请求；
- http 请求访问虚拟主机上的 deploy 操作；
- deploy 操作从 github 下载 main.zip 到服务器；
- 服务器解压 main.zip 到特定目录，完成升级；