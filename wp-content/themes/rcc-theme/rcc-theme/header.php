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
<header class="rcc-header" id="rcc-header">
    <div class="rcc-shell rcc-header__inner">
        <a class="rcc-brand" href="<?php echo esc_url(home_url('/')); ?>">
            <img class="rcc-brand__logo" src="<?php echo esc_url(rcc_upload_asset_candidates(['2026/03/rcc.PNG', 'rcc.PNG'])); ?>" alt="<?php echo esc_attr($site['company']); ?>">
            <span class="rcc-brand__text">
                <strong><?php echo esc_html($site['company']); ?></strong>
                <small><?php echo esc_html($site['tagline']); ?></small>
            </span>
        </a>
        <button class="rcc-menu-toggle" id="rcc-menu-toggle" aria-expanded="false" aria-controls="rcc-mobile-nav">Menu</button>
        <nav class="rcc-nav" aria-label="Primary navigation">
            <?php foreach (rcc_nav_items() as $item) : ?>
                <a href="<?php echo esc_url($item['slug'] === '' ? home_url('/') : rcc_get_page_url($item['slug'])); ?>"><?php echo esc_html($item['label']); ?></a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>
<nav class="rcc-mobile-nav" id="rcc-mobile-nav" aria-label="Mobile navigation">
    <?php foreach (rcc_nav_items() as $item) : ?>
        <a href="<?php echo esc_url($item['slug'] === '' ? home_url('/') : rcc_get_page_url($item['slug'])); ?>"><?php echo esc_html($item['label']); ?></a>
    <?php endforeach; ?>
</nav>
<main class="rcc-main">
