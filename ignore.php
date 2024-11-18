<?php
ini_set('display_errors', "On");

// ingnore設定のファイルを出力する
include_once "config.php";
?>
<h3>ignore_list</h3>
<ul>
<?php
foreach($configs as $row):
    if(!$row["flg_check"]):
?>
    <li><?php echo htmlspecialchars($row['name']);?></li>
<?php
    endif;
endforeach;
?>
</ul>
