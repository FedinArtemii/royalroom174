# Royal Room

Одностраничный сайт студии красоты в Челябинске. Выбран вариант 03 «Подпись».

- Локальное превью WordPress — http://127.0.0.1:8874/
- Публичный сайт — https://fedinartemii.github.io/royalroom174/
- Запись — https://vk.ru/royalroom174

## Правки и публикация

На Windows с PHP и расширениями PDO SQLite, GD и mbstring запустите `./scripts/Start-Preview.ps1` из PowerShell. При первом запуске скрипт скачает WordPress и SQLite Database Integration с WordPress.org и создаст локальную базу.

Учётные данные записываются в локальный файл `.local/admin.txt`. В панели WordPress откройте страницу **Royal Room**. Заголовки, тексты, фотографии, ссылки и раскрывающийся список услуг состоят из стандартных блоков. Сохранение страницы обновляет локальное превью.

Чтобы обновить публикацию, выполните `./scripts/Export-Pages.ps1`, добавьте изменения в `docs/` в коммит и отправьте его в `main`. GitHub Pages публикует папку `docs/`. Локальная база и учётные данные исключены из репозитория.

## Материалы

Логотип предоставлен в брифе. Фотографии показывают процедуры и служат иллюстрациями. Лицензии шрифтов Manrope и Prata лежат рядом с файлами шрифтов.

| Кадр | Источник |
| --- | --- |
| Маникюр на первом экране | [Vera Emilie, Pexels](https://www.pexels.com/photo/a-woman-s-hand-with-long-nails-and-a-white-background-20805375/) |
| Массаж лица | [olia danilevich, Pexels](https://www.pexels.com/photo/close-up-shot-of-a-woman-having-a-face-massage-9335966/) |
| Светлое покрытие | [Alexander Mass, Pexels](https://www.pexels.com/photo/elegant-white-manicured-hand-on-beige-surface-36553742/) |
| Палитра покрытий | [cottonbro studio, Pexels](https://www.pexels.com/photo/client-in-a-beauty-salon-3997379/) |
| Работа мастера | [Leeloo The First, Pexels](https://www.pexels.com/photo/women-sitting-by-table-and-doing-manicure-4677844/) |

Часы, услуги и городской телефон сверены с [профилем студии во ВКонтакте](https://vk.ru/royalroom174). Адрес сверён с [карточкой студии на Яндекс Картах](https://yandex.ru/maps/org/royal_room/1853835657/).
