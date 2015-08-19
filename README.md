# 說文解字注CSV
説文解字注をCSVファイル、SQL文、.ods(OpenDocumentスプレッドシート) にしたものです。CSVにした範囲は第一篇〜第十四篇です。  
もとのデータ(swjz.xml)は、以下の「漢字データベースプロジェクト」で公開されているデータ(GPL)です。  
https://github.com/cjkvi/  
http://kanji-database.sourceforge.net/  
  
  
## CSVのフォーマット
　カラムは以下になります。漢字データベースプロジェクトに記されていた属性を使用しています。  
  
    章番号, 文字id, 文字の画像ファイル名(除く拡張子), 文字, この文字を形作る文字, 説文解字注の文章  
  
  
## ファイル
　img/　　　　　文字の画像ファイル  
　swjz.csv　　　CSVファイル。1万行超えます。  
　swjz_all.ods　swjz.csvをodsにしたもの  
　swjz_A.ods　　swjz_all.odsの前半半分  
　swjz_B.ods　　swjz_all.odsの後半半分  

　splitCSV/split.sh　swjz.csvをchapterごとに分割するbashスクリプトです。  
  
　create.sql　　create table SQL文  
　swjz.sql　　　insert SQL文  
  
　swjz2csv.php　swjz.xmlを読み込んで、CSV(SQL)、pngを出力するスクリプト  
  
　swjz.xml　　　オリジナルXMLファイル  

　UnDescribedChars.txt	swjz.xmlに記載されていない文字  
  
  
## 文字画像
「漢字データベースプロジェクト」の記述にしたがって、小篆フォントSW.ttfからshuowen.ttfを作成し、それから画像をつくりました。しかしグリフが定義されていない文字があります。その場合は画像には何も書かれていません。  
  
  
## License
GPLです。元データがGPLで、本データはその派生物になりますので。  
  
  
## フォント
文字が表示されない場合、以下のフォントのどれかをインストールしてみてください。  
　IPAmj明朝	http://mojikiban.ipa.go.jp/1300.html  
　花園明朝A,B	http://osdn.jp/projects/hanazono-font/releases/p12900  
　Google Noto Sans CJK	https://www.google.com/get/noto/help/cjk/  
  
