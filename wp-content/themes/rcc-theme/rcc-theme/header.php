<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php $site = rcc_site_config(); ?>
<?php if (is_front_page()) : ?>
    <?php $preload = rcc_preloader_images(); ?>
    <div class="rcc-preloader" id="rcc-preloader" aria-hidden="true" data-loader-scope="home">
        <div class="rcc-preloader__inner">
            <img class="rcc-preloader__brand" src="<?php echo esc_url($preload['rcc_logo']); ?>" alt="Radiant Creative Concepts">
            <p class="rcc-preloader__label">Radiant Creative Concepts Limited</p>
            <p class="rcc-preloader__sub">Event Organizers</p>
            <div class="rcc-preloader__events">
                <div class="rcc-preloader__event">
                    <img src="<?php echo esc_url($preload['mega_logo']); ?>" alt="Megastruct Africa" onerror="this.closest('.rcc-preloader__event').style.display='none';">
                    <span>MEGASTRUCT AFRICA</span>
                </div>
                <div class="rcc-preloader__event">
                    <img src="<?php echo esc_url($preload['messo_logo']); ?>" alt="Messodex West Africa" onerror="this.closest('.rcc-preloader__event').style.display='none';">
                    <span>MESSODEX WEST AFRICA</span>
                </div>
            </div>
            <div class="rcc-preloader__bar">
                <span class="rcc-preloader__bar-fill"></span>
            </div>
        </div>
    </div>
<?php endif; ?>
<header class="rcc-header" id="rcc-header">
    <div class="rcc-shell rcc-header__inner">
        <a class="rcc-brand" href="<?php echo esc_url(home_url('/')); ?>">
            <img class="rcc-brand__logo rcc-brand__logo--desktop" src="<?php echo esc_url(rcc_get_logo_url('desktop')); ?>" alt="<?php echo esc_attr($site['company']); ?>">
            <img class="rcc-brand__logo rcc-brand__logo--mobile" src="<?php echo esc_url(rcc_get_logo_url('mobile')); ?>" alt="<?php echo esc_attr($site['company']); ?>">
        </a>
        <button class="rcc-menu-toggle" id="rcc-menu-toggle" aria-expanded="false" aria-controls="rcc-mobile-nav">Menu</button>
        <nav class="rcc-nav" aria-label="Primary navigation">
            <?php foreach (rcc_nav_items() as $item) : ?>
                <?php if (!empty($item['children'])) : ?>
                    <div class="rcc-nav__item">
                        <a class="rcc-nav__link" href="<?php echo esc_url(rcc_get_page_url($item['slug'])); ?>">
                            <?php echo esc_html($item['label']); ?> <span class="rcc-nav__caret">&#9660;</span>
                        </a>
                        <div class="rcc-nav__dropdown">
                            <?php foreach ($item['children'] as $child) : ?>
                                <a class="rcc-nav__dropdown-item" href="<?php echo esc_url(rcc_get_page_url($child['slug'])); ?>">
                                    <strong><?php echo esc_html($child['label']); ?></strong>
                                    <span><?php echo esc_html($child['desc']); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <a class="rcc-nav__link" href="<?php echo esc_url($item['slug'] === '' ? home_url('/') : rcc_get_page_url($item['slug'])); ?>"><?php echo esc_html($item['label']); ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </div>
</header>
<nav class="rcc-mobile-nav" id="rcc-mobile-nav" aria-label="Mobile navigation">
    <?php foreach (rcc_nav_items() as $item) : ?>
        <?php if (!empty($item['children'])) : ?>
            <div class="rcc-mobile-nav-parent">
                <a href="<?php echo esc_url(rcc_get_page_url($item['slug'])); ?>"><?php echo esc_html($item['label']); ?></a>
                <div class="rcc-mobile-subnav">
                    <?php foreach ($item['children'] as $child) : ?>
                        <a href="<?php echo esc_url(rcc_get_page_url($child['slug'])); ?>"><?php echo esc_html($child['label']); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else : ?>
            <a href="<?php echo esc_url($item['slug'] === '' ? home_url('/') : rcc_get_page_url($item['slug'])); ?>"><?php echo esc_html($item['label']); ?></a>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>
<main class="rcc-main">
