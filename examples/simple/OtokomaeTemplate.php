<?php
/*
 *  OtokomaeTemplate.php -- レイアウトテンプレートに対応した90行のテンプレートエンジン
 *
 *  - レイアウトテンプレート中で echo $_content; とすると中身が表示される。
 *  - テンプレート中で設定した変数をレイアウトテンプレートで使うことが可能。
 *  - レイアウトテンプレート名をテンプレート側で指定することも可能。
 *  - 使い方：
 *      require_once('OtokomaeTemplate.php');
 *      $TEMPLATE_DIR    = 'templates';  // 省略可、パーミッションに注意
 *      $LAYOUT_TEMPLATE = 'layout.php'; // 省略可
 *      $context = array('title'=>'Example',
 *                       'list'=>array(10,'<A&amp;B>',NULL));
 *      include_template('template.php', $context);
 *  - 要 PHP 5.1 or later
 *  - ライセンス: public domain （自由に改造してね）
 */

/*
 *  設定用のグローバル変数
 */
$TEMPLATE_DIR    = NULL;   /* テンプレートを探すディレクトリ */
$LAYOUT_TEMPLATE = NULL;   /* レイアウトテンプレートのファイル名 */

/*
 *  テンプレートを読み込んで実行する。
 *  $_context は変数名をキー、値を要素とする連想配列。
 *  $_layout はレイアウトテンプレートのファイル名。
 *  - NULL または省略した場合は $LAYOUT_TEMPLATE を使う。
 *  - FALSE ならレイアウトテンプレートを使わない。
 *  - $_context['_layout'] = '...'; とすればテンプレート側でも指定可能。
 */
function include_template($_filename, $_context, $_layout=NULL) {
    global $LAYOUT_TEMPLATE;
    $_content = render_template($_filename, $_context);
    if (@$_context['_layout'] !== NULL)   // テンプレート側で指定された場合は
        $_layout = $_context['_layout'];  // それを使う。
    elseif ($_layout === NULL)            // 引数で指定されなかった場合は
        $_layout = $LAYOUT_TEMPLATE;      // デフォルトのファイル名を使う。
    if ($_layout) {
        $_context['_content'] = $_content;  // レイアウトテンプレート中で使う変数
        $_content = render_template($_layout, $_context);
    }
    echo $_content;   // or return $_content;
}

/*
 *  テンプレートを読み込んで実行し、その結果を文字列で返す。
 *  include_template() の実体。
 */
function render_template($_filename, &amp;$_context) {
    $_cachename = convert_template($_filename);
    extract($_context);     // 連想配列をローカル変数に展開
    ob_start();
    include($_cachename);   // テンプレートを読み込んで実行
    return ob_get_clean();
}

/*
 *  テンプレートファイルを読み込み、convert_string() で置換してから
 *  キャッシュファイルに書き込む。読み込み時のロックは省略。
 *  (file_get_contents() もファイルロックできるようにしてほしいなあ。)
 */
function convert_template($filename) {
    global $TEMPLATE_DIR;
    if (! file_exists($filename) &amp;&amp; $TEMPLATE_DIR)
        $filename = "$TEMPLATE_DIR/$filename";
    $cachename = $filename . '.cache';
    if (! file_exists($cachename) || filemtime($cachename) < filemtime($filename)) {
        $s = file_get_contents($filename);
        $s = convert_string($s);
        file_put_contents($cachename, $s, LOCK_EX); // LOCK_EX サポートは 5.1.0 から
    }
    return $cachename;
}

/*
 *  テンプレートの中身を置換する。
 *  - '#{...}' を 'echo ...;' に置換
 *  - '%{...}' を 'echo htmlspecialchars(...);' に置換
 *  - ついでにXML宣言も置換
 */
function convert_string($s) {
    $s = preg_replace('/^<\?xml/', '<<?php ?>?xml', $s);
    $s = preg_replace('/#\{(.*?)\}/', '<?php echo $1; ?>', $s);
    $s = preg_replace('/%\{(.*?)\}/', '<?php echo htmlspecialchars($1); ?>', $s);
    return $s;
}
?>
