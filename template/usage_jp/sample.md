# [Webページをつくる方法](howtomakeweb.md)


## [このページのソースをみる](https://ist.mns.kyutech.ac.jp/swweb/miura/_edit/src.php?file=sample.md&key=7cce010dc015a937e6841b4e289c7b7d)

上部メニューを修正するには，`navigation.md` を編集します．

YOURSITE の文字列は，自由に変更してください．
- [navigation.mdの説明](http://dynalon.github.io/mdwiki/#!quickstart.md)

<!-- HTMLのコメント表記で，表示されないメモや覚書きをうめこむことができます -->

`config.json` を修正すると，タイトルと，右下の表記がかわります．
また，左側のメニュー（見出し２に対応）を消すこともできます．

# 見出し１

## 見出し２

### 見出し３

warning: 黄色い背景

hint: 緑の背景

note: 水色


文章の途中で，**強調したい** ところは＊＊強調したい＊＊と書きます．
または，_イタリックで強調したい_ というように，アンダーバーで囲みます．
`バッククォートで囲んで` 強調することもできます．

### 見出しで**強調したい** 

### 見出しで_イタリックで強調_したい

行のはじめから書くと，こうなります．
行のはじめから書くと，こうなります．
行のはじめから書くと，こうなります．

```
プログラムなど，改行を反映して
読み込ませたいときは
３つのバッククォートの行で
囲みます
`ただし`，**強調はできません**．
```

# HTMLの文法(CSSのスタイル指定)で書くと，スタイルをもっと自由に適用できます．

<h2 style="background: #fea;">スタイルを指定したH2</h2>

<h2 style="background: #afc; border: 5px dotted #8c7; padding: 10px;">ボーダーやパディングスタイルを指定したH2</h2>

<h2 style="background: #acf; border: 5px solid #87c; padding: 10px; border-radius: 16px;">角丸スタイルを指定したH2</h2>

文章のなかで，<span style="background: #fac; padding: 5px; margin: 3px; border: 2px solid #a79;">部分的にオリジナルのスタイル</span>を適用することもできます．

-----

tip: スタイルシート `style.css` で設定すると，クラスを設定し，統一的にスタイルを変更できます．
(MDWikiの要である index.html のヘッダ部分に，独自のstyle.css を読み込む設定を追加した)

独自スタイル適用の <span class="pink">テスト</span>

<h3 class="kakumaru">角丸のH3</h3>

<h1 class="grad">[CSS3 GENERATOR](http://www.css3.me/)で，グラデーションをつけたスタイルを作成し，適用してみました．</h1>

[gimmick:TwitterFollow](@miuramo)

