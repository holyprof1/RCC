<?php
get_header();

$slug = rcc_get_current_slug();

if (!rcc_render_page_by_slug($slug)) :
    if (have_posts()) :
        while (have_posts()) :
            the_post();
            ?>
            <section class="rcc-page-hero">
                <div class="rcc-shell">
                    <span class="rcc-kicker">Page</span>
                    <h1><?php the_title(); ?></h1>
                </div>
            </section>
            <section class="rcc-section">
                <div class="rcc-shell">
                    <article class="rcc-card">
                        <?php the_content(); ?>
                    </article>
                </div>
            </section>
            <?php
        endwhile;
    endif;
endif;

get_footer();
