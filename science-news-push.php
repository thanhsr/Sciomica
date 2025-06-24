<?php
// Đường dẫn đến RSS Feed
$rss_url = 'https://sciomica.com/feed/';

// Đường dẫn lưu cache ID bài viết mới nhất đã gửi
$cache_file = __DIR__ . '/last-post-id.txt';

// Lấy RSS Feed
$rss = simplexml_load_file($rss_url);
if (!$rss) {
    die("Không thể tải RSS.");
}

// Lấy bài mới nhất
$item = $rss->channel->item[0];
$title = (string)$item->title;
$link = (string)$item->link;
$guid = (string)$item->guid;

// Kiểm tra xem đã gửi bài này chưa
$last_sent_guid = file_exists($cache_file) ? trim(file_get_contents($cache_file)) : '';

if ($guid !== $last_sent_guid) {
    // Gửi notification đến trình duyệt (ở đây dùng gửi local - bạn có thể tích hợp Web Push API, hoặc OneSignal)
    $payload = [
        'title' => "🧪 Bài mới trên Sciomica!",
        'body' => $title,
        'url' => $link
    ];

    // Gửi đến client thông qua broadcast channel hoặc lưu payload để JavaScript gọi ajax lấy
    file_put_contents(__DIR__ . '/push-payload.json', json_encode($payload));

    // Cập nhật cache
    file_put_contents($cache_file, $guid);
    echo "Đã ghi bài mới: $title";
} else {
    echo "Không có bài mới.";
}
