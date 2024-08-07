# 入社前課題　掲示板サイト　PHP-bulletin-board

**開発背景**
---
このアプリは、WEB開発の基本的なスキルを実践的に学ぶことを目的として作成しました。HTML,CSS,JavaScript,PHP,MySQLを使用し、シンプルな掲示版機能を実装することで、フロントエンドとバックエンドの連携を理解し、データベース操作の基礎を習得することを目的に作成しました。

**主要機能**
---
<実装済み>
* 投稿の表示
* 新規投稿の作成
* 投稿の編集
* 投稿の削除
* データベース接続の確立
* CRUD操作の実装（Create, Read, Update, Delete）
* MySQLの使用
* 動作環境の構築（MAMPの使用）
* セキュリティ対策（SQLインジェクション対策、XSS対策）（発展チャレンジ）

<未実装>
* レスポンシブデザイン
* フォームのバリデーション
* 適切なテーブル設計
* 効率的なクエリの作成
* テストとデバックの実施
* フレームワークの使用（発展チャレンジ）
* 非同期通信（Ajax）を使用した投稿の送信と更新（発展チャレンジ）
* ユーザー認証システムの実装（発展チャレンジ）
* 画像アップロード機能の追加（発展チャレンジ）
* ページネーション機能の実装（発展チャレンジ）
* 検索機能の追加（発展チャレンジ）

**使用技術**
---
* PHP 8.2.20 (言語)
* MySQL 8.0.35　（データーベース）
* MAMP (ローカル開発環境)
* GitHub (バージョン管理システム)
* VScode　（エディタ）

**感想・反省点**
---
<感想>
この掲示板アプリを作成してみて、まず感じたことは、自分の知識や技術の低さです。今回の掲示板アプリの作成に限ったことではないですが、プログラミングの学習をすればするほど、自分のレベルの低さを実感します。ただ、現在のレベルが低い分、今回の掲示板アプリ作成で多くのことを学ぶことができたのはとても良かったと多います。中でも学習していて特に楽しいと感じた部分は、セキュリティ対策についてです。自分にとってセキュリティ対策の学習をすることは初めてだったので、コードの目的や意味への理解にとても苦労しましたが、その分理解できた時の喜びは大きいものでした。また、それと同時に、セキュリティ対策については、徹底的に学ばなければいけない分野だとも感じました。将来的には、ユーザーが使用する個人情報を扱うためのシステム開発を行うことになると思うのですが、セキュリティ対策の理解を深めなければまず、現場で働くことはできないなと強く感じました。
<反省点>
一番の反省点は、フロントエンドの実装を行えなかった点になります。cssが範囲されず、考えられる解決策を自分でも２日間かけて実施したのですが、解決できませんでした。与えていただいた、掲示板アプリの作成で、レスポンシブデザインの作成や、フォームのバリエーションの追加を行えなかったのは悔しいです。



**参考資料**
---
このアプリは、『ケケンタの独学ITブログの掲示板アプリの作り方』（参考サイトの項目にURLあり）をベースに開発しました。
『ケケンタの独学ITブログの掲示板アプリの作り方』の参考にすることで、今回の課題である、『掲示板の基本機能』と、発展チャレンジである『セキュリティ対策』は実装することができました。
『ケケンタの独学ITブログの掲示板アプリの作り方』で作成したコードのクラス化を行い、その後、PHP公式ドキュメントやその他のサイトを参考にしながら、発展チャレンジの機能の実装を行いました。

* PHP公式ドキュメント
https://www.php.net/manual/ja/index.php
* 掲示板サイト
https://kekenta-it-blog.com/board-app-php/
* 画像アップロード機能
* セキュリティ対策
https://kekenta-it-blog.com/php-board-app-secure/
https://kekenta-it-blog.com/xss-php/
https://kekenta-it-blog.com/csrf-php/
https://kekenta-it-blog.com/sql-inj-php/


