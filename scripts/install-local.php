<?php

$project = dirname(__DIR__);
$root = $project . '/.local/wordpress';
$url = 'http://127.0.0.1:8874';
$_SERVER['HTTP_HOST'] = '127.0.0.1:8874';
$_SERVER['REQUEST_URI'] = '/';

if (!is_file($root . '/wp-config.php')) {
    $settings = [
        'DB_NAME' => 'royal_room', 'DB_USER' => 'local', 'DB_PASSWORD' => '',
        'DB_HOST' => 'localhost', 'DB_CHARSET' => 'utf8mb4', 'DB_COLLATE' => '',
        'DB_ENGINE' => 'sqlite', 'WP_HOME' => $url, 'WP_SITEURL' => $url,
        'DISABLE_WP_CRON' => true, 'WP_DEBUG' => false,
    ];
    foreach (['AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT'] as $key) {
        $settings[$key] = bin2hex(random_bytes(32));
    }
    $config = "<?php\n";
    foreach ($settings as $key => $value) {
        $config .= 'define(' . var_export($key, true) . ', ' . var_export($value, true) . ");\n";
    }
    $config .= "\n\$table_prefix = 'rr_';\nif (!defined('ABSPATH')) { define('ABSPATH', __DIR__ . '/'); }\nrequire_once ABSPATH . 'wp-settings.php';\n";
    file_put_contents($root . '/wp-config.php', $config);
}

define('WP_INSTALLING', true);
require $root . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require __DIR__ . '/page-blocks.php';

if (!is_blog_installed()) {
    $password = wp_generate_password(28, false);
    $install = wp_install('Royal Room', 'royal-room-editor', 'royal-room@example.invalid', false, '', $password, 'ru_RU');
    file_put_contents($project . '/.local/admin.txt', "$url/wp-admin/\nroyal-room-editor\n$password\n");
}
wp_set_current_user((int) get_user_by('login', 'royal-room-editor')->ID);
switch_theme('royal-room');
update_option('timezone_string', 'Asia/Yekaterinburg');
update_option('blogdescription', 'Студия красоты в Челябинске');
update_option('blog_public', 0);
update_option('default_comment_status', 'closed');
update_option('uploads_use_yearmonth_folders', 0);

$page = get_page_by_path('royal-room');
if (!$page) {
    $sources = [
        'logo' => "$project/theme/royal-room/assets/royal-room-mark.png",
        'hero-manicure' => "$project/media/hero-manicure.webp",
        'hero-massage' => "$project/media/hero-massage.webp",
        'manicure-detail' => "$project/media/manicure-detail.webp",
        'polish-palette' => "$project/media/polish-palette.webp",
        'manicure-session' => "$project/media/manicure-session.webp",
    ];
    $ids = [];
    foreach ($sources as $key => $file) {
        $tmp = wp_tempnam(basename($file));
        copy($file, $tmp);
        $id = media_handle_sideload(['name' => basename($file), 'tmp_name' => $tmp], 0);
        if (is_wp_error($id)) {
            throw new RuntimeException($id->get_error_message());
        }
        $ids[$key] = $id;
    }
    $id = wp_insert_post(wp_slash([
        'post_type' => 'page',
        'post_name' => 'royal-room',
        'post_title' => 'Royal Room',
        'post_content' => rr_build_blocks($ids),
        'post_status' => 'publish',
        'comment_status' => 'closed',
    ]), true);
    if (is_wp_error($id)) {
        throw new RuntimeException($id->get_error_message());
    }
} else {
    $id = $page->ID;
}
update_option('page_on_front', $id);
update_option('show_on_front', 'page');
echo "WordPress: $url/\nEditor: $url/wp-admin/post.php?post=$id&action=edit\n";
