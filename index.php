<?php
ini_set('display_errors', "On");
include_once('functions.php');
include_once('config.php');


$result = array();
// ヘッダー追加
$result[] = array(
    "No",
    "プラグイン名",
    "スラッグ",
    "現在のバージョン",
    "一つ前のバージョン",
    "最終更新日",
    "必須WPバージョン",
    "検証済みWPバージョン",
    "必須PHPバージョン",
    "プラグインページURL(日本語)",
    "プラグインページURL(英語)",
);

foreach($configs as $i => $config){
    if(!$config["flg_check"]){ //フラグありでスキップ
        continue;
    }

    $info = get_plugin_update_info($config['slug']);

    if(empty($info)){ // 想定外でスキップ
        continue;
    }

    // エラーがある場合
    if(isset($info['error'])){
        $result[] = array( //格納
            $i,
            $config['name'],
            $config['slug'],
            $info['error'],
            "",
            "",
            "",
            "",
            "",
            'https://ja.wordpress.org/plugins/'.$config['slug'],
            'https://wordpress.org/plugins/'.$config['slug'],
        );
    }else{

        // 一つ前のバージョンを取得 
        $keys = array_keys($info["versions"]);
        $keyIndex = array_search($info['version'], $keys);
        $previous_version = "";
        if($keyIndex > 0){
            $previous_version = $keys[$keyIndex-1];
        }

        $result[] = array( //格納
            $i,
            $info['name'],
            $info['slug'],
            $info['version'],
            $previous_version,
            $info['last_updated'],
            $info['requires'],
            $info['tested'],
            $info['requires_php'],
            'https://ja.wordpress.org/plugins/'.$config['slug'],
            'https://wordpress.org/plugins/'.$config['slug'],
        );
    }
}

// CSVとしてダウンロードする場合
include_once('csv.php');
arrayToCsvFile($result, 'sample.csv');

// スプシに書き込み
// include_once("spreadsheetAPI/api.php");
