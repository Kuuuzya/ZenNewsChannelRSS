<div class="wrap">
    <h1>Настройки Zen News&Channel RSS</h1>

    <div class="notice notice-info inline">
        <p>
            <strong>Ваши RSS ленты:</strong><br>
            Дзен Новости: <a href="<?php echo site_url('/' . get_option('zen_rss_news_slug', 'zen-news')); ?>"
                target="_blank"><?php echo site_url('/' . get_option('zen_rss_news_slug', 'zen-news')); ?></a><br>
            Дзен Канал: <a href="<?php echo site_url('/' . get_option('zen_rss_channel_slug', 'zen-channel')); ?>"
                target="_blank"><?php echo site_url('/' . get_option('zen_rss_channel_slug', 'zen-channel')); ?></a>
        </p>
    </div>

    <form method="post" action="options.php">
        <?php
        settings_fields('zen_rss_option_group');
        do_settings_sections('zen_rss_option_group');
        ?>

        <hr>
        <h2>Общие настройки</h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">URL для Дзен Новостей</th>
                <td>
                    <input type="text" name="zen_rss_news_slug"
                        value="<?php echo esc_attr(get_option('zen_rss_news_slug', 'zen-news')); ?>"
                        class="regular-text" />
                    <p class="description">Путь (slug) для ленты новостей. По умолчанию: zen-news</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">URL для Дзен Канала</th>
                <td>
                    <input type="text" name="zen_rss_channel_slug"
                        value="<?php echo esc_attr(get_option('zen_rss_channel_slug', 'zen-channel')); ?>"
                        class="regular-text" />
                    <p class="description">Путь (slug) для ленты канала. По умолчанию: zen-channel</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Yandex Webmaster Token</th>
                <td>
                    <input type="text" name="zen_rss_yandex_token"
                        value="<?php echo esc_attr(get_option('zen_rss_yandex_token')); ?>" class="regular-text" />
                    <p class="description">OAuth токен для доступа к API Яндекс.Вебмастера.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Отправлять оригинальные тексты</th>
                <td>
                    <label>
                        <input type="checkbox" name="zen_rss_send_unique_text" value="1" <?php checked(1, get_option('zen_rss_send_unique_text'), true); ?> />
                        Автоматически отправлять тексты в "Оригинальные тексты" Яндекс.Вебмастера
                    </label>
                </td>
            </tr>
        </table>

        <hr>
        <h2>Настройки ленты для Дзен Новостей</h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Количество записей</th>
                <td>
                    <input type="number" name="zen_rss_news_count"
                        value="<?php echo esc_attr(get_option('zen_rss_news_count', 50)); ?>" class="small-text" />
                    <p class="description">Сколько последних записей включать в ленту.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Максимальный возраст (дней)</th>
                <td>
                    <input type="number" name="zen_rss_news_max_age"
                        value="<?php echo esc_attr(get_option('zen_rss_news_max_age', 3)); ?>" class="small-text" />
                    <p class="description">Не включать записи старше указанного количества дней.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Логотип издания</th>
                <td>
                    <input type="text" name="zen_rss_news_logo"
                        value="<?php echo esc_attr(get_option('zen_rss_news_logo')); ?>" class="regular-text" />
                    <p class="description">Ссылка на горизонтальный логотип.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Квадратный логотип</th>
                <td>
                    <input type="text" name="zen_rss_news_logo_square"
                        value="<?php echo esc_attr(get_option('zen_rss_news_logo_square')); ?>"
                        class="regular-text" />
                    <p class="description">Ссылка на квадратный логотип (опционально).</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Миниатюры</th>
                <td>
                    <label>
                        <input type="checkbox" name="zen_rss_news_thumbnails" value="1" <?php checked(1, get_option('zen_rss_news_thumbnails'), true); ?> />
                        Включать изображения (тег enclosure)
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Очистка контента</th>
                <td>
                    <label>
                        <input type="checkbox" name="zen_rss_news_remove_teaser" value="1" <?php checked(1, get_option('zen_rss_news_remove_teaser'), true); ?> />
                        Удалять первый абзац (тизер) из полного текста
                    </label>
                    <br>
                    <label>
                        <input type="checkbox" name="zen_rss_news_remove_shortcodes" value="1" <?php checked(1, get_option('zen_rss_news_remove_shortcodes'), true); ?> />
                        Удалять шорткоды
                    </label>
                </td>
            </tr>
        </table>

        <hr>
        <h2>Настройки ленты для Дзен Канала</h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Количество записей</th>
                <td>
                    <input type="number" name="zen_rss_channel_count"
                        value="<?php echo esc_attr(get_option('zen_rss_channel_count', 50)); ?>"
                        class="small-text" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Максимальный возраст (дней)</th>
                <td>
                    <input type="number" name="zen_rss_channel_max_age"
                        value="<?php echo esc_attr(get_option('zen_rss_channel_max_age', 30)); ?>"
                        class="small-text" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Полный текст</th>
                <td>
                    <label>
                        <input type="checkbox" name="zen_rss_channel_fulltext" value="1" <?php checked(1, get_option('zen_rss_channel_fulltext'), true); ?> />
                        Генерировать content:encoded (полный текст статьи с разметкой)
                    </label>
                    <p class="description">Если выключено, будет использоваться только краткое описание (excerpt).</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Блок "Ещё по теме"</th>
                <td>
                    <label>
                        <input type="checkbox" name="zen_rss_channel_related" value="1" <?php checked(1, get_option('zen_rss_channel_related'), true); ?> />
                        Встраивать блок ссылок на похожие статьи внутрь текста
                    </label>
                    <p class="description">Добавляет 5 ссылок на статьи из той же рубрики после 2-го абзаца.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Миниатюры и Шорткоды</th>
                <td>
                    <label>
                        <input type="checkbox" name="zen_rss_channel_thumbnails" value="1" <?php checked(1, get_option('zen_rss_channel_thumbnails'), true); ?> />
                        Включать миниатюры
                    </label>
                    <br>
                    <label>
                        <input type="checkbox" name="zen_rss_channel_remove_shortcodes" value="1" <?php checked(1, get_option('zen_rss_channel_remove_shortcodes'), true); ?> />
                        Удалять шорткоды
                    </label>
                </td>
            </tr>
        </table>

        <?php submit_button('Сохранить настройки'); ?>
    </form>
</div>