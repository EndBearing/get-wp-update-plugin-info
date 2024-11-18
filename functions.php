<?php
// 公式API ドキュメント
// https://codex.wordpress.org/WordPress.org_API
// 参考 
// https://code.tutsplus.com/communicating-with-the-wordpressorg-plugin-api--wp-33069t


// プラグイン取得API
function get_plugin_update_info($slug) {
    $url = "https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]=" . urlencode($slug);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL検証を無効化（デバッグ用）
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false || empty($response)) {
        $array["error"] = $error;
        return $array;
    }

    $plugin_info = json_decode($response, true);
    if (empty($plugin_info)) {
        $array['error'] = "APIから空のレスポンスが返されました。";
        return $array;
    }

    return $plugin_info;
}


/**
 * 配列をCSVファイルとして出力する関数
 *
 * @param array $data 配列データ
 * @param string $filename 出力するファイル名
 */
function arrayToCsvFile(array $data, string $filename = 'output.csv') {
    // 出力をバッファリング
    ob_start();

    foreach ($data as $row) {
        // 各値をダブルクォーテーションで囲み、カンマで連結
        $csvRow = array_map(function ($value) {
            // 値内のダブルクォーテーションをエスケープ
            $escapedValue = str_replace('"', '""', $value);
            return '"' . $escapedValue . '"';
        }, $row);

        // 行を出力
        echo implode(',', $csvRow) . "\n";
    }

    // バッファ内容を取得
    $csvContent = ob_get_clean();

    // ヘッダーの設定（UTF-8 BOM付き）
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo "\xEF\xBB\xBF"; // UTF-8 BOMを出力（Excel対応）
    echo $csvContent;
}