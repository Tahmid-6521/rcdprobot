<?php
$token = '7113571075:AAEy2AfQfb5Y0EcnkfX0qAwb0N03JdtGfgc';

function getChannelPosts($channelLink) {
    $url = "https://api.telegram.org/bot$token/getChat";
    $data = [
        'channel' => $channelLink
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true)['result']['messages'];
}

function sendMedia($chatId, $media) {
    global $token;
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $data = ['chat_id' => $chatId, 'text' => $media['text'] ?? 'No content'];

    if ($media['type'] === 'photo') {
        $url = "https://api.telegram.org/bot$token/sendPhoto";
        $data['photo'] = $media['file_id'];
    } elseif ($media['type'] === 'video') {
        $url = "https://api.telegram.org/bot$token/sendVideo";
        $data['video'] = $media['file_id'];
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function handleExtractCommand($chatId, $channelLink) {
    $posts = getChannelPosts($channelLink);

    foreach ($posts as $post) {
        if (isset($post['photo'])) {
            sendMedia($chatId, ['type' => 'photo', 'file_id' => $post['photo']['file_id']]);
        } elseif (isset($post['video'])) {
            sendMedia($chatId, ['type' => 'video', 'file_id' => $post['video']['file_id']]);
        } else {
            sendMedia($chatId, ['type' => 'text', 'text' => $post['text']]);
        }
    }
}
