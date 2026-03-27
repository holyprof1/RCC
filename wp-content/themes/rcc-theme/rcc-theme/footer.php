<?php $site = rcc_site_config(); ?>
</main>
<footer class="rcc-footer">
    <div class="rcc-shell">
        <div class="rcc-footer__top">
            <div>
                <h2><?php echo esc_html($site['company']); ?></h2>
                <p><?php echo esc_html($site['tagline']); ?></p>
            </div>
            <div>
                <h3>Social Media Handles</h3>
                <div class="rcc-footer__socials">
                    <?php foreach ($site['socials'] as $social) : ?>
                        <a href="<?php echo esc_url($social['url']); ?>"><?php echo esc_html($social['label']); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div>
                <h3>Contact Details</h3>
                <p>Phone: <?php echo esc_html($site['phone']); ?></p>
                <p>Email: <?php echo esc_html($site['email']); ?></p>
                <p>Website: <?php echo esc_html($site['website']); ?></p>
                <p>Address: <?php echo esc_html($site['address']); ?></p>
            </div>
        </div>
        <div class="rcc-footer__bottom">
            <div class="rcc-footer__legal">
                <?php foreach ($site['legal'] as $legal) : ?>
                    <a href="<?php echo esc_url($legal['url']); ?>"><?php echo esc_html($legal['label']); ?></a>
                <?php endforeach; ?>
            </div>
            <p>&copy; Radiant Creative Concepts Limited RC 8938428</p>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
