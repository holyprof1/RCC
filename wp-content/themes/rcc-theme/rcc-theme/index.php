<?php
/**
 * index.php — Fallback template
 */
get_header();
?>
<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:160px 0 80px;">
    <div class="rcc-container" style="text-align:center;">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <h1 class="rcc-heading-lg" style="color:var(--rcc-ink);margin-bottom:20px;"><?php the_title(); ?></h1>
            <div class="rcc-body" style="max-width:720px;margin:0 auto 36px;"><?php the_content(); ?></div>
        <?php endwhile; else : ?>
            <h1 class="rcc-heading-lg" style="color:var(--rcc-ink);margin-bottom:20px;">Page not found</h1>
            <p class="rcc-body">The page you're looking for doesn't exist.</p>
            <a href="<?php echo home_url('/'); ?>" class="rcc-btn rcc-btn--gold" style="margin-top:28px;display:inline-flex;">Back to Home</a>
        <?php endif; ?>
    </div>
</div>
<?php get_footer(); ?>
