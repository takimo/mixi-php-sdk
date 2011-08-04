<?php // レイアウトテンプレート名をテンプレート中で指定する場合 ?>
<?php //$_context['_layout'] = 'mylayout.php'; ?>
<?php // レイアウトで使用する変数をテンプレート中で指定する場合 ?>
<?php $_context['title'] = 'レイアウトのサンプル'; ?>
<table>
<?php foreach ($list as $i=>$item): ?>
  <tr bgcolor="#{$i % 2 ? '#FFCCCC' : '#CCCCFF'}">
    <td&gt;#{$i}</td&gt;
    <td&gt;%{$item}</td&gt;
  </tr>
<?php endforeach ?>
</table>
