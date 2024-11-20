<?php
/*غير الحقوق واثبت انك فاشل
اذا تريد تنقل اذكر اسمي او اسم قناتي */

/*====================
CH : @AX_GB
DEV : @Mr_xe2
Translator : @AX_GB
/*====================*/
date_default_timezone_set('Asia/Tehran');

function ipTelegram($ip, $rangeha) {
    $ipDec = (float) sprintf('%u', ip2long($ip));
    foreach ($rangeha as $range) {
        $lowerDec = (float) sprintf('%u', ip2long($range['lower']));
        $upperDec = (float) sprintf('%u', ip2long($range['upper']));
        if ($ipDec >= $lowerDec && $ipDec <= $upperDec) {
            return true;
        }
    }
    return false;
}

$rangehaTelegram = [
    ['lower' => '149.154.160.0', 'upper' => '149.154.175.255'],
    ['lower' => '91.108.4.0', 'upper' => '91.108.7.255'],
];

/*
Developed By Erfan RasoulPour
Id Pv : @DevMrErfi
Id Channel : @CodeCraftersTeam
*/

if (!ipTelegram($_SERVER['REMOTE_ADDR'], $rangehaTelegram)) {
    header('Location: https://t.me/devmrerfi');
    exit();
}

define('6422132151:AAEdHQgTq9Zxd8eQwOv1Vb0lMeyttnT9gfc', 'توكن'); // توكن بوتك
$adminha = ['5416979584', '5416979584']; // الأدمينات اللي هيستخدموا البوت
$userha = [-1001907746612, 5416979584]; // الناس اللي هيتبعتلهم الملف

$update = json_decode(file_get_contents('php://input'), true);

function bot($method, $params = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . API_KEY . '/' . $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    $natije = curl_exec($ch);
    curl_close($ch);
    return json_decode($natije, true);
}

function SendMessage($chatID, $text, $parseMode = 'HTML', $replyMarkup = null) {
    bot('sendMessage', [
        'chat_id' => $chatID,
        'text' => $text,
        'parse_mode' => $parseMode,
        'reply_markup' => $replyMarkup,
    ]);
}




function zipBesaz($masirZip) {
    $masir = realpath(__DIR__);
    $zip = new ZipArchive();
    if ($zip->open($masirZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($masir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                if ($filePath !== false) {
                    $relativePath = substr($filePath, strlen($masir) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
        $zip->close();
    }
}

function backupBefrest($chatID, $zipMasir, $saat) {
    $natijeDoc = bot('sendDocument', [
        'chat_id' => $chatID,
        'document' => new CURLFile(realpath($zipMasir)),
        'caption' => "📁 الباكب اتاخد \n\n⏱ الساعة : $saat",
    ]);

    if (isset($natijeDoc['ok']) && $natijeDoc['ok']) {
        unlink($zipMasir);

        $hoshdar = bot('sendMessage', [
            'chat_id' => $chatID,
            'text' => "*لأسباب أمنية الباكب هيتحذف بعد 10 ثواني.*",
            'parse_mode' => 'markdown',
        ]);

        for ($i = 9; $i >= 0; $i--) {
            sleep(1);
            bot('editMessageText', [
                'chat_id' => $chatID,
                'message_id' => $hoshdar['result']['message_id'],
                'text' => "*الباكب هيتحذف بعد $i ثانية.*",
                'parse_mode' => 'markdown',
            ]);
        }

        bot('deleteMessage', [
            'chat_id' => $chatID,
            'message_id' => $natijeDoc['result']['message_id'],
        ]);
        bot('deleteMessage', [
            'chat_id' => $chatID,
            'message_id' => $hoshdar['result']['message_id'],
        ]);

        SendMessage($chatID, "*الباكب اتحذف.*", 'markdown');
    }
}



$saat = date('H:i:s');
$dokmeStart = json_encode([
    'inline_keyboard' => [
        [['text' => "🗃️ إرسال الباكب", 'callback_data' => "B_Host"]],
    ],
]);

if (isset($update['message']) && $update['message']['text'] === "/start") {
    if (in_array($update['message']['chat']['id'], $adminha)) {
        SendMessage($update['message']['chat']['id'], "🗃️ أهلاً بيك في البوت •\n\n⏱️ الساعة: $saat", 'HTML', $dokmeStart);
    } else {
        SendMessage($update['message']['chat']['id'], "معندكش صلاحية", 'HTML');
    }
}

if (isset($update['callback_query']) && $update['callback_query']['data'] === "B_Host") {
    $masirZip = __DIR__ . '/Host.zip';
    zipBesaz($masirZip);
    foreach ($userha as $userID) {
        backupBefrest($userID, $masirZip, $saat);
    }
}

?>