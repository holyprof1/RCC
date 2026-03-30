<?php $site = rcc_site_config(); ?>
</main>
<footer class="rcc-footer">
    <div class="rcc-shell">
        <div class="rcc-footer__top">
            <div>
                <h2>Radiant Creative Concepts Ltd.</h2>
                <p class="rcc-footer__col-tagline">Organizing Africa&apos;s Leading Exhibitions &amp; Trade Shows. Building Connections, Creating Opportunities.</p>
            </div>
            <div>
                <h3>Follow Us</h3>
                <div class="rcc-footer__socials">
                    <?php foreach ($site['socials'] as $social) : ?>
                        <a href="<?php echo esc_url($social['url']); ?>"><?php echo esc_html($social['label']); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div>
                <h3>Contact</h3>
                <p>&#9742; <?php echo esc_html($site['phone']); ?></p>
                <p>&#9993; <?php echo esc_html($site['email']); ?></p>
                <p style="margin-top:0.75rem;font-size:0.78rem;line-height:1.65;"><?php echo esc_html($site['address']); ?></p>
            </div>
        </div>
        <div class="rcc-footer__bottom">
            <div class="rcc-footer__legal">
                <?php foreach ($site['legal'] as $legal) : ?>
                    <a href="<?php echo esc_url($legal['url']); ?>"><?php echo esc_html($legal['label']); ?></a>
                <?php endforeach; ?>
            </div>
            <p>&copy; <?php echo esc_html(date('Y')); ?> Radiant Creative Concepts Limited &mdash; RC 8938428</p>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
