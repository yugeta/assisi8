

# Docker

init.sh
```
#!/bin/bash

#`source ~/.nvm/nvm.sh`
`sh ~/.nvm/nvm.sh`

/usr/sbin/service nginx start
/usr/sbin/service php7.0-fpm start
/usr/sbin/service postfix start
#/usr/sbin/service mysql start

export LANG=ja_JP.UTF-8

while true ; do
  /bin/bash
done

exit 0
```


## Centos7インストール履歴

参考:
- nginx
  https://qiita.com/MuuKojima/items/afc0ad8309ba9c5ed5ee
  $ vi /etc/yum.repos.d/nginx.repo
  ```
[nginx]
name=nginx repo
baseurl=http://nginx.org/packages/mainline/centos/7/$basearch/
gpgcheck=0
enabled=1
  ```
  $ yum install -y nginx
  $ systemctl enable nginx
  $ systemctl start nginx
  

- wget
  yum install -y wget


- php
  https://qiita.com/inakadegaebal/items/b57cf10339978d638305

- postfix


# Mac内でのモジュール対応
- php
## インストール
...

## 起動
$ php -S 0.0.0.0:80 -t ~/web/


- node
# インストール
参考 : https://qiita.com/mame_daifuku/items/373daf5f49ee585ea498
      https://www.webdesignleaves.com/pr/jquery/node_installation_mac.html
homebrew
nodebrew
node
$ brew install nodebrew
$ nodebrew setup
再インストールの時 : $ brew reinstall nodebrew
$ echo 'export PATH=$HOME/.nodebrew/current/bin:$PATH' >> ~/.bash_profile
$ source ~/.bash_profile

$ nodebrew install-binary [latest , stable]

# 起動
$ nodebrew use [ver..]

# 確認
$ nodebrew ls