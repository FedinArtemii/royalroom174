<?php

add_action('after_setup_theme', function () {
    add_theme_support('editor-styles');
    add_theme_support('responsive-embeds');
    add_editor_style('assets/site.css');
});

function rr_asset(string $file): string {
    $prefix = defined('RR_STATIC_EXPORT') ? 'assets/' : get_theme_file_uri('/assets/');
    return $prefix . $file;
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
    if (!array_intersect(['rr-hero-photo', 'rr-logo'], $classes)) {
        return $html;
    }
    $processor = new WP_HTML_Tag_Processor($html);
    if ($processor->next_tag('img')) {
        $processor->set_attribute('loading', 'eager');
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
