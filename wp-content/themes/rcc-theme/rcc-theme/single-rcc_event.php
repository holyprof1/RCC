<?php
get_header();
$slug = get_post_field('post_name', get_the_ID());
if ($slug !== 'megastruct-africa' && $slug !== 'messodex-west-africa') {
    $slug = stripos(get_the_title(), 'mega') !== false ? 'megastruct-africa' : 'messodex-west-africa';
}
rcc_render_event_page($slug);
get_footer();
