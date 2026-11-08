<?php

if (!isset($rr_content)) {
    $rr_content = have_posts() ? get_post()->post_content : '';
}
$rr_static = defined('RR_STATIC_EXPORT');
$rr_title = 'Royal Room — студия красоты в Челябинске';
$rr_description = 'Маникюр, педикюр, брови, ресницы и массаж лица. Royal Room, Челябинск, ул. Коммуны, 131. Запись во ВКонтакте.';
$rr_public = 'https://fedinartemii.github.io/royalroom174/';
?><!doctype html>
<html lang="ru" data-type="place" data-radius="sharp" data-buttons="solid" data-motion>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?php echo esc_html($rr_title); ?></title>
    <meta name="description" content="<?php echo esc_attr($rr_description); ?>">
    <meta name="page-type" content="place">
    <meta name="page-kind" content="place">
    <meta name="site-kind" content="other">
    <meta name="theme-color" content="#523c2a">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:title" content="<?php echo esc_attr($rr_title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($rr_description); ?>">
    <meta property="og:image" content="<?php echo esc_url($rr_public . 'assets/share.jpg'); ?>">
    <meta property="og:url" content="<?php echo esc_url($rr_public); ?>">
    <?php if ($rr_static) : ?>
    <link rel="canonical" href="<?php echo esc_url($rr_public); ?>">
    <?php else : ?>
    <?php wp_head(); ?>
    <?php endif; ?>
    <link rel="icon" href="<?php echo esc_attr(rr_asset('icon.svg')); ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?php echo esc_attr(rr_asset('motion.css')); ?>">
    <link rel="stylesheet" href="<?php echo esc_attr(rr_asset('moment.css')); ?>">
    <link rel="stylesheet" href="<?php echo esc_attr(rr_asset('site.css')); ?>">
    <script type="module" src="<?php echo esc_attr(rr_asset('site.js')); ?>"></script>
</head>
<body>
<?php if (!$rr_static) { wp_body_open(); } ?>
<a class="skip-link" href="#main">Перейти к содержанию</a>
<?php echo apply_filters('the_content', $rr_content); ?>
<?php if (!$rr_static) { wp_footer(); } ?>
</body>
</html>
