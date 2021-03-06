NicoAnime
====

NicoAnimeはニコニコチャンネルの無料配信動画をアーカイブすることのできるウェブアプリです。

Requirements
----

- Ruby（Version 1.9 もしくはそれ以降）
    - Ruby/MySQL
- PHP（Version 5.3 もしくはそれ以降）
    - DOM
    - PDO (MySQL)
- Apache
    - mod_rewrite
- MySQL

Install
----

いくつかのステップが必要です。

### 必要なファイル郡の設置

~~~~
$ git clone https://github.com/tondol/NicoAnime.git ~/www/nicoanime
$ cd ~/www/nicoanime
$ git submodule update --init
~~~~

### config.ymlの編集

~~~~
$ cp config.yml.sample config.yml
$ vim config.yml
~~~~

MySQLへの接続設定（ホスト名，データベース名，ユーザー名，パスワード），動画のダウンロードに使用するアカウントの設定（メールアドレス，パスワード）などを編集します。

### rtmpdumpの導入

rtmpeプロトコルの動画をダウンロードするため，下記のようにして[rtmpdump](http://rtmpdump.mplayerhq.hu/)を導入します。
rtmpdumpの実行ファイルはパスの通っている場所に配置する必要があります。

~~~~
$ cd ~/tmp
$ git clone git://git.ffmpeg.org/rtmpdump
$ cd ~/tmp/rtmpdump
$ ./configure
$ make
$ sudo make install
~~~~

### httpd.confの編集

&lt;設置したパス&gt;/public をドキュメントルートとして設定します。

~~~~
# 記述例

<VirtualHost *:80>
    DocumentRoot /home/fuga/www/nicoanime/public
    ServerName nicoanime.tondol.com
</VirtualHost>
~~~~

### public/.htaccessの編集

~~~~
$ cd ~/www/nicoanime/public
$ cp .htaccess.sample .htaccess
$ vim .htaccess
~~~~

設定ファイルへのアクセス制限や，ウェブ側UIを動作させるためのmod_rewriteの設定，Content-Typeの追加設定などが記述されています。
必要に応じて，設定を追加してください（.htaccessを使用せずにhttpd.confに記述することももちろん可能です）。
**Basic認証などの方法によりアクセス制限の設定を追加すること** を強くお薦めします。

### 必要なテーブルを作成

データベースに必要なテーブルを作成します。
コマンド例のユーザー名（fuga）やデータベース名（nicoanime）は適宜変更してください。

~~~~
$ cd ~/www/nicoanime
$ mysql -u fuga -p -default-character-set=utf8 nicoanime < INSTALL.sql
~~~~

### crontabの設定

ruby/crawler.rb と ruby/downloader.rb を定期実行するように設定します。

~~~~
$ crontab -e
~~~~

~~~~
# 記述例

SHELL=/bin/bash
HOME=/home/fuga
MAILTO=""

# nicoanime
NICOANIME_DIR=/home/fuga/www/nicoanime/ruby
00 * * * * ruby $NICOANIME_DIR/crawler.rb >> $NICOANIME_DIR/nicoanime.log 2>> $NICOANIME_DIR/error.log
30 * * * * ruby $NICOANIME_DIR/downloader.rb >> $NICOANIME_DIR/nicoanime.log 2>> $NICOANIME_DIR/error.log
~~~~

### パーミッションの設定

chmodコマンドなどにより，phpのスクリプトを実行するユーザーとcrontabを実行するユーザーが，それぞれ動画のダウンロード先パスにファイルを作成・削除できるように設定します。

~~~~
$ chmod o+w ~/www/nicoanime/public/contents
~~~~

HOW TO USE
----

&lt;設置先URL&gt;/help/ をブラウザで閲覧してください。
