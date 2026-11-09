<?php

function rr_block(string $name, array $attrs, string $markup): string {
    $json = $attrs ? ' ' . serialize_block_attributes($attrs) : '';
    return "<!-- wp:$name$json -->\n$markup\n<!-- /wp:$name -->\n";
}

function rr_group(string $class, string $content, string $tag = 'div', string $id = ''): string {
    $attrs = ['className' => $class];
    if ($tag !== 'div') {
        $attrs['tagName'] = $tag;
    }
    $anchor = '';
    if ($id !== '') {
        $attrs['anchor'] = $id;
        $anchor = ' id="' . esc_attr($id) . '"';
    }
    return rr_block('group', $attrs, "<$tag$anchor class=\"wp-block-group $class\">\n$content</$tag>");
}

function rr_paragraph(string $content, string $class = ''): string {
    return rr_block('paragraph', $class ? ['className' => $class] : [], $class ? "<p class=\"$class\">$content</p>" : "<p>$content</p>");
}

function rr_heading(string $text, int $level, string $id): string {
    return rr_block('heading', ['level' => $level, 'anchor' => $id], "<h$level class=\"wp-block-heading\" id=\"$id\">$text</h$level>");
}

function rr_image(int $attachment, string $alt, string $class): string {
    $src = esc_url(wp_get_attachment_url($attachment));
    $alt = esc_attr($alt);
    return rr_block('image', ['id' => $attachment, 'sizeSlug' => 'full', 'linkDestination' => 'none', 'className' => $class],
        "<figure class=\"wp-block-image size-full $class\"><img src=\"$src\" alt=\"$alt\" class=\"wp-image-$attachment\"/></figure>");
}

function rr_booking(): string {
    return rr_block('buttons', [], '<div class="wp-block-buttons">' . rr_block('button', ['className' => 'rr-book'],
        '<div class="wp-block-button rr-book"><a class="wp-block-button__link wp-element-button" href="https://vk.ru/royalroom174">Записаться</a></div>') . '</div>');
}

function rr_disclosure(string $label, string $description): string {
    return rr_block('details', [],
        '<details class="wp-block-details"><summary>' . esc_html($label) . '</summary>' .
        rr_paragraph(esc_html($description)) . '</details>');
}

function rr_build_blocks(array $images): string {
    $nav = rr_group('rr-nav',
        rr_paragraph('<a href="#services">Услуги</a>') .
        rr_paragraph('<a href="#studio">О студии</a>') .
        rr_paragraph('<a href="#contact">Контакты</a>'), 'nav', 'navigation');
    $header = rr_group('rr-header',
        rr_group('wrap', rr_paragraph('<a href="#top">ROYAL ROOM</a>', 'rr-brand') . $nav), 'header', 'top');

    $heroCopy = rr_group('rr-hero-copy',
        rr_image($images['logo'], 'Royal Room', 'rr-logo') .
        rr_heading('Студия красоты', 1, 'hero-title') .
        rr_paragraph('Маникюр, педикюр, брови, ресницы и массаж лица.', 'rr-hero-lead') .
        rr_booking());
    $hero = rr_group('rr-hero hero',
        rr_group('wrap',
            rr_group('rr-hero-grid',
                $heroCopy .
                rr_image($images['hero-manicure'], 'Руки с нюдовым маникюром', 'rr-hero-photo rr-hero-photo--left') .
                rr_image($images['hero-massage'], 'Мастер массирует лицо', 'rr-hero-photo rr-hero-photo--right')) .
            rr_group('rr-hero-facts',
                rr_paragraph('Челябинск') .
                rr_paragraph('Ежедневно с 9:00 до 21:00'))), 'section');

    $services = [
        'Маникюр' => 'Уход за руками и ногтями. Форму и оттенок покрытия можно обсудить с мастером.',
        'Педикюр' => 'Уход за стопами и ногтями. При записи расскажите мастеру, какой результат вам нужен.',
        'Брови' => 'Мастер поможет подобрать форму и оттенок, которые подходят вашим чертам.',
        'Ресницы' => 'Расскажите мастеру, какой эффект вам нравится. Доступные процедуры можно уточнить при записи.',
        'Массаж лица' => 'Время для отдыха и ухода за лицом. Подробности процедуры можно уточнить при записи.',
    ];
    $serviceRows = '';
    foreach ($services as $label => $description) {
        $serviceRows .= rr_disclosure($label, $description);
    }
    $offer = rr_group('rr-section rr-services',
        rr_group('wrap',
            rr_heading('Услуги студии', 2, 'services-title') .
            rr_group('rr-services-grid',
                rr_group('rr-service-photos',
                    rr_image($images['manicure-detail'], 'Светлое покрытие на коротких ногтях', 'rr-service-photo') .
                    rr_image($images['polish-palette'], 'Образцы оттенков лака для ногтей', 'rr-detail-photo')) .
                rr_group('rr-service-list', $serviceRows))), 'section', 'services');

    $studio = rr_group('rr-section rr-studio',
        rr_group('wrap',
            rr_heading('О Royal Room', 2, 'studio-title') .
            rr_group('rr-studio-grid',
                rr_image($images['manicure-session'], 'Мастер и гостья за маникюрным столом', 'rr-studio-photo') .
                rr_group('rr-statement',
                    rr_paragraph('Мы создали уютную студию, где можно <strong>выдохнуть</strong> и доверить уход внимательным мастерам.', 'mo-wh__text') .
                    rr_paragraph('С любовью, Royal Room', 'rr-signoff')))), 'section', 'studio');

    $contact = rr_group('rr-contact',
        rr_group('wrap',
            rr_heading('Адрес и запись', 2, 'contact-title') .
            rr_group('rr-contact-grid',
                rr_group('rr-contact-location',
                    rr_paragraph('Челябинск,<br>ул. Коммуны, 131', 'rr-contact-address') .
                    rr_paragraph('<a href="https://yandex.ru/maps/org/royal_room/1853835657/">Открыть на карте</a>', 'rr-map') .
                    rr_paragraph('<a href="tel:+73512480868">+7 (351) 248-08-68</a>', 'rr-contact-phone')) .
                rr_group('rr-contact-copy',
                    rr_paragraph('Напишите в группу ВКонтакте, чтобы уточнить стоимость и выбрать время визита.') .
                    rr_booking())) .
            rr_paragraph('Royal Room · Иллюстративные фотографии <a href="https://www.pexels.com/">Pexels</a>', 'rr-credits')), 'footer', 'contact');

    return $header . rr_group('rr-main', $hero . $offer . $studio, 'main', 'main') . $contact;
}
