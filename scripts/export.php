<?php

$project = dirname(__DIR__);
$_SERVER['HTTP_HOST'] = '127.0.0.1:8874';
$_SERVER['REQUEST_URI'] = '/';
define('RR_STATIC_EXPORT', true);
require $project . '/.local/wordpress/wp-load.php';

$page = get_post((int) get_option('page_on_front'));
if (!$page || $page->post_status !== 'publish') {
    throw new RuntimeException('Publish the WordPress front page before exporting.');
}
$GLOBALS['post'] = $page;
$rr_content = $page->post_content;
ob_start();
require $project . '/theme/royal-room/index.php';
$html = ob_get_clean();

$docs = "$project/docs";
$uploads = wp_get_upload_dir();
$uploadUrl = rtrim($uploads['baseurl'], '/');
$uploadPath = wp_parse_url($uploadUrl, PHP_URL_PATH);
$html = str_replace([$uploadUrl . '/', $uploadPath . '/'], 'media/', $html);
foreach (['assets' => "$project/theme/royal-room/assets", 'media' => $uploads['basedir']] as $dir => $source) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS));
    foreach ($files as $file) {
        $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($source) + 1));
        if ($dir === 'media' && !str_contains($html, 'media/' . $relative)) {
            continue;
        }
        $destination = "$docs/$dir/$relative";
        if (!is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0777, true);
        }
        if (!copy($file->getPathname(), $destination)) {
            throw new RuntimeException("Cannot copy $relative");
        }
    }
}
file_put_contents("$docs/index.html", $html);
file_put_contents("$docs/.nojekyll", '');
echo "GitHub Pages export: $docs/index.html\n";
