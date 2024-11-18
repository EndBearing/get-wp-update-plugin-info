<?php
ini_set('display_errors', "On");
// 認証参考
// https://note.com/s_t877/n/n7ce48a6e945f
require 'spreadsheetAPI/composer/vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

// サービスアカウントキーのパス
$serviceAccountFile = '{path to key.json}';
// スプレッドシートID
$spreadsheetId = '{spreadID}';

// Google API クライアントの設定
$client = new Client();
$client->setAuthConfig($serviceAccountFile);
$client->addScope(Sheets::SPREADSHEETS);

$service = new Sheets($client);

// 新しいシートを作成
function createSheet($service, $spreadsheetId, $sheetTitle) {
    $requests = [
        new Sheets\Request([
            'addSheet' => [
                'properties' => [
                    'title' => $sheetTitle
                ]
            ]
        ])
    ];

    $batchUpdateRequest = new Sheets\BatchUpdateSpreadsheetRequest([
        'requests' => $requests
    ]);

    $response = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);

    // 新しく作成されたシートのIDを取得
    $sheetId = $response->getReplies()[0]->getAddSheet()->getProperties()->getSheetId();
    return $sheetId;
}

// データを新しいシートに追加
function appendDataToNewSheet($service, $spreadsheetId, $sheetTitle, $values) {
    // 新しいシートを作成
    createSheet($service, $spreadsheetId, $sheetTitle);

    // データを追加
    $body = new Sheets\ValueRange([
        'values' => $values
    ]);
    $params = [
        'valueInputOption' => 'USER_ENTERED'
    ];
    $range = $sheetTitle;
    $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
}

// データ写す
$data = $result;

// シート名をユニークにするためにタイムスタンプを使用
$sheetTitle = "Sheet_" . date('Ymd_His');

// 新しいシートを作成しデータを登録
try{
    appendDataToNewSheet($service, $spreadsheetId, $sheetTitle, $data);
    echo "新しいシート「".$sheetTitle."」にデータが登録されました。";
}catch( Exception $ex){
    echo "書き込みでエラーが発生しました。";
    var_dump($ex);
}