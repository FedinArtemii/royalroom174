<?php

add_action('after_setup_theme', function () {
    add_theme_support('editor-styles');
    add_theme_support('responsive-embeds');
    add_editor_style('assets/site.css');
});

add_filter('wp_img_tag_add_auto_sizes', '__return_false');

function rr_asset(string $file): string {
    $prefix = defined('RR_STATIC_EXPORT') ? 'assets/' : get_theme_file_uri('/assets/');
    $url = $prefix . $file;
    if (str_ends_with($file, '.css') || str_ends_with($file, '.js')) {
        $url .= '?v=' . substr(hash_file('sha256', get_theme_file_path('/assets/' . $file)), 0, 12);
    }
    return $url;
}

// Add behavior hooks and landmarks without changing the editor's saved core block markup.
add_filter('render_block_core/group', function ($html, $block) {
    $classes = explode(' ', $block['attrs']['className'] ?? '');
    $attributes = [];
    if (in_array('rr-hero', $classes, true)) {
        $attributes = ['data-enter' => '', 'aria-labelledby' => 'hero-title'];
    } elseif (in_array('rr-nav', $classes, true)) {
        $attributes = ['aria-label' => 'Основное меню'];
    } elseif (in_array('rr-statement', $classes, true)) {
        $attributes = ['data-moment' => 'word-highlight'];
    } elseif (in_array('rr-main', $classes, true)) {
        $attributes = ['tabindex' => '-1'];
    } elseif (in_array('rr-section', $classes, true) && isset($block['attrs']['anchor'])) {
        $attributes = ['aria-labelledby' => $block['attrs']['anchor'] . '-title'];
    }
    if (!$attributes) {
        return $html;
    }
    $processor = new WP_HTML_Tag_Processor($html);
    if ($processor->next_tag()) {
        foreach ($attributes as $name => $value) {
            $processor->set_attribute($name, $value);
        }
    }
    return $processor->get_updated_html();
}, 10, 2);

add_filter('render_block_core/image', function ($html, $block) {
    $classes = explode(' ', $block['attrs']['className'] ?? '');
    $sizes = [
        'rr-hero-photo' => '(max-width: 700px) 260px, 520px',
        'rr-detail-photo' => '(max-width: 700px) 300px, 460px',
        'rr-studio-photo' => '(max-width: 700px) 560px, (max-width: 1000px) 640px, 900px',
    ];
    if (!array_intersect(['rr-logo', ...array_keys($sizes)], $classes)) {
        return $html;
    }
    $processor = new WP_HTML_Tag_Processor($html);
    if ($processor->next_tag('img')) {
        // A cover crop needs pixels for the taller axis even when its frame is narrow.
        foreach ($sizes as $class => $size) {
            if (in_array($class, $classes, true)) {
                $processor->set_attribute('sizes', $size);
                break;
            }
        }
        if (array_intersect(['rr-hero-photo', 'rr-logo'], $classes)) {
            $processor->set_attribute('loading', 'eager');
        }
        if (in_array('rr-hero-photo--right', $classes, true)) {
            $processor->set_attribute('fetchpriority', 'high');
        }
    }
    return $processor->get_updated_html();
}, 10, 2);

add_filter('render_block_core/button', function ($html, $block) {
    if (($block['attrs']['className'] ?? '') !== 'rr-book') {
        return $html;
    }
    $processor = new WP_HTML_Tag_Processor($html);
    if ($processor->next_tag('a')) {
        $processor->add_class('btn');
        $processor->add_class('btn--primary');
        $processor->add_class('btn--arrow');
    }
    return $processor->get_updated_html();
}, 10, 2);
