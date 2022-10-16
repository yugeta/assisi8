
# check
- 2020.10.16 : サーバーでメールを使えるようにする初回設定
  - 確認
  $ alternatives --display mta -> ダメ
  $ apt-get install postfix

  - /var/log/mail.log
  Oct 16 20:56:18 9l8jymj0 postfix/smtp[21204]: 0FD1EC5D5F: to=<yugeta.koji@gmail.com>, relay=gmail-smtp-in.l.google.com[108.177.125.27]:25, delay=1.4, delays=0.07/0.01/0.45/0.91, dsn=5.7.26, status=bounced (host gmail-smtp-in.l.google.com[108.177.125.27] said: 550-5.7.26 This message does not have authentication information or fails to 550-5.7.26 pass authentication checks. To best protect our users from spam, the 550-5.7.26 message has been blocked. Please visit 550-5.7.26  https://support.google.com/mail/answer/81126#authentication for more 550 5.7.26 information. m14si2102435pgh.435 - gsmtp (in reply to end of DATA command))

  

