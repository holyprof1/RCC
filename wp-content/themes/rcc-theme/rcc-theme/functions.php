<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption', 'style', 'script']);
});

add_action('wp_enqueue_scripts', function () {
    $style_ver = file_exists(get_stylesheet_directory() . '/style.css') ? (string) filemtime(get_stylesheet_directory() . '/style.css') : '2.0.0';
    $main_css_ver = file_exists(get_template_directory() . '/assets/css/rcc-main.css') ? (string) filemtime(get_template_directory() . '/assets/css/rcc-main.css') : '2.1.0';
    $main_js_ver = file_exists(get_template_directory() . '/assets/js/main.js') ? (string) filemtime(get_template_directory() . '/assets/js/main.js') : '2.1.0';

    wp_enqueue_style(
        'rcc-fonts',
        'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&family=DM+Sans:wght@300;400;500;600;700&display=swap',
        [],
        null
    );
    wp_enqueue_style('rcc-style', get_stylesheet_uri(), [], $style_ver);
    wp_enqueue_style('rcc-main', get_template_directory_uri() . '/assets/css/rcc-main.css', ['rcc-fonts'], $main_css_ver);
    wp_enqueue_script('rcc-main', get_template_directory_uri() . '/assets/js/main.js', [], $main_js_ver, true);
});

add_action('wp_head', function () {
    $images = [
        rcc_get_logo_url(),
        rcc_get_logo_url('mega'),
        rcc_get_logo_url('messo'),
        rcc_upload_asset_candidates(['hero.png']),
        rcc_get_event_visual_url('mega'),
        rcc_get_event_visual_url('messo'),
    ];

    foreach (array_unique($images) as $i => $image) {
        if (!$image) {
            continue;
        }
        echo '<link rel="preload" as="image" href="' . esc_url($image) . '"' . ($i === 0 ? ' fetchpriority="high"' : '') . '>' . "\n";
    }
}, 5);

function rcc_site_config()
{
    return [
        'company'       => 'Radiant Creative Concepts Limited',
        'short_company' => 'RCC',
        'tagline'       => 'Event Management | Exhibitions | Trade Shows | Corporate & Social Events',
        'phone'         => rcc_acf_option('rcc_phone',   '+234 903 491 4989'),
        'email'         => rcc_acf_option('rcc_email',   'info@radiantccafrica.com'),
        'website'       => 'www.radiantccafrica.com',
        'website_url'   => 'https://www.radiantccafrica.com',
        'address'       => rcc_acf_option('rcc_address', 'RADIANT CREATIVE CONCEPTS LIMITED (EVENTS & EXHIBITIONS), Thuraya Crescent, Along Lagos-Ibadan Expressway, Mowe, Ogun State, Nigeria.'),
        'socials' => [
            ['label' => 'FB',       'url' => rcc_acf_option('rcc_social_facebook',  '#')],
            ['label' => 'IG',       'url' => rcc_acf_option('rcc_social_instagram', '#')],
            ['label' => 'YouTube',  'url' => rcc_acf_option('rcc_social_youtube',   '#')],
            ['label' => 'LinkedIn', 'url' => rcc_acf_option('rcc_social_linkedin',  '#')],
        ],
        'legal' => [
            ['label' => 'Privacy Policy',   'url' => '#'],
            ['label' => 'Terms & Services', 'url' => '#'],
        ],
    ];
}

function rcc_get_page_url($slug)
{
    $page = get_page_by_path($slug);
    if ($page instanceof WP_Post) {
        return get_permalink($page);
    }

    $legacy = [
        'about-us' => 'about',
        'exhibitions' => 'events',
        'contact-us' => 'contact',
    ];

    if (isset($legacy[$slug])) {
        $page = get_page_by_path($legacy[$slug]);
        if ($page instanceof WP_Post) {
            return get_permalink($page);
        }
    }

    return home_url('/' . trim($slug, '/') . '/');
}

function rcc_nav_items()
{
    return [
        ['label' => 'Home', 'slug' => '', 'children' => []],
        ['label' => 'About Us', 'slug' => 'about-us', 'children' => []],
        [
            'label' => 'Our Events',
            'slug'  => 'exhibitions',
            'children' => [
                ['label' => 'MEGASTRUCT AFRICA',     'slug' => 'megastruct-africa',    'desc' => 'Infrastructure, Construction & Mining Expo'],
                ['label' => 'MESSODEX WEST AFRICA',  'slug' => 'messodex-west-africa', 'desc' => 'Media, Stage & Sound Technology Expo'],
            ],
        ],
        ['label' => 'Gallery', 'slug' => 'gallery', 'children' => []],
        ['label' => 'Contact Us', 'slug' => 'contact-us', 'children' => []],
    ];
}

function rcc_icon_arrow()
{
    return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h13"></path><path d="M13 6l6 6-6 6"></path></svg>';
}

function rcc_button($label, $url, $class = 'rcc-btn-primary')
{
    echo '<a class="rcc-btn ' . esc_attr($class) . '" href="' . esc_url($url) . '">' . esc_html($label) . rcc_icon_arrow() . '</a>';
}

function rcc_upload_asset($filename)
{
    $basename  = basename($filename);
    $theme_abs = get_template_directory() . '/assets/images/' . $basename;
    if (file_exists($theme_abs)) {
        return rcc_versioned_public_url(get_template_directory_uri() . '/assets/images/' . $basename);
    }
    return rcc_versioned_public_url(get_template_directory_uri() . '/assets/images/' . $basename);
}

function rcc_acf_option($key, $default = '')
{
    // WordPress options saved via RCC Settings admin page (primary)
    $wp_val = get_option('rcc_opt_' . $key, '');
    if ($wp_val !== '' && $wp_val !== false) {
        return $wp_val;
    }
    // ACF PRO options page (fallback if installed)
    if (function_exists('get_field')) {
        $val = get_field($key, 'option');
        if ($val !== null && $val !== false && $val !== '') {
            return $val;
        }
    }
    return $default;
}

function rcc_versioned_public_url($url)
{
    if (!$url) {
        return $url;
    }

    $parsed = wp_parse_url($url);
    $path = $parsed['path'] ?? '';
    if (!$path) {
        return $url;
    }

    $local = wp_normalize_path(untrailingslashit(ABSPATH) . '/' . ltrim($path, '/'));
    if (!file_exists($local)) {
        return $url;
    }

    return add_query_arg('ver', (string) filemtime($local), $url);
}

function rcc_saved_media_url($saved)
{
    if (!$saved) {
        return '';
    }

    $saved = trim((string) $saved);
    $parsed = wp_parse_url($saved);
    $path = $parsed['path'] ?? '';

    if ($path) {
        $local = wp_normalize_path(untrailingslashit(ABSPATH) . '/' . ltrim($path, '/'));
        if (file_exists($local)) {
            return rcc_versioned_public_url($saved);
        }
    }

    if (preg_match('#^https?://#i', $saved)) {
        return $saved;
    }

    return '';
}

function rcc_theme_img_url($filename)
{
    return get_template_directory_uri() . '/assets/images/' . $filename;
}

function rcc_theme_image_exists($filename)
{
    return file_exists(get_template_directory() . '/assets/images/' . basename($filename));
}

function rcc_theme_svg_logo_url()
{
    if (rcc_theme_image_exists('rcc.svg')) {
        return rcc_versioned_public_url(rcc_theme_img_url('rcc.svg'));
    }

    return '';
}

function rcc_get_logo_url($variant = '')
{
    $svg_logo = rcc_theme_svg_logo_url();

    if ($variant === 'mega') {
        return rcc_upload_asset_candidates(['MEGASTRUCT-LOGO.png', 'megastruct-logo.jpeg', 'megastruct-lgo.jpeg']);
    }
    if ($variant === 'messo') {
        return rcc_upload_asset_candidates(['mesodex-logo.jpeg', 'mesodex-ogo.jpeg']);
    }
    if ($variant === 'desktop') {
        if ($svg_logo) { return $svg_logo; }
        return rcc_upload_asset_candidates(['rccc.png', 'RCC.png', 'rcc2.PNG']);
    }
    if ($variant === 'mobile') {
        if ($svg_logo) { return $svg_logo; }
        return rcc_upload_asset_candidates(['RCC.png', 'rccc.png', 'rcc2.PNG']);
    }

    if ($svg_logo) { return $svg_logo; }
    return rcc_upload_asset_candidates(['rccc.png', 'RCC.png', 'rcc2.PNG']);
}

function rcc_get_event_visual_url($variant)
{
    if ($variant === 'mega') {
        return rcc_upload_asset_candidates(['MEGASTRUCT.png', 'megastruct.jpeg']);
    }

    if ($variant === 'messo') {
        return rcc_upload_asset_candidates(['messodex-logo.jpeg']);
    }

    return '';
}

function rcc_upload_asset_candidates($candidates)
{
    foreach ($candidates as $candidate) {
        $basename  = basename($candidate);
        $theme_abs = get_template_directory() . '/assets/images/' . $basename;
        if (file_exists($theme_abs)) {
            return rcc_versioned_public_url(get_template_directory_uri() . '/assets/images/' . $basename);
        }
    }

    return rcc_versioned_public_url(get_template_directory_uri() . '/assets/images/' . basename($candidates[0]));
}

function rcc_get_home_data()
{
    return [
        'hero_title_html' => 'Shaping Africa\'s <span>Premier Events</span> &amp; <span>Exhibitions</span>',
        'hero_text' => 'Connecting Industries. Showcasing Innovation. Creating Lasting Opportunities.',
        'hero_image' => rcc_upload_asset_candidates(['hero.png']),
        'events' => [
            [
                'title' => 'MEGASTRUCT AFRICA',
                'subtitle' => 'Mega Infrastructure, Construction & Mining Equipment Expo',
                'description' => 'An international platform for construction machinery, infrastructure solutions, mining equipment, investors, and industry leaders ready to shape Africa\'s growth.',
                'url' => rcc_get_page_url('megastruct-africa'),
                'image' => rcc_get_event_visual_url('mega'),
                'meta' => '11th - 13th October 2026 | Landmark Centre, Victoria Island, Lagos',
                'theme' => 'megastruct',
            ],
            [
                'title' => 'MESSODEX WEST AFRICA',
                'subtitle' => 'Media, Stage & Sound Technology Expo',
                'description' => 'A bold showcase for media technology, stage engineering, professional audio, lighting systems, and creative production built for the West African market.',
                'url' => rcc_get_page_url('messodex-west-africa'),
                'image' => rcc_get_event_visual_url('messo'),
                'meta' => '19-21 August 2026 | Landmark Centre, Victoria Island, Lagos',
                'theme' => 'messodex',
            ],
        ],
        'reasons' => [
            [
                'title' => 'Industry Expertise & Proven Experience',
                'text' => 'With years of experience in organizing international exhibitions, trade shows, and corporate events, we understand what it takes to deliver world-class experiences. Our team combines deep industry knowledge with practical execution to ensure every event is impactful and successful.',
            ],
            [
                'title' => 'Strategic Global Network',
                'text' => 'Our strength lies in our extensive global network of partners, exhibitors, investors, and industry leaders. We actively collaborate with international stakeholders across Europe, Asia, and Africa to bring world-class participation to our events. This global reach allows our clients to access new markets, form strategic alliances, and expand their business footprint beyond borders.',
            ],
            [
                'title' => 'Innovation & Creative Excellence',
                'text' => 'Innovation is at the core of everything we do. We leverage modern event technologies, creative concepts, and forward-thinking strategies to design experiences that captivate audiences and deliver results. From immersive exhibition layouts to digital engagement tools, we ensure every event is fresh, relevant, and future-focused.',
            ],
            [
                'title' => 'Results-Driven Approach',
                'text' => 'Every project we handle is designed to achieve clear objectives, whether it is brand visibility, lead generation, or market expansion. We focus on delivering real value and measurable outcomes for our clients.',
            ],
            [
                'title' => 'End-to-End Event Solutions',
                'text' => 'From concept development to execution, we provide complete event management services. We handle planning, coordination, marketing, and on-site management, allowing our clients to focus on their core business.',
            ],
            [
                'title' => 'Strong Market Presence in Africa',
                'text' => 'With a deep understanding of the African market, we create events that are relevant, results-driven, and tailored to the region\'s unique opportunities and challenges.',
            ],
            [
                'title' => 'Client-Centered Excellence',
                'text' => 'Our clients are at the heart of everything we do. We prioritize professionalism, clear communication, and attention to detail, ensuring a seamless and satisfying experience from start to finish.',
            ],
        ],
    ];
}

function rcc_get_about_data()
{
    return [
        'hero_title' => 'Shaping Africa\'s Premier Events & Exhibitions',
        'hero_text' => 'Connecting Industries. Showcasing Innovation. Creating Lasting Opportunities.',
        'intro' => 'We are a leading event management and exhibition company dedicated to creating unforgettable experiences for businesses, brands, and communities across Africa.',
        'who' => 'Radiant Creative Concepts Limited (RCCL) is a dynamic and innovative event management company specializing in the planning and execution of international exhibitions, trade shows, corporate events, social events, product launches, conferences, workshops, virtual & hybrid events, and cultural celebrations. With creativity, precision, and global-standard professionalism, we bring ideas to life, beautifully and seamlessly.',
        'services' => [
            'International Exhibitions & Trade Shows',
            'Corporate & Business Events',
            'Social & Cultural Events',
            'Product Launch & Brand Activations',
            'Event Planning & Project Management',
            'Stage, Sound, and Technical Setup',
            'Vendor Coordination & Logistics Management',
        ],
        'why' => [
            'A team of certified and experienced event strategists',
            'Innovative and creative event solutions',
            'Strong vendor and industry partnerships',
            'Proven experience organizing large-scale exhibitions',
            'Professionalism, excellence, and timely delivery',
        ],
        'vision' => 'To become Africa\'s most innovative and trusted events and exhibitions company, connecting brands to opportunities and transforming ideas into exceptional experiences.',
        'mission' => 'To deliver world-class events through excellence, professionalism, and creativity while providing clients with seamless planning, coordination, and execution.',
        'values' => [
            'Radiance - We bring light, visibility, and excellence to every event.',
            'Creativity - Innovative ideas that stand out.',
            'Professionalism - Quality service delivery with integrity.',
            'Collaboration - Working with clients to make dreams real.',
            'Impact - Creating experiences that inspire and transform.',
        ],
        'difference' => [
            'Strong experience in international exhibitions and trade fairs',
            'End-to-end planning and execution',
            'Modern event technology and stage solutions',
            'A dedicated project team for each event',
            'Reliable vendor and supplier network',
            'Tailored solutions for each client\'s needs',
        ],
    ];
}

function rcc_get_services_data()
{
    return [
        [
            'title' => 'Exhibition & Trade Show Management',
            'text' => 'We plan and organize large-scale international expos, trade fairs, and exhibitions across different industries.',
            'items' => ['Booth sales & exhibitor management', 'Floor plan design & venue layout', 'Marketing & promotion', 'Registration & attendee management', 'On-site operations & logistics', 'Post-event reporting & analytics'],
        ],
        [
            'title' => 'Corporate Event Planning',
            'text' => 'Perfectly structured and professionally delivered corporate events.',
            'items' => ['Conferences & Seminars', 'Annual General Meetings (AGM)', 'Product Launches', 'Brand Activations', 'Press Conferences', 'Award Nights', 'Business Networking Events'],
        ],
        [
            'title' => 'Social & Cultural Events',
            'text' => 'We create beautiful, memorable experiences for life and community moments.',
            'items' => ['Weddings & Engagements', 'Birthdays & Anniversaries', 'Cultural Celebrations', 'Social Gatherings', 'Charity Events', 'Community Festivals'],
        ],
        [
            'title' => 'Technical & Production Services',
            'text' => 'Professional setup and execution for seamless events.',
            'items' => ['Stage & Lighting', 'Sound & Audio Engineering', 'LED Screens & Projection', 'Event Decor & Ambience', 'Power & Technical Support'],
        ],
        [
            'title' => 'Event Consulting & Project Management',
            'text' => 'We offer expert advisory and full event project management to help clients create well-structured, impactful, and budget-friendly events.',
            'items' => [],
        ],
    ];
}

function rcc_get_megastruct_data()
{
    return [
        'title'   => 'MEGASTRUCT AFRICA',
        'subtitle' => 'Mega Infrastructure, Construction & Mining Equipment Expo',
        'date'    => rcc_acf_option('mega_date',  '11th - 13th October 2026'),
        'venue'   => rcc_acf_option('mega_venue', 'Landmark Centre, Victoria Island, Lagos, Nigeria'),
        'email'   => rcc_acf_option('mega_email', 'megastruct@radiantccafrica.com'),
        'phone'   => rcc_acf_option('mega_phone', '+234 903 491 4989'),
        'tagline' => 'Exhibit • Network • Sell • Collaborate',
        'about' => [
            'MEGASTRUCT AFRICA is West Africa\'s premier platform dedicated to the infrastructure, construction, and mining sectors. The expo brings together global manufacturers, industry leaders, investors, and decision-makers to explore opportunities, showcase innovations, and drive sustainable development across Africa.',
            'Positioned at the heart of one of Africa\'s fastest-growing economies, MEGASTRUCT AFRICA serves as a strategic gateway for companies looking to enter or expand within the African market. The event creates a dynamic environment for business networking, partnerships, product launches, and knowledge exchange.',
        ],
        'highlights' => [
            'International exhibition featuring top global and local brands',
            'Live product demonstrations of machinery and equipment',
            'High-level networking with industry professionals and stakeholders',
            'B2B meetings and deal-making opportunities',
            'Conference sessions, panel discussions, and expert-led masterclasses',
            'Government and private sector participation',
            'Market insights into Africa\'s infrastructure and construction landscape',
        ],
        'exhibitors' => [
            'Construction machinery and heavy equipment',
            'Building materials and finishing products',
            'Mining equipment and technology',
            'Road construction and asphalt solutions',
            'Power, energy, and electrical systems',
            'Smart infrastructure and construction technology (ConTech)',
            'Engineering, architecture, and project management services',
            'Safety, security, and environmental solutions',
            'Logistics, transport, and material handling equipment',
            'Financial institutions and project financing organizations',
        ],
        'visitors' => [
            'Construction companies and contractors',
            'Real estate developers and property investors',
            'Government agencies and policymakers',
            'Engineers, architects, and consultants',
            'Procurement and project managers',
            'Mining companies and operators',
            'Distributors, dealers, and importers',
            'Financial institutions and investors',
            'Industry associations and trade organizations',
        ],
        'booths' => [
            ['title' => 'Shell Scheme (Standard Booth)', 'text' => 'A ready-to-use exhibition booth that includes a basic fascia structure, wall panels, a fascia board, lighting, carpet, and power supply, ideal for companies seeking a convenient and professional setup.'],
            ['title' => 'Raw Space', 'text' => 'An open exhibition space that allows exhibitors to design and build their own customized booth from scratch, giving full creative control and maximum brand expression.'],
            ['title' => 'Custom Booth', 'text' => 'A fully customized, professionally designed stand tailored to your brand identity and objectives. Our team can assist in creating a unique and visually striking presence that stands out on the exhibition floor.'],
        ],
        'sponsorship' => [
            'Title Sponsorship (PLATINUM, GOLD & SILVER)',
            'Conference & Stage Sponsorship',
            'Lanyard & Badge Branding',
            'Networking Lounge Branding',
            'LED Screen Advertising',
            'Product Demo Sessions',
        ],
        'why_exhibit' => [
            'Position your brand in front of thousands of decision-makers, engineers, contractors, and investors across West Africa.',
            'Showcase your latest construction machinery, building materials, mining equipment, or infrastructure solutions live on the exhibition floor.',
            'Connect with government agencies, real estate developers, and private sector players driving Africa\'s infrastructure boom.',
            'Secure new distributors, dealers, and long-term business partners across West Africa and beyond.',
            'Leverage Africa\'s fastest-growing construction market, projected to see massive infrastructure investment through 2030.',
            'Gain premium brand visibility through sponsorships, LED screens, and product demonstration sessions.',
        ],
    ];
}

function rcc_get_messodex_data()
{
    return [
        'title'   => 'MESSODEX WEST AFRICA',
        'subtitle' => 'Premier Media, Stage, & Sound Technology Expo',
        'date'    => rcc_acf_option('messo_date',  '19-21 August 2026'),
        'venue'   => rcc_acf_option('messo_venue', 'Landmark Centre, Victoria Island, Lagos, Nigeria'),
        'email'   => rcc_acf_option('messo_email', 'messodex@radiantccafrica.com'),
        'phone'   => rcc_acf_option('messo_phone', '+234 903 491 4989'),
        'intro'   => 'Join the largest and most influential platform connecting global leaders in media technology, professional audio, lighting, broadcasting solutions, stage engineering, creative production, and live entertainment equipment with the fast-growing West African market.',
        'tagline' => 'Exhibit • Network • Sell • Collaborate',
        'about' => [
            'Welcome to Africa\'s New Epicentre for Media, Stage & Sound Innovation.',
            'MESSODEX West Africa is an international exhibition and conference dedicated to showcasing cutting-edge technologies in Media, Broadcasting, Professional Audio, Lighting, Stage Engineering, Production, Events, Entertainment, and Creative Technology.',
            'MESSODEX is positioned to become the most comprehensive gateway for global manufacturers and solution providers seeking to enter, expand, or strengthen their presence in the African market.',
            'With Africa\'s entertainment, media, and live production industry growing rapidly, the demand for advanced equipment, modern technology, and professional expertise is at an all-time high. MESSODEX fills this gap by bringing innovators, buyers, system integrators, distributors, creative professionals, and decision-makers under one roof.',
        ],
        'matters' => [
            'Africa\'s live events and entertainment industry is projected to surpass $11 billion by 2027.',
            'Nigeria remains the continent\'s biggest hub for music, film, broadcasting, content creation, and mega-events.',
            'West Africa imports nearly 90% of its professional media, sound, and stage equipment.',
            'There is a strong market demand for studio equipment, broadcast systems, lighting technologies, acoustic solutions, staging tools, LED screens, and advanced production gear.',
        ],
        'why_exhibit' => [
            'Connect with verified buyers from event production companies, broadcast stations, venues, music studios, distributors, importers, worship centers, government agencies, and creative companies.',
            'Showcase your latest innovations live, from sound systems to video walls, stage technologies, DJ gear, microphones, mixers, lights, and broadcast hardware.',
            'Meet new distributors, OEM partners, long-term representatives, and regional dealers looking for serious market opportunities.',
            'Expand into Africa\'s fastest-growing creative hub across music, film, live production, content creation, and digital storytelling.',
            'Position your company as a market leader with stronger brand visibility, dominance, and competitive advantage.',
        ],
        'exhibitors' => [
            'Media Production & Broadcast Technology Providers',
            'Video & Film Equipment Exhibitors',
            'Sound Technology Specialists',
            'Post-Production & Editing Solutions Companies',
            'Stage Technology & Trussing Experts',
            'Stage Lighting, Effects & Control Systems Suppliers',
            'Professional Audio & Sound Reinforcement Providers',
            'AV Integration & Display Technology Firms',
            'Stage LED Display Solutions',
            'Special Effects & Event Technology Exhibitors',
            'Studio & Recording Equipment Providers',
            'Live Streaming & Content Delivery Services',
            'Lighting & Sound Design Solutions Companies',
            'Event Production & Technical Service Providers',
            'Professional Lighting & Audio (Pro Light & Pro Audio) Suppliers',
        ],
        'visitors' => ['Broadcasting corporations', 'Cinematographers', 'Music & film producers', 'Technical directors', 'Sound engineers', 'Event venues', 'Manufacturers & integrators', 'Importers & wholesalers', 'Government cultural agencies', 'Worship centers', 'Media houses', 'Creative industry professionals'],
        'conference' => ['Industry keynote sessions', 'Technical workshops', 'Product demonstrations', 'Case-study masterclasses', 'Fireside chats with global experts'],
        'package' => ['Free listing on website & catalogue', 'Business matchmaking opportunities', 'Access to VIP networking sessions', 'Brand visibility packages', 'Access to visitor database (post-event)'],
        'sponsorship' => ['Title Sponsorship (PLATINUM, GOLD & SILVER)', 'Conference & Stage Sponsorship', 'Lanyard & Badge Branding', 'Networking Lounge Branding', 'LED Screen Advertising', 'Product Demo Sessions'],
        'booths' => [
            ['title' => 'Shell Scheme (Standard Booth)', 'text' => 'A ready-to-use exhibition booth that includes a basic fascia structure, wall panels, a fascia board, lighting, carpet, and power supply — ideal for companies seeking a convenient and professional setup.'],
            ['title' => 'Raw Space', 'text' => 'An open exhibition space that allows exhibitors to design and build their own customized booth from scratch, giving full creative control and maximum brand expression.'],
            ['title' => 'Custom Booth', 'text' => 'A fully customized, professionally designed stand tailored to your brand identity and objectives. Our team can assist in creating a unique and visually striking presence on the exhibition floor.'],
        ],
    ];
}

function rcc_notice_markup($sent = false, $failed = false)
{
    if ($sent) {
        echo '<div class="rcc-notice rcc-notice-success">Your enquiry has been sent successfully. We will get back to you shortly.</div>';
    }
    if ($failed) {
        echo '<div class="rcc-notice rcc-notice-error">We could not send your enquiry right now. Please try again or email us directly at info@radiantccafrica.com.</div>';
    }
}

function rcc_render_list($items)
{
    echo '<ul class="rcc-list">';
    foreach ($items as $item) {
        echo '<li>' . esc_html($item) . '</li>';
    }
    echo '</ul>';
}

function rcc_get_booth_gallery($event_key)
{
    $saved_json = get_option('rcc_opt_' . $event_key . '_booth_images', '');
    if ($saved_json) {
        $arr = json_decode($saved_json, true);
        if (is_array($arr) && !empty($arr)) {
            return $arr;
        }
    }
    // Fallback: use BOOTH.png
    $fallback = rcc_upload_asset_candidates(['BOOTH.png']);
    return $fallback ? [$fallback] : [];
}

function rcc_preloader_images()
{
    return [
        'rcc_logo'   => rcc_get_logo_url('desktop'),
        'mega_logo'  => rcc_get_logo_url('mega'),
        'messo_logo' => rcc_get_logo_url('messo'),
    ];
}

function rcc_render_homepage()
{
    $data = rcc_get_home_data();
    $site = rcc_site_config();
    $sent = isset($_GET['sent']) && $_GET['sent'] === '1';
    $failed = isset($_GET['sent']) && $_GET['sent'] === '0';
    ?>
    <section class="rcc-home-hero" style="background-image:url('<?php echo esc_url($data['hero_image']); ?>');">
        <div class="rcc-shell">
            <div class="rcc-home-hero__content">
                <h1>Organizing Africa&apos;s Leading Exhibitions &amp; Trade Shows</h1>
                <p class="rcc-home-hero__tagline">Building Connections, Creating Opportunities.</p>
                <div class="rcc-actions rcc-actions--hero">
                    <?php rcc_button('Learn More', rcc_get_page_url('about-us')); ?>
                </div>
            </div>
        </div>
    </section>

    <section class="rcc-home-events">
        <div class="rcc-shell">
            <div class="rcc-section-title rcc-reveal">
                <span class="rcc-section-tag">Upcoming &amp; Featured</span>
                <h2>Our Featured Events</h2>
                <div class="rcc-section-divider"></div>
            </div>
            <div class="rcc-home-events__grid">
                <?php foreach ($data['events'] as $event) : ?>
                    <article class="rcc-event-card-premium rcc-event-card-premium--<?php echo esc_attr($event['theme']); ?> rcc-reveal">
                        <div class="rcc-event-card-premium__image-wrap">
                            <img src="<?php echo esc_url($event['image']); ?>" alt="<?php echo esc_attr($event['title']); ?>">
                        </div>
                        <div class="rcc-event-card-premium__body">
                            <h2><?php echo esc_html($event['title']); ?></h2>
                            <p class="rcc-event-card-premium__subtitle"><?php echo esc_html($event['subtitle']); ?></p>
                            <p><?php echo esc_html($event['description']); ?></p>
                            <?php rcc_button('View Event', $event['url']); ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="rcc-section rcc-section--soft">
        <div class="rcc-shell">
            <div class="rcc-why-outer">
                <div class="rcc-why-left rcc-reveal">
                    <span class="rcc-section-tag">Our Strengths</span>
                    <h2>Why Choose<br>Radiant<br><span>Creative Concepts?</span></h2>
                    <div class="rcc-why-divider"></div>
                    <p>We are more than an events company. We are architects of opportunity, building platforms that connect Africa to the world.</p>
                </div>
                <div class="rcc-why-list">
                    <div class="rcc-why-item rcc-reveal">
                        <div class="rcc-why-icon">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                        </div>
                        <div class="rcc-why-text">
                            <h3>Experience</h3>
                            <p><?php echo esc_html($data['reasons'][0]['text']); ?></p>
                        </div>
                    </div>
                    <div class="rcc-why-item rcc-reveal">
                        <div class="rcc-why-icon">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.22 4.22l2.12 2.12M17.66 17.66l2.12 2.12M2 12h3M19 12h3M4.22 19.78l2.12-2.12M17.66 6.34l2.12-2.12"/></svg>
                        </div>
                        <div class="rcc-why-text">
                            <h3>Innovation</h3>
                            <p><?php echo esc_html($data['reasons'][2]['text']); ?></p>
                        </div>
                    </div>
                    <div class="rcc-why-item rcc-reveal">
                        <div class="rcc-why-icon">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        </div>
                        <div class="rcc-why-text">
                            <h3>Global Network</h3>
                            <p><?php echo esc_html($data['reasons'][1]['text']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rcc-home-contact">
        <div class="rcc-shell">
            <div class="rcc-home-contact__grid">
                <div class="rcc-home-contact__form">
                    <span class="rcc-section-tag">Reach Out</span>
                    <h2>Get In <span>Touch</span></h2>
                    <p class="rcc-contact-sub">Have an exhibition idea? Looking to sponsor or exhibit? We&apos;d love to hear from you.</p>
                    <?php rcc_notice_markup($sent, $failed); ?>
                    <form class="rcc-form-card rcc-form-card--dark" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="rcc_submit_enquiry">
                        <input type="hidden" name="form_type" value="General Contact">
                        <?php wp_nonce_field('rcc_submit_enquiry', 'rcc_nonce'); ?>
                        <label>Full Name<input type="text" name="contact_name" required placeholder="Your full name"></label>
                        <label>Your Email<input type="email" name="email" required placeholder="your@email.com"></label>
                        <label>Message<textarea name="message" rows="5" required placeholder="How can we help?"></textarea></label>
                        <button class="rcc-submit" type="submit">Send Message</button>
                    </form>
                </div>
                <div class="rcc-home-contact__details">
                    <h3>Contact Details</h3>
                    <div class="rcc-contact-detail">
                        <strong>Phone</strong>
                        <span><?php echo esc_html($site['phone']); ?></span>
                    </div>
                    <div class="rcc-contact-detail">
                        <strong>Email</strong>
                        <span><?php echo esc_html($site['email']); ?></span>
                    </div>
                    <div class="rcc-contact-detail">
                        <strong>Web</strong>
                        <span><?php echo esc_html($site['website']); ?></span>
                    </div>
                    <div class="rcc-contact-detail">
                        <strong>Address</strong>
                        <span><?php echo esc_html($site['address']); ?></span>
                    </div>
                    <div class="rcc-home-contact__socials">
                        <h4>Follow Us</h4>
                        <div class="rcc-footer__socials">
                            <?php foreach ($site['socials'] as $social) : ?>
                                <a href="<?php echo esc_url($social['url']); ?>" class="rcc-social-badge"><?php echo esc_html($social['label']); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
}

function rcc_render_about_page()
{
    $data = rcc_get_about_data();
    $site = rcc_site_config();

    // Override data with ACF page fields when set
    if (function_exists('get_field')) {
        $about_page = get_page_by_path('about-us');
        if ($about_page) {
            $pid = $about_page->ID;
            if (get_field('about_intro',   $pid)) { $data['intro']   = get_field('about_intro',   $pid); }
            if (get_field('about_who',     $pid)) { $data['who']     = get_field('about_who',     $pid); }
            if (get_field('about_vision',  $pid)) { $data['vision']  = get_field('about_vision',  $pid); }
            if (get_field('about_mission', $pid)) { $data['mission'] = get_field('about_mission', $pid); }
        }
    }
    $sent = isset($_GET['sent']) && $_GET['sent'] === '1';
    $failed = isset($_GET['sent']) && $_GET['sent'] === '0';
    ?>
    <section class="rcc-page-hero">
        <div class="rcc-shell">
            <div class="rcc-page-hero__inner">
                <span class="rcc-kicker">Who We Are</span>
                <h1>Radiant Creative<br>Concepts Limited</h1>
                <div class="rcc-page-hero__divider"></div>
                <p><strong>Shaping Africa&apos;s Premier Events &amp; Exhibitions</strong><br>Connecting Industries. Showcasing Innovation. Creating Lasting Opportunities.</p>
            </div>
        </div>
    </section>

    <!-- ═══ COMPANY PROFILE ═══ -->
    <section class="rcc-section">
        <div class="rcc-shell">
            <div class="rcc-about-profile rcc-reveal">
                <div class="rcc-about-profile__text">
                    <span class="rcc-section-tag">Company Profile</span>
                    <h2 class="rcc-about-h2">Who We Are</h2>
                    <div class="rcc-section-divider" style="margin:0 0 1.5rem;"></div>
                    <p><?php echo esc_html($data['intro']); ?></p>
                    <p><?php echo esc_html($data['who']); ?></p>
                </div>
                <div class="rcc-about-profile__services">
                    <h3 class="rcc-about-sub-h3">Our Core Services</h3>
                    <?php rcc_render_list($data['services']); ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ EVENT CATEGORIES ═══ -->
    <section class="rcc-section rcc-section--soft">
        <div class="rcc-shell">
            <div class="rcc-section-title rcc-reveal">
                <span class="rcc-section-tag">What We Do</span>
                <h2>Event Categories</h2>
                <div class="rcc-section-divider"></div>
                <p>From international trade fairs to intimate corporate gatherings &mdash; we deliver every format with excellence.</p>
            </div>
            <div class="rcc-about-cats rcc-reveal">
                <div class="rcc-about-cat-card">
                    <div class="rcc-about-cat-icon">
                        <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                    </div>
                    <h3>Corporate Events</h3>
                    <p>Conferences, seminars, AGMs, product launches, brand activations, press conferences, and award nights executed with precision and style.</p>
                </div>
                <div class="rcc-about-cat-card">
                    <div class="rcc-about-cat-icon">
                        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <h3>Exhibitions &amp; Trade Shows</h3>
                    <p>International-standard expo platforms that connect global manufacturers, distributors, and investors to the African market.</p>
                </div>
                <div class="rcc-about-cat-card">
                    <div class="rcc-about-cat-icon">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    </div>
                    <h3>Virtual &amp; Social Events</h3>
                    <p>Hybrid and virtual conferences, live-streamed events, weddings, cultural celebrations, and community festivals with full technical production.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ VISION & MISSION ═══ -->
    <section class="rcc-section">
        <div class="rcc-shell">
            <div class="rcc-section-title rcc-reveal">
                <span class="rcc-section-tag">Our Direction</span>
                <h2>Vision &amp; Mission</h2>
                <div class="rcc-section-divider"></div>
            </div>
            <div class="rcc-about-vm rcc-reveal">
                <div class="rcc-about-vm-card rcc-about-vm-card--vision">
                    <span class="rcc-about-vm-label">Vision</span>
                    <h3><?php echo esc_html($data['vision']); ?></h3>
                </div>
                <div class="rcc-about-vm-card rcc-about-vm-card--mission">
                    <span class="rcc-about-vm-label">Mission</span>
                    <h3><?php echo esc_html($data['mission']); ?></h3>
                </div>
            </div>
            <div class="rcc-about-values rcc-reveal">
                <h3 class="rcc-about-sub-h3" style="margin-bottom:1.25rem;">Our Core Values</h3>
                <div class="rcc-about-values-grid">
                    <?php foreach ($data['values'] as $value) :
                        $parts = explode(' - ', $value, 2); ?>
                        <div class="rcc-about-value-item">
                            <strong><?php echo esc_html($parts[0]); ?></strong>
                            <?php if (!empty($parts[1])) : ?>
                                <span><?php echo esc_html($parts[1]); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ EXPERIENCE & EXCELLENCE ═══ -->
    <section class="rcc-section rcc-section--soft">
        <div class="rcc-shell">
            <div class="rcc-section-title rcc-reveal">
                <span class="rcc-section-tag">Why Choose RCCL</span>
                <h2>Experience &amp; Excellence</h2>
                <div class="rcc-section-divider"></div>
                <p>A team of certified event strategists committed to delivering world-class experiences across Africa.</p>
            </div>
            <div class="rcc-about-diff rcc-reveal">
                <?php foreach ($data['difference'] as $item) : ?>
                    <div class="rcc-about-diff-item">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                        <span><?php echo esc_html($item); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══ CONTACT FOR EVENT PLANNING ═══ -->
    <section class="rcc-section">
        <div class="rcc-shell">
            <div class="rcc-about-contact-grid">
                <div class="rcc-reveal">
                    <span class="rcc-section-tag">Work With Us</span>
                    <h2 class="rcc-about-h2">Contact for<br>Event Planning</h2>
                    <div class="rcc-section-divider" style="margin:1rem 0 1.5rem;"></div>
                    <p style="color:var(--rcc-muted);font-size:0.9rem;line-height:1.8;margin-bottom:2rem;">Have a project in mind? Whether it&apos;s an exhibition, corporate event, or brand activation &mdash; let&apos;s make it happen.</p>
                    <?php rcc_notice_markup($sent, $failed); ?>
                    <form class="rcc-form-card rcc-form-card--dark" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="rcc_submit_enquiry">
                        <input type="hidden" name="form_type" value="Event Planning Enquiry">
                        <?php wp_nonce_field('rcc_submit_enquiry', 'rcc_nonce'); ?>
                        <div class="rcc-form-grid">
                            <label>Full Name<input type="text" name="contact_name" required placeholder="Your full name"></label>
                            <label>Phone / WhatsApp<input type="text" name="phone" required placeholder="+234 xxx xxx xxxx"></label>
                        </div>
                        <label>Email Address<input type="email" name="email" required placeholder="your@email.com"></label>
                        <label>Message<textarea name="message" rows="5" required placeholder="Tell us about your event..."></textarea></label>
                        <button class="rcc-submit" type="submit">Submit Request</button>
                    </form>
                </div>
                <div class="rcc-about-contact-info rcc-reveal">
                    <h3 class="rcc-about-sub-h3">Get in Touch</h3>
                    <div class="rcc-contact-detail">
                        <strong>Phone</strong>
                        <span><?php echo esc_html($site['phone']); ?></span>
                    </div>
                    <div class="rcc-contact-detail">
                        <strong>Email</strong>
                        <span><?php echo esc_html($site['email']); ?></span>
                    </div>
                    <div class="rcc-contact-detail">
                        <strong>Web</strong>
                        <span><?php echo esc_html($site['website']); ?></span>
                    </div>
                    <div class="rcc-contact-detail">
                        <strong>Address</strong>
                        <span><?php echo esc_html($site['address']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
}

function rcc_render_services_page()
{
    $services = rcc_get_services_data();
    ?>
    <section class="rcc-page-hero">
        <div class="rcc-shell">
            <div class="rcc-page-hero__inner">
                <span class="rcc-kicker">What We Offer</span>
                <h1>Our Event Solutions</h1>
                <div class="rcc-page-hero__divider"></div>
                <p>From exhibitions to technical production, we deliver complete event solutions built around strategy, professionalism, and measurable impact.</p>
            </div>
        </div>
    </section>

    <section class="rcc-section">
        <div class="rcc-shell">
            <div class="rcc-card-grid">
                <?php foreach ($services as $service) : ?>
                    <article class="rcc-card">
                        <h2><?php echo esc_html($service['title']); ?></h2>
                        <p><?php echo esc_html($service['text']); ?></p>
                        <?php if (!empty($service['items'])) : ?>
                            <?php rcc_render_list($service['items']); ?>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
}

function rcc_render_exhibitions_page()
{
    $home_data = rcc_get_home_data();
    ?>
    <section class="rcc-page-hero">
        <div class="rcc-shell">
            <div class="rcc-page-hero__inner">
                <span class="rcc-kicker">Upcoming &amp; Featured</span>
                <h1>Our Events</h1>
                <div class="rcc-page-hero__divider"></div>
                <p>Two high-impact expo platforms connecting global exhibitors with African industries, buyers, and opportunities.</p>
            </div>
        </div>
    </section>

    <section class="rcc-section">
        <div class="rcc-shell">
            <div class="rcc-home-events__grid">
                <?php foreach ($home_data['events'] as $event) : ?>
                    <article class="rcc-event-card-premium rcc-event-card-premium--<?php echo esc_attr($event['theme']); ?>">
                        <div class="rcc-event-card-premium__image-wrap">
                            <img src="<?php echo esc_url($event['image']); ?>" alt="<?php echo esc_attr($event['title']); ?>">
                        </div>
                        <div class="rcc-event-card-premium__body">
                            <h2><?php echo esc_html($event['title']); ?></h2>
                            <p class="rcc-event-card-premium__subtitle"><?php echo esc_html($event['subtitle']); ?></p>
                            <p><?php echo esc_html($event['description']); ?></p>
                            <p><strong>&#128197; <?php echo esc_html($event['meta']); ?></strong></p>
                            <?php rcc_button('View Event', $event['url']); ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
}

function rcc_render_event_page($slug)
{
    $data        = $slug === 'megastruct-africa' ? rcc_get_megastruct_data() : rcc_get_messodex_data();
    $is_mega     = $slug === 'megastruct-africa';
    $contact_url = rcc_get_page_url('contact-us');
    $site        = rcc_site_config();

    $logo = rcc_get_logo_url($is_mega ? 'mega' : 'messo');

    $hero_img = $is_mega
        ? rcc_get_event_visual_url('mega')
        : rcc_get_event_visual_url('messo');

    $booth_img = rcc_upload_asset_candidates(['BOOTH.png']);
    $theme_cls = $is_mega ? 'rcc-event-hero--mega' : 'rcc-event-hero--messo';

    $jsonld = [
        '@context'            => 'https://schema.org',
        '@type'               => 'ExhibitionEvent',
        'name'                => $data['title'],
        'description'         => $data['subtitle'] . '. ' . $data['date'] . ' at ' . $data['venue'],
        'image'               => $hero_img,
        'startDate'           => $is_mega ? '2026-10-13' : '2026-08-19',
        'endDate'             => $is_mega ? '2026-10-15' : '2026-08-21',
        'location'            => [
            '@type'   => 'Place',
            'name'    => 'Landmark Centre, Victoria Island',
            'address' => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => 'Landmark Centre, Victoria Island',
                'addressLocality' => 'Lagos',
                'addressRegion'   => 'Lagos State',
                'addressCountry'  => 'NG',
            ],
        ],
        'organizer'           => [
            '@type' => 'Organization',
            'name'  => 'Radiant Creative Concepts Limited',
            'url'   => $site['website_url'],
            'email' => $site['email'],
            'telephone' => $site['phone'],
        ],
        'eventStatus'         => 'https://schema.org/EventScheduled',
        'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
        'keywords'            => $is_mega
            ? 'infrastructure expo, construction exhibition, mining expo, Lagos, West Africa, MEGASTRUCT'
            : 'media expo, stage technology, sound expo, broadcasting, Lagos, West Africa, MESSODEX',
    ];
    ?>
    <script type="application/ld+json"><?php echo wp_json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>

    <div class="rcc-ev-page <?php echo esc_attr('rcc-ev-page--' . ($is_mega ? 'mega' : 'messo')); ?>">

    <!-- ═══ FULLSCREEN HERO ═══ -->
    <section class="rcc-event-hero <?php echo esc_attr($theme_cls); ?>"
             style="background-image:url('<?php echo esc_url($hero_img); ?>');">
        <div class="rcc-event-hero__overlay"></div>
        <div class="rcc-event-hero__inner rcc-shell">
            <img class="rcc-event-hero__brand-logo"
                 src="<?php echo esc_url($logo); ?>"
                 alt="<?php echo esc_attr($data['title']); ?> logo">
            <div class="rcc-event-hero__center">
                <?php if ($is_mega) : ?>
                    <h1 class="rcc-event-hero__title"><?php echo esc_html($data['title']); ?></h1>
                <?php endif; ?>
                <p class="rcc-event-hero__subtitle"><?php echo esc_html($data['subtitle']); ?></p>
                <div class="rcc-event-hero__meta">
                    <span>&#128197; <?php echo esc_html($data['date']); ?></span>
                    <span>&#128205; <?php echo esc_html($data['venue']); ?></span>
                </div>
                <p class="rcc-event-hero__tagline"><?php echo esc_html($data['tagline']); ?></p>
                <div class="rcc-event-hero__actions">
                    <?php rcc_button($is_mega ? 'Book a Stand' : 'Become an Exhibitor', $contact_url); ?>
                    <?php rcc_button('Download Brochure', $contact_url, 'rcc-btn-secondary'); ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ STICKY SECTION NAV ═══ -->
    <nav class="rcc-event-nav" aria-label="Page sections">
        <div class="rcc-shell">
            <a href="#about">About</a>
            <a href="#date-venue">Date &amp; Venue</a>
            <a href="#exhibitors">Exhibitor Profile</a>
            <a href="#visitors">Visitor Profile</a>
            <a href="#why-exhibit">Why Exhibit</a>
            <a href="#stands">Stand Options</a>
            <a href="#gallery">Booth Gallery</a>
        </div>
    </nav>

    <!-- ═══ ABOUT ═══ -->
    <section id="about" class="rcc-ev-section">
        <div class="rcc-shell">
            <div class="rcc-ev-about">
                <div class="rcc-ev-about__text rcc-reveal">
                    <span class="rcc-section-tag">About the Event</span>
                    <h2><?php echo esc_html($is_mega ? 'About MEGASTRUCT AFRICA' : 'About MESSODEX'); ?></h2>
                    <?php foreach ($data['about'] as $paragraph) : ?>
                        <p><?php echo esc_html($paragraph); ?></p>
                    <?php endforeach; ?>
                </div>
                <div class="rcc-ev-about__image rcc-reveal">
                    <img src="<?php echo esc_url($hero_img); ?>"
                         alt="<?php echo esc_attr($data['title']); ?>">
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ DATE & VENUE ═══ -->
    <section id="date-venue" class="rcc-ev-section rcc-ev-section--dark">
        <div class="rcc-shell">
            <div class="rcc-ev-section-header rcc-reveal">
                <span class="rcc-section-tag">When &amp; Where</span>
                <h2>Event Date &amp; Venue</h2>
            </div>
            <div class="rcc-ev-datevenue rcc-reveal">
                <div class="rcc-ev-datevenue__item">
                    <span class="rcc-ev-dv-icon">&#128197;</span>
                    <h3>Date</h3>
                    <p><?php echo esc_html($data['date']); ?></p>
                </div>
                <div class="rcc-ev-datevenue__item">
                    <span class="rcc-ev-dv-icon">&#128205;</span>
                    <h3>Venue</h3>
                    <p><?php echo esc_html($data['venue']); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ EXHIBITOR + VISITOR PROFILE ═══ -->
    <section class="rcc-ev-section">
        <div class="rcc-shell">
            <div class="rcc-ev-section-header rcc-reveal">
                <span class="rcc-section-tag">Who&apos;s Involved</span>
                <h2>Exhibition Profiles</h2>
            </div>
            <div class="rcc-ev-profiles">
                <div id="exhibitors" class="rcc-ev-profile-card rcc-reveal">
                    <h3>Exhibitor Profile</h3>
                    <?php rcc_render_list($data['exhibitors']); ?>
                </div>
                <div id="visitors" class="rcc-ev-profile-card rcc-reveal">
                    <h3>Visitor Profile</h3>
                    <?php rcc_render_list($data['visitors']); ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ WHY EXHIBIT ═══ -->
    <section id="why-exhibit" class="rcc-ev-section rcc-ev-section--dark">
        <div class="rcc-shell">
            <div class="rcc-ev-section-header rcc-reveal">
                <span class="rcc-section-tag">Reasons to Participate</span>
                <h2>Why Exhibit</h2>
            </div>
            <div class="rcc-ev-why-wrap rcc-reveal">
                <?php foreach ($data['why_exhibit'] as $reason) : ?>
                    <div class="rcc-ev-why-item">
                        <p><?php echo esc_html($reason); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══ STAND OPTIONS ═══ -->
    <section id="stands" class="rcc-ev-section">
        <div class="rcc-shell">
            <div class="rcc-ev-section-header rcc-reveal">
                <span class="rcc-section-tag">Participation Options</span>
                <h2>Stand Options</h2>
            </div>
            <div class="rcc-ev-stands">
                <?php foreach ($data['booths'] as $booth) : ?>
                    <div class="rcc-ev-stand-card rcc-reveal">
                        <h3><?php echo esc_html($booth['title']); ?></h3>
                        <p><?php echo esc_html($booth['text']); ?></p>
                        <?php rcc_button('Enquire About This Stand', $contact_url, 'rcc-btn-outline'); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══ BOOTH GALLERY (CAROUSEL) ═══ -->
    <?php $booth_gallery = rcc_get_booth_gallery($is_mega ? 'mega' : 'messo'); ?>
    <?php if (!empty($booth_gallery)) : ?>
    <section id="gallery" class="rcc-ev-section rcc-ev-section--dark">
        <div class="rcc-shell">
            <div class="rcc-ev-section-header rcc-reveal">
                <span class="rcc-section-tag">See What&apos;s Possible</span>
                <h2>Booth Gallery</h2>
                <p>Explore exhibition setups and booth designs at our events.</p>
            </div>
            <div class="rcc-carousel rcc-reveal">
                <button class="rcc-carousel__btn rcc-carousel__prev" aria-label="Previous">&#8249;</button>
                <div class="rcc-carousel__track-wrap">
                    <div class="rcc-carousel__track">
                        <?php foreach ($booth_gallery as $bi => $booth_slide) :
                            if (!$booth_slide) { continue; } ?>
                            <div class="rcc-carousel__slide">
                                <img src="<?php echo esc_url($booth_slide); ?>"
                                     alt="Exhibition booth <?php echo esc_attr($bi + 1); ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button class="rcc-carousel__btn rcc-carousel__next" aria-label="Next">&#8250;</button>
                <div class="rcc-carousel__dots"></div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ═══ BOTTOM CTA ═══ -->
    <section class="rcc-ev-cta <?php echo esc_attr($theme_cls); ?>">
        <div class="rcc-shell rcc-ev-cta__inner">
            <h2><?php echo $is_mega ? 'Ready to Exhibit at MEGASTRUCT AFRICA?' : 'Ready to Exhibit at MESSODEX WEST AFRICA?'; ?></h2>
            <p><?php echo $is_mega
                ? 'Secure your stand at West Africa\'s premier infrastructure &amp; construction expo.'
                : 'Secure your stand at West Africa\'s premier media &amp; stage technology expo.'; ?></p>
            <div class="rcc-actions">
                <?php rcc_button($is_mega ? 'Book a Stand' : 'Become an Exhibitor', $contact_url); ?>
                <?php rcc_button('Download Brochure', $contact_url, 'rcc-btn-secondary'); ?>
            </div>
        </div>
    </section>

    <!-- ═══ CONTACT & SOCIAL ═══ -->
    <section class="rcc-ev-contacts">
        <div class="rcc-shell">
            <div class="rcc-ev-contacts__grid">
                <div class="rcc-ev-contact-group">
                    <h3>Radiant Creative</h3>
                    <p>&#9993; <?php echo esc_html($site['email']); ?></p>
                    <p>&#9742; <?php echo esc_html($site['phone']); ?></p>
                </div>
                <div class="rcc-ev-contact-group">
                    <h3>MEGASTRUCT</h3>
                    <p>&#9993; <?php echo esc_html(rcc_acf_option('mega_email', 'megastruct@radiantccafrica.com')); ?></p>
                    <p>&#9742; <?php echo esc_html(rcc_acf_option('mega_phone', $site['phone'])); ?></p>
                </div>
                <div class="rcc-ev-contact-group">
                    <h3>MESSODEX</h3>
                    <p>&#9993; <?php echo esc_html(rcc_acf_option('messo_email', 'messodex@radiantccafrica.com')); ?></p>
                    <p>&#9742; <?php echo esc_html(rcc_acf_option('messo_phone', $site['phone'])); ?></p>
                </div>
                <div class="rcc-ev-contact-social">
                    <h3>Follow Us</h3>
                    <div class="rcc-footer__socials">
                        <?php foreach ($site['socials'] as $social) : ?>
                            <a href="<?php echo esc_url($social['url']); ?>"
                               class="rcc-social-badge"><?php echo esc_html($social['label']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    </div><!-- /.rcc-ev-page -->
    <?php
}

function rcc_render_book_a_stand_page()
{
    $sent = isset($_GET['sent']) && $_GET['sent'] === '1';
    $failed = isset($_GET['sent']) && $_GET['sent'] === '0';
    ?>
    <section class="rcc-page-hero">
        <div class="rcc-shell">
            <div class="rcc-page-hero__inner">
                <span class="rcc-kicker">Participate</span>
                <h1>Book a Stand</h1>
                <div class="rcc-page-hero__divider"></div>
                <p>Tell us about your business, preferred event, and booth size. Our team will respond with the next steps.</p>
            </div>
        </div>
    </section>

    <section class="rcc-section">
        <div class="rcc-shell rcc-form-wrap">
            <?php rcc_notice_markup($sent, $failed); ?>
            <form class="rcc-form-card" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="rcc_submit_enquiry">
                <input type="hidden" name="form_type" value="Book a Stand">
                <?php wp_nonce_field('rcc_submit_enquiry', 'rcc_nonce'); ?>
                <div class="rcc-form-section">
                    <h2>Exhibition Interest</h2>
                    <div class="rcc-choice-grid">
                        <label><input type="checkbox" name="events[]" value="MESSODEX WEST AFRICA 2026">MESSODEX WEST AFRICA 2026</label>
                        <label><input type="checkbox" name="events[]" value="MEGASTRUCT AFRICA 2026">MEGASTRUCT AFRICA 2026</label>
                    </div>
                </div>
                <div class="rcc-form-grid">
                    <label>Nature of Business<input type="text" name="nature_of_business" required></label>
                    <label>Company Name<input type="text" name="company_name" required></label>
                    <label>Country<input type="text" name="country" required></label>
                    <label>Company Website<input type="text" name="company_website" placeholder="https://www.example.com"></label>
                    <label>Contact Person: Full Name<input type="text" name="contact_name" required></label>
                    <label>Job Title<input type="text" name="job_title"></label>
                    <label>Email Address<input type="email" name="email" required></label>
                    <label>Phone/WhatsApp<input type="text" name="phone" required></label>
                </div>
                <div class="rcc-form-section">
                    <h2>Preferred Booth</h2>
                    <div class="rcc-choice-grid rcc-choice-grid--compact">
                        <?php foreach (['12sqm','15sqm','18sqm','21sqm','24sqm','36sqm','54sqm','60sqm','72sqm','90sqm','Others'] as $size) : ?>
                            <label><input type="radio" name="booth_size" value="<?php echo esc_attr($size); ?>" <?php checked($size, '12sqm', false); ?>><?php echo esc_html($size); ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <label>Additional Notes<textarea name="message" rows="5" placeholder="Tell us any booth, sponsorship, or exhibition requirements."></textarea></label>
                <button class="rcc-submit" type="submit">Submit Enquiry</button>
            </form>
        </div>
    </section>
    <?php
}

function rcc_gallery_get_images()
{
    $extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    // Helper: scan a folder and return image entries
    $scan_folder = function ($dir, $base_url) use ($extensions) {
        $result = [];
        if (!is_dir($dir)) {
            return $result;
        }
        $files = @scandir($dir);
        if (!$files) {
            return $result;
        }
        sort($files); // alphabetical so order is predictable
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, $extensions, true)) {
                continue;
            }
            $result[] = [
                'url' => trailingslashit($base_url) . $file,
                'alt' => pathinfo($file, PATHINFO_FILENAME),
            ];
        }
        return $result;
    };

    // 1. Admin-uploaded images via RCC Settings -> Gallery page
    $saved_json = get_option('rcc_opt_gallery_images', '');
    if ($saved_json) {
        $saved = json_decode($saved_json, true);
        if (is_array($saved) && !empty($saved)) {
            $result = [];
            foreach ($saved as $item) {
                $url = is_string($item) ? $item : ($item['url'] ?? '');
                $alt = is_string($item) ? '' : ($item['alt'] ?? '');
                if ($url) {
                    $result[] = ['url' => $url, 'alt' => $alt];
                }
            }
            if (!empty($result)) {
                return $result;
            }
        }
    }

    // 2. Theme assets/gallery/ folder fallback
    $gallery_dir = get_template_directory() . '/assets/gallery';
    $gallery_url = get_template_directory_uri() . '/assets/gallery';
    $theme_gallery = $scan_folder($gallery_dir, $gallery_url);
    if (!empty($theme_gallery)) {
        return $theme_gallery;
    }

    // 3. ACF gallery field (if ACF is active)
    if (function_exists('get_field')) {
        $gallery_page = get_page_by_path('gallery');
        if ($gallery_page) {
            $acf = get_field('gallery_images', $gallery_page->ID) ?: [];
            if (!empty($acf)) {
                $result = [];
                foreach ($acf as $img) {
                    $url = is_array($img) ? ($img['sizes']['large'] ?? $img['url'] ?? '') : $img;
                    $alt = is_array($img) ? ($img['alt'] ?? '') : '';
                    if ($url) {
                        $result[] = ['url' => $url, 'alt' => $alt];
                    }
                }
                if (!empty($result)) {
                    return $result;
                }
            }
        }
    }

    return [];
}

function rcc_render_gallery_page()
{
    $images = rcc_gallery_get_images();
    ?>
    <section class="rcc-page-hero">
        <div class="rcc-shell">
            <div class="rcc-page-hero__inner">
                <span class="rcc-kicker">Media</span>
                <h1>Gallery</h1>
                <div class="rcc-page-hero__divider"></div>
                <p>Booth designs, live event moments, and partner visibility from our flagship exhibitions.</p>
            </div>
        </div>
    </section>

    <section class="rcc-section">
        <div class="rcc-shell">
            <?php if (!empty($images)) : ?>
                <div class="rcc-masonry-gallery">
                    <?php foreach ($images as $img) : ?>
                        <div class="rcc-masonry-gallery__item">
                            <img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p style="text-align:center;color:var(--rcc-muted);padding:3rem 0;">
                    No gallery images yet. Go to <strong>RCC Settings &rarr; Gallery</strong> in the WordPress admin to upload photos.
                </p>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

function rcc_render_contact_page()
{
    $site = rcc_site_config();
    $sent = isset($_GET['sent']) && $_GET['sent'] === '1';
    $failed = isset($_GET['sent']) && $_GET['sent'] === '0';

    $contact_intro = 'Complete the form below and our team will get back to you with stand options, pricing, and next steps.';
    if (function_exists('get_field')) {
        $contact_page = get_page_by_path('contact-us');
        if ($contact_page) {
            $acf_intro = get_field('contact_intro', $contact_page->ID);
            if ($acf_intro) { $contact_intro = $acf_intro; }
        }
    }
    ?>
    <section class="rcc-page-hero">
        <div class="rcc-shell">
            <div class="rcc-page-hero__inner">
                <span class="rcc-kicker">Exhibit With Us</span>
                <h1>Exhibiting Enquiry</h1>
                <div class="rcc-page-hero__divider"></div>
                <p><?php echo esc_html($contact_intro); ?></p>
            </div>
        </div>
    </section>

    <section class="rcc-section">
        <div class="rcc-shell">
            <?php rcc_notice_markup($sent, $failed); ?>
            <div class="rcc-enquiry-layout">
                <form class="rcc-form-card" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="rcc_submit_enquiry">
                    <input type="hidden" name="form_type" value="Exhibiting Enquiry">
                    <?php wp_nonce_field('rcc_submit_enquiry', 'rcc_nonce'); ?>

                    <div class="rcc-form-section">
                        <h3>Exhibition Interest — Which event are you interested in? (Required)</h3>
                        <div class="rcc-choice-grid">
                            <label><input type="checkbox" name="events[]" value="MEGASTRUCT AFRICA"> MEGASTRUCT AFRICA</label>
                            <label><input type="checkbox" name="events[]" value="MESSODEX WEST AFRICA"> MESSODEX WEST AFRICA</label>
                            <label><input type="checkbox" name="events[]" value="Both"> Both</label>
                        </div>
                    </div>

                    <div class="rcc-form-section">
                        <h3>Nature of Business</h3>
                        <div class="rcc-choice-grid rcc-choice-grid--compact">
                            <?php foreach (['Manufacturer', 'Agent / Distributor / Supplier', 'Service Provider', 'Government / Municipality', 'Trade Association', 'Product / Service Buyer', 'Other'] as $type) : ?>
                                <label><input type="checkbox" name="nature_of_business[]" value="<?php echo esc_attr($type); ?>"> <?php echo esc_html($type); ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="rcc-form-grid">
                        <label>Company Name (Required)<input type="text" name="company_name" required></label>
                        <label>Country (Required)<input type="text" name="country" required></label>
                        <label>Company Website (Optional)<input type="text" name="company_website" placeholder="https://www.example.com"></label>
                        <label>Contact Person: Full Name (Required)<input type="text" name="contact_name" required></label>
                        <label>Job Title (Optional)<input type="text" name="job_title"></label>
                        <label>Email Address (Required)<input type="email" name="email" required></label>
                        <label class="rcc-form-full">Phone / WhatsApp No. (Required)<input type="text" name="phone" required></label>
                    </div>

                    <div class="rcc-form-section">
                        <h3>Preferred Booth Size (Required)</h3>
                        <div class="rcc-choice-grid rcc-choice-grid--compact">
                            <?php foreach (['12sqm','15sqm','18sqm','24sqm','36sqm','54sqm','60sqm','72sqm','90sqm','Others'] as $size) : ?>
                                <label><input type="radio" name="booth_size" value="<?php echo esc_attr($size); ?>"> <?php echo esc_html($size); ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button class="rcc-submit" type="submit">SUBMIT</button>
                </form>

                <aside class="rcc-enquiry-contact">
                    <h3>Contact Info</h3>
                    <div class="rcc-enquiry-contact__group">
                        <strong>RCC</strong>
                        <p>Email: <?php echo esc_html($site['email']); ?></p>
                        <p>Phone: <?php echo esc_html($site['phone']); ?></p>
                    </div>
                    <div class="rcc-enquiry-contact__group">
                        <strong>MEGASTRUCT</strong>
                        <p>Email: megastruct@radiantccafrica.com</p>
                        <p>Phone: <?php echo esc_html($site['phone']); ?></p>
                    </div>
                    <div class="rcc-enquiry-contact__group">
                        <strong>MESSODEX</strong>
                        <p>Email: messodex@radiantccafrica.com</p>
                        <p>Phone: <?php echo esc_html($site['phone']); ?></p>
                    </div>
                    <div class="rcc-home-contact__socials" style="margin-top:1.5rem;">
                        <h4>Social Media</h4>
                        <div class="rcc-footer__socials">
                            <?php foreach ($site['socials'] as $social) : ?>
                                <a href="<?php echo esc_url($social['url']); ?>" class="rcc-social-badge"><?php echo esc_html($social['label']); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
    <?php
}

function rcc_render_page_by_slug($slug)
{
    switch ($slug) {
        case '':
        case 'home':
            rcc_render_homepage();
            return true;
        case 'about':
        case 'about-us':
            rcc_render_about_page();
            return true;
        case 'services':
            rcc_render_services_page();
            return true;
        case 'events':
        case 'exhibitions':
            rcc_render_exhibitions_page();
            return true;
        case 'messodex-west-africa':
        case 'megastruct-africa':
            rcc_render_event_page($slug);
            return true;
        case 'book-a-stand':
            rcc_render_book_a_stand_page();
            return true;
        case 'gallery':
            rcc_render_gallery_page();
            return true;
        case 'contact':
        case 'contact-us':
            rcc_render_contact_page();
            return true;
    }

    return false;
}

function rcc_supported_slugs()
{
    return ['about', 'about-us', 'services', 'events', 'exhibitions', 'messodex-west-africa', 'megastruct-africa', 'book-a-stand', 'gallery', 'contact', 'contact-us'];
}

function rcc_get_request_slug()
{
    $path = wp_parse_url(home_url(add_query_arg([])), PHP_URL_PATH);
    $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
    $request_path = wp_parse_url($request_uri, PHP_URL_PATH);

    if ($path && strpos($request_path, $path) === 0) {
        $request_path = substr($request_path, strlen($path));
    }

    return trim((string) $request_path, '/');
}

function rcc_get_current_slug()
{
    if (is_front_page()) {
        return '';
    }

    $post = get_queried_object();
    if ($post instanceof WP_Post) {
        return $post->post_name;
    }

    return rcc_get_request_slug();
}

function rcc_handle_submit_enquiry()
{
    if (!isset($_POST['rcc_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rcc_nonce'])), 'rcc_submit_enquiry')) {
        wp_die('Invalid request.');
    }

    $site = rcc_site_config();
    $form_type = isset($_POST['form_type']) ? sanitize_text_field(wp_unslash($_POST['form_type'])) : 'Website Enquiry';
    $fields = [
        'Form Type' => $form_type,
        'Exhibition Interest' => isset($_POST['events']) ? implode(', ', array_map('sanitize_text_field', wp_unslash((array) $_POST['events']))) : '',
        'Nature of Business' => isset($_POST['nature_of_business']) ? implode(', ', array_map('sanitize_text_field', wp_unslash((array) $_POST['nature_of_business']))) : '',
        'Company Name' => isset($_POST['company_name']) ? sanitize_text_field(wp_unslash($_POST['company_name'])) : '',
        'Country' => isset($_POST['country']) ? sanitize_text_field(wp_unslash($_POST['country'])) : '',
        'Company Website' => isset($_POST['company_website']) ? esc_url_raw(wp_unslash($_POST['company_website'])) : '',
        'Contact Person' => isset($_POST['contact_name']) ? sanitize_text_field(wp_unslash($_POST['contact_name'])) : '',
        'Job Title' => isset($_POST['job_title']) ? sanitize_text_field(wp_unslash($_POST['job_title'])) : '',
        'Email Address' => isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '',
        'Phone/WhatsApp' => isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '',
        'Preferred Booth' => isset($_POST['booth_size']) ? sanitize_text_field(wp_unslash($_POST['booth_size'])) : '',
        'Message' => isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '',
    ];

    $body = [];
    foreach ($fields as $label => $value) {
        if ($value !== '') {
            $body[] = $label . ': ' . $value;
        }
    }

    $headers = [];
    if (!empty($fields['Email Address'])) {
        $headers[] = 'Reply-To: ' . $fields['Email Address'];
    }

    $recipient = 'info@radiantccafrica.com';
    if (!empty($site['email']) && is_email($site['email'])) {
        $recipient = $site['email'];
    }

    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    $headers[] = 'From: Radiant Creative Concepts <' . $recipient . '>';

    $sent = wp_mail($recipient, 'New Exhibition Enquiry - ' . $form_type, implode("\n\n", $body), $headers);
    $redirect = wp_get_referer() ? wp_get_referer() : home_url('/');
    $redirect = remove_query_arg(['sent'], $redirect);
    wp_safe_redirect(add_query_arg('sent', $sent ? '1' : '0', $redirect));
    exit;
}
add_action('admin_post_nopriv_rcc_submit_enquiry', 'rcc_handle_submit_enquiry');
add_action('admin_post_rcc_submit_enquiry', 'rcc_handle_submit_enquiry');

add_filter('template_include', function ($template) {
    if (!is_404()) {
        return $template;
    }

    $slug = rcc_get_request_slug();
    if (!in_array($slug, rcc_supported_slugs(), true)) {
        return $template;
    }

    status_header(200);
    global $wp_query;
    $wp_query->is_404 = false;

    return get_template_directory() . '/page.php';
});

/* ============================================================
   SEO — DYNAMIC META TAGS
   ============================================================ */
function rcc_seo_pages()
{
    $site = rcc_site_config();

    return [
        '' => [
            'title'       => 'Radiant Creative Concepts Limited | Events, Exhibitions & Trade Shows in Nigeria',
            'description' => 'Radiant Creative Concepts Limited is an event management and exhibitions company organizing corporate events, trade shows, and international exhibitions across Nigeria and Africa.',
            'image'       => rcc_upload_asset_candidates(['hero.png']),
            'url'         => home_url('/'),
            'type'        => 'website',
            'keywords'    => 'Radiant Creative Concepts Limited, RCC, event management Nigeria, exhibitions Nigeria, trade shows Africa, corporate events Lagos',
        ],
        'about-us' => [
            'title'       => 'About Radiant Creative Concepts Limited | Event & Exhibition Company',
            'description' => 'Learn about Radiant Creative Concepts Limited, our vision, mission, event expertise, and how we deliver premium exhibitions and corporate events across Africa.',
            'image'       => rcc_get_logo_url(),
            'url'         => rcc_get_page_url('about-us'),
            'type'        => 'article',
            'keywords'    => 'about Radiant Creative Concepts, event company Nigeria, exhibition organizers Africa',
        ],
        'exhibitions' => [
            'title'       => 'Our Events | Radiant Creative Concepts Limited',
            'description' => 'Explore RCC exhibitions and trade shows including MEGASTRUCT AFRICA and MESSODEX WEST AFRICA, connecting industries, buyers, exhibitors, and investors.',
            'image'       => rcc_get_event_visual_url('mega'),
            'url'         => rcc_get_page_url('exhibitions'),
            'type'        => 'website',
            'keywords'    => 'events by RCC, exhibitions Nigeria, trade shows Lagos, Megastruct Africa, Messodex West Africa',
        ],
        'gallery' => [
            'title'       => 'Gallery | Radiant Creative Concepts Limited',
            'description' => 'View gallery highlights from RCC exhibition booths, live events, stage production setups, and event brand experiences.',
            'image'       => rcc_upload_asset_candidates(['BOOTH.png']),
            'url'         => rcc_get_page_url('gallery'),
            'type'        => 'article',
            'keywords'    => 'event gallery Nigeria, exhibition booth gallery, RCC gallery',
        ],
        'contact-us' => [
            'title'       => 'Contact Radiant Creative Concepts Limited | Bookings & Exhibition Enquiries',
            'description' => 'Contact Radiant Creative Concepts Limited for bookings, partnerships, vendor collaboration, and exhibition stand enquiries.',
            'image'       => rcc_get_logo_url(),
            'url'         => rcc_get_page_url('contact-us'),
            'type'        => 'website',
            'keywords'    => 'contact RCC, exhibition enquiries Nigeria, event bookings Lagos',
        ],
        'book-a-stand' => [
            'title'       => 'Book a Stand | Radiant Creative Concepts Limited',
            'description' => 'Reserve exhibition space for MESSODEX WEST AFRICA or MEGASTRUCT AFRICA and connect with qualified buyers, partners, and industry leaders.',
            'image'       => rcc_upload_asset_candidates(['BOOTH.png']),
            'url'         => rcc_get_page_url('book-a-stand'),
            'type'        => 'website',
            'keywords'    => 'book exhibition stand Nigeria, trade fair booth booking, MESSODEX stand, MEGASTRUCT stand',
        ],
        'megastruct-africa' => [
            'title'       => 'MEGASTRUCT AFRICA 2026 | Infrastructure, Construction & Mining Expo in Lagos',
            'description' => 'MEGASTRUCT AFRICA 2026 is West Africa\'s premier infrastructure, construction and mining equipment expo holding at Landmark Centre, Victoria Island, Lagos, Nigeria.',
            'image'       => rcc_upload_asset_candidates(['MEGASTRUCT.png']),
            'url'         => rcc_get_page_url('megastruct-africa'),
            'type'        => 'event',
            'keywords'    => 'Megastruct Africa, construction expo Africa, mining equipment expo Nigeria, infrastructure exhibition Lagos',
        ],
        'messodex-west-africa' => [
            'title'       => 'MESSODEX WEST AFRICA 2026 | Media, Stage & Sound Technology Expo in Lagos',
            'description' => 'MESSODEX WEST AFRICA 2026 is West Africa\'s leading media, stage and sound technology expo for broadcast, AV, lighting, production, and live event innovation.',
            'image'       => rcc_get_event_visual_url('messo'),
            'url'         => rcc_get_page_url('messodex-west-africa'),
            'type'        => 'event',
            'keywords'    => 'Messodex West Africa, media technology expo Africa, sound exhibition Nigeria, stage lighting expo Lagos',
        ],
    ];
}

function rcc_get_seo_context()
{
    $slug = rcc_get_current_slug();
    $pages = rcc_seo_pages();

    if (is_front_page()) {
        return $pages[''];
    }

    return $pages[$slug] ?? null;
}

add_filter('document_title_parts', function ($parts) {
    $seo = rcc_get_seo_context();
    if ($seo && !empty($seo['title'])) {
        $parts['title'] = $seo['title'];
    }
    return $parts;
});

add_action('wp_head', function () {
    $seo = rcc_get_seo_context();
    if (!$seo) {
        return;
    }

    $site = rcc_site_config();
    $title = esc_attr($seo['title']);
    $description = esc_attr($seo['description']);
    $url = esc_url($seo['url']);
    $image = !empty($seo['image']) ? esc_url($seo['image']) : '';
    $svg_logo = rcc_theme_svg_logo_url();
    $org_logo = $svg_logo ?: rcc_get_logo_url();
    $type = $seo['type'] ?? 'website';

    echo '<meta name="description" content="' . $description . '">' . "\n";
    echo '<meta name="keywords" content="' . esc_attr($seo['keywords'] ?? '') . '">' . "\n";
    echo '<meta name="robots" content="index, follow, max-image-preview:large">' . "\n";
    echo '<link rel="canonical" href="' . $url . '">' . "\n";

    echo '<meta property="og:site_name" content="' . esc_attr($site['company']) . '">' . "\n";
    echo '<meta property="og:locale" content="en_NG">' . "\n";
    echo '<meta property="og:title" content="' . $title . '">' . "\n";
    echo '<meta property="og:description" content="' . $description . '">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr($type) . '">' . "\n";
    echo '<meta property="og:url" content="' . $url . '">' . "\n";
    if ($image) {
        echo '<meta property="og:image" content="' . $image . '">' . "\n";
    }

    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . $title . '">' . "\n";
    echo '<meta name="twitter:description" content="' . $description . '">' . "\n";
    if ($image) {
        echo '<meta name="twitter:image" content="' . $image . '">' . "\n";
    }

    if ($svg_logo) {
        echo '<link rel="icon" type="image/svg+xml" href="' . esc_url($svg_logo) . '">' . "\n";
    }

    $org_schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => $site['company'],
        'legalName'=> 'Radiant Creative Concepts Limited',
        'alternateName' => [
            'Radiant Creative Concepts Ltd.',
            'Radiant Creative Concepts',
            'RCC',
        ],
        'url'      => home_url('/'),
        'logo'     => $org_logo,
        'telephone'=> $site['phone'],
        'email'    => $site['email'],
        'address'  => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $site['address'],
            'addressLocality' => 'Mowe',
            'addressRegion'   => 'Ogun State',
            'addressCountry'  => 'NG',
        ],
        'sameAs'   => array_values(array_filter(array_map(function ($social) {
            return !empty($social['url']) && $social['url'] !== '#' ? $social['url'] : null;
        }, $site['socials']))),
    ];

    $website_schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => $site['company'],
        'alternateName' => [
            'Radiant Creative Concepts Limited',
            'Radiant Creative Concepts Ltd.',
        ],
        'url'      => home_url('/'),
        'publisher'=> [
            '@type' => 'Organization',
            'name'  => $site['company'],
            'logo'  => [
                '@type' => 'ImageObject',
                'url'   => $org_logo,
            ],
        ],
    ];

    echo '<script type="application/ld+json">' . wp_json_encode($org_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    echo '<script type="application/ld+json">' . wp_json_encode($website_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}, 5);

/* ============================================================
   RCC ADMIN — SETTINGS PAGES (WordPress Settings API)
   Works with both free and PRO ACF, or without ACF entirely.
   ============================================================ */

add_action('admin_menu', function () {
    add_menu_page(
        'RCC Site Settings',
        'RCC Settings',
        'manage_options',
        'rcc-settings',
        'rcc_admin_page_general',
        'dashicons-admin-settings',
        25
    );
    add_submenu_page('rcc-settings', 'General Settings', 'General', 'manage_options', 'rcc-settings', 'rcc_admin_page_general');
    add_submenu_page('rcc-settings', 'MEGASTRUCT Settings', 'MEGASTRUCT', 'manage_options', 'rcc-mega-settings', 'rcc_admin_page_mega');
    add_submenu_page('rcc-settings', 'MESSODEX Settings', 'MESSODEX', 'manage_options', 'rcc-messo-settings', 'rcc_admin_page_messo');
    add_submenu_page('rcc-settings', 'Gallery Images', 'Gallery', 'manage_options', 'rcc-gallery-settings', 'rcc_admin_page_gallery');
});

add_action('admin_enqueue_scripts', function ($hook) {
    if (strpos($hook, 'rcc-') === false && strpos($hook, 'rcc_') === false) {
        return;
    }
    wp_enqueue_media();
    wp_add_inline_script('jquery-core', '
        jQuery(function($){
            $(document).on("click", ".rcc-img-upload", function(e){
                e.preventDefault();
                var btn=$(this), field=btn.data("field"), prev=btn.data("prev");
                var frame=wp.media({title:"Select Image",multiple:false,library:{type:"image"}});
                frame.on("select", function(){
                    var att=frame.state().get("selection").first().toJSON();
                    $("#"+field).val(att.url);
                    $("#"+prev).attr("src",att.url).show();
                });
                frame.open();
            });
            $(document).on("click", ".rcc-img-remove", function(e){
                e.preventDefault();
                var btn=$(this), field=btn.data("field"), prev=btn.data("prev");
                $("#"+field).val("");
                $("#"+prev).hide().attr("src","");
            });
        });
    ');
});

function rcc_admin_booth_gallery_section($option_key, $js_var)
{
    $existing = [];
    $saved_json = get_option('rcc_opt_' . $option_key, '');
    if ($saved_json) {
        $decoded = json_decode($saved_json, true);
        if (is_array($decoded)) {
            $existing = $decoded;
        }
    }
    ?>
    <hr style="margin:2rem 0;">
    <h2>Booth Gallery (Carousel Images)</h2>
    <p>These images appear in the <strong>Booth Gallery</strong> carousel on the event page.<br>
       Add, remove, and arrange the order with the arrow buttons. The first image appears first in the live slider. Click <em>Save</em> above to apply changes.</p>
    <input type="hidden" name="<?php echo esc_attr($option_key); ?>" id="<?php echo esc_attr($js_var . '_data'); ?>"
           value="<?php echo esc_attr(wp_json_encode($existing)); ?>">
    <div id="<?php echo esc_attr($js_var . '_thumbs'); ?>" style="display:flex;flex-wrap:wrap;gap:12px;margin:1rem 0 1.25rem;">
        <?php foreach ($existing as $index => $url) : ?>
            <div class="rcc-booth-thumb" style="position:relative;width:140px;padding-top:28px;">
                <div style="position:absolute;top:0;left:0;display:flex;gap:4px;">
                    <button type="button" class="button rcc-booth-move"
                            data-var="<?php echo esc_attr($js_var); ?>"
                            data-index="<?php echo (int) $index; ?>"
                            data-dir="-1"
                            style="min-height:0;line-height:1.6;padding:0 8px;">&#8592;</button>
                    <button type="button" class="button rcc-booth-move"
                            data-var="<?php echo esc_attr($js_var); ?>"
                            data-index="<?php echo (int) $index; ?>"
                            data-dir="1"
                            style="min-height:0;line-height:1.6;padding:0 8px;">&#8594;</button>
                </div>
                <img src="<?php echo esc_url($url); ?>"
                     style="width:140px;height:105px;object-fit:cover;display:block;border:1px solid #ddd;border-radius:4px;">
                <button type="button" class="button rcc-booth-remove"
                        data-var="<?php echo esc_attr($js_var); ?>"
                        data-url="<?php echo esc_attr($url); ?>"
                        style="position:absolute;top:32px;right:4px;padding:0 6px;line-height:1.6;min-height:0;background:#c00;color:#fff;border-color:#a00;">&times;</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="button button-secondary rcc-booth-add" data-var="<?php echo esc_attr($js_var); ?>">
        &#43; Add Booth Photos
    </button>
    <script>
    jQuery(function($){
        var <?php echo esc_js($js_var); ?> = <?php echo wp_json_encode($existing); ?>;

        function refreshBooth(varName, data) {
            var thumbsId = '#' + varName + '_thumbs';
            var dataId   = '#' + varName + '_data';
            $(dataId).val(JSON.stringify(data));
            var html = '';
            data.forEach(function(url, index) {
                html += '<div class="rcc-booth-thumb" style="position:relative;width:140px;padding-top:28px;">'
                    + '<div style="position:absolute;top:0;left:0;display:flex;gap:4px;">'
                    + '<button type="button" class="button rcc-booth-move" data-var="'+varName+'" data-index="'+index+'" data-dir="-1" style="min-height:0;line-height:1.6;padding:0 8px;">&#8592;</button>'
                    + '<button type="button" class="button rcc-booth-move" data-var="'+varName+'" data-index="'+index+'" data-dir="1" style="min-height:0;line-height:1.6;padding:0 8px;">&#8594;</button>'
                    + '</div>'
                    + '<img src="'+url+'" style="width:140px;height:105px;object-fit:cover;display:block;border:1px solid #ddd;border-radius:4px;">'
                    + '<button type="button" class="button rcc-booth-remove" data-var="'+varName+'" data-url="'+url+'"'
                    + ' style="position:absolute;top:32px;right:4px;padding:0 6px;line-height:1.6;min-height:0;background:#c00;color:#fff;border-color:#a00;">&times;</button>'
                    + '</div>';
            });
            $(thumbsId).html(html);
        }

        $(document).on('click', '.rcc-booth-add[data-var="<?php echo esc_js($js_var); ?>"]', function(e) {
            e.preventDefault();
            var varName = $(this).data('var');
            var frame = wp.media({title: 'Add Booth Photos', multiple: true, library: {type: 'image'}});
            frame.on('select', function() {
                frame.state().get('selection').each(function(att) {
                    var url = att.toJSON().url;
                    if (url && <?php echo esc_js($js_var); ?>.indexOf(url) === -1) {
                        <?php echo esc_js($js_var); ?>.push(url);
                    }
                });
                refreshBooth(varName, <?php echo esc_js($js_var); ?>);
            });
            frame.open();
        });

        $(document).on('click', '.rcc-booth-remove[data-var="<?php echo esc_js($js_var); ?>"]', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var varName = $(this).data('var');
            <?php echo esc_js($js_var); ?> = <?php echo esc_js($js_var); ?>.filter(function(u) { return u !== url; });
            refreshBooth(varName, <?php echo esc_js($js_var); ?>);
        });
        $(document).on('click', '.rcc-booth-move[data-var="<?php echo esc_js($js_var); ?>"]', function(e) {
            e.preventDefault();
            var dir = parseInt($(this).data('dir'), 10);
            var index = parseInt($(this).data('index'), 10);
            var varName = $(this).data('var');
            var data = <?php echo esc_js($js_var); ?>;
            var swapIndex = index + dir;
            if (swapIndex < 0 || swapIndex >= data.length) {
                return;
            }
            var temp = data[index];
            data[index] = data[swapIndex];
            data[swapIndex] = temp;
            refreshBooth(varName, data);
        });
    });
    </script>
    <?php
}

function rcc_admin_save_settings($fields, $nonce_key)
{
    if (
        !isset($_POST[$nonce_key]) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$nonce_key])), 'rcc_save_' . $nonce_key)
    ) {
        return false;
    }
    foreach ($fields as $key) {
        $raw = isset($_POST[$key]) ? wp_unslash($_POST[$key]) : '';
        update_option('rcc_opt_' . $key, sanitize_text_field($raw));
    }
    return true;
}

function rcc_admin_field_row($type, $key, $label, $default = '', $extra = [])
{
    $val = get_option('rcc_opt_' . $key, '');
    if ($val === '' || $val === false) {
        $val = $default;
    }
    $id = 'rcc_field_' . esc_attr($key);
    $placeholder = $extra['placeholder'] ?? $default;
    $description = $extra['description'] ?? '';
    echo '<tr>';
    echo '<th scope="row"><label for="' . $id . '">' . esc_html($label) . '</label></th>';
    echo '<td>';
    if ($type === 'textarea') {
        echo '<textarea id="' . $id . '" name="' . esc_attr($key) . '" rows="' . (int) ($extra['rows'] ?? 3) . '" class="large-text" placeholder="' . esc_attr($placeholder) . '">' . esc_textarea($val) . '</textarea>';
    } elseif ($type === 'image') {
        $img_url = get_option('rcc_opt_' . $key, '');
        echo '<input type="hidden" id="' . $id . '" name="' . esc_attr($key) . '" value="' . esc_attr($img_url) . '">';
        echo '<div style="margin-bottom:8px;">';
        if ($img_url) {
            echo '<img id="prev_' . $id . '" src="' . esc_url($img_url) . '" style="max-height:80px;max-width:200px;display:block;margin-bottom:6px;">';
        } else {
            echo '<img id="prev_' . $id . '" src="" style="max-height:80px;max-width:200px;display:none;margin-bottom:6px;">';
        }
        echo '</div>';
        echo '<button type="button" class="button rcc-img-upload" data-field="' . $id . '" data-prev="prev_' . $id . '">Upload / Change Image</button> ';
        echo '<button type="button" class="button rcc-img-remove" data-field="' . $id . '" data-prev="prev_' . $id . '">Remove</button>';
    } else {
        echo '<input type="text" id="' . $id . '" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '" class="regular-text" placeholder="' . esc_attr($placeholder) . '">';
    }
    if ($description) {
        echo '<p class="description">' . esc_html($description) . '</p>';
    }
    echo '</td>';
    echo '</tr>';
}

function rcc_admin_page_general()
{
    $fields = ['rcc_phone', 'rcc_email', 'rcc_address', 'rcc_social_facebook', 'rcc_social_instagram', 'rcc_social_youtube', 'rcc_social_linkedin', 'rcc_logo'];
    $saved  = rcc_admin_save_settings($fields, 'rcc_general');
    ?>
    <div class="wrap">
        <h1>RCC General Settings</h1>
        <?php if ($saved) : ?>
            <div class="notice notice-success is-dismissible"><p>Settings saved successfully.</p></div>
        <?php endif; ?>
        <form method="post">
            <?php wp_nonce_field('rcc_save_rcc_general', 'rcc_general'); ?>
            <table class="form-table">
                <?php
                rcc_admin_field_row('text',     'rcc_phone',            'Phone Number',    '+234 903 491 4989', ['description' => 'Main public contact number shown across the website and SEO organization markup.']);
                rcc_admin_field_row('text',     'rcc_email',            'Main Email',      'info@radiantccafrica.com', ['description' => 'Primary business email for website contact, event enquiries, and search engine business details.']);
                rcc_admin_field_row('textarea', 'rcc_address',          'Office Address',  'RADIANT CREATIVE CONCEPTS LIMITED (EVENTS & EXHIBITIONS), Thuraya Crescent, Along Lagos-Ibadan Expressway, Mowe, Ogun State, Nigeria.', ['rows' => 3, 'description' => 'Formal office address used in the footer, contact page, and business schema markup.']);
                rcc_admin_field_row('text',     'rcc_social_facebook',  'Facebook URL',    'https://facebook.com/radiantccafrica', ['description' => 'Full Facebook page URL.']);
                rcc_admin_field_row('text',     'rcc_social_instagram', 'Instagram URL',   'https://instagram.com/radiantccafrica', ['description' => 'Full Instagram profile URL.']);
                rcc_admin_field_row('text',     'rcc_social_youtube',   'YouTube URL',     'https://youtube.com/@radiantccafrica', ['description' => 'Full YouTube channel URL.']);
                rcc_admin_field_row('text',     'rcc_social_linkedin',  'LinkedIn URL',    'https://linkedin.com/company/radiantccafrica', ['description' => 'Full LinkedIn company page URL.']);
                rcc_admin_field_row('image',    'rcc_logo',             'RCC Logo', '', ['description' => 'Optional admin logo override. For live theme-only export, keep the master brand file inside assets/images, especially rcc.svg.']);
                ?>
            </table>
            <?php submit_button('Save General Settings'); ?>
        </form>
    </div>
    <?php
}

function rcc_admin_page_mega()
{
    $fields = ['mega_date', 'mega_venue', 'mega_email', 'mega_phone', 'mega_logo'];
    $saved  = rcc_admin_save_settings($fields, 'rcc_mega');

    // Save booth gallery images
    if (
        isset($_POST['rcc_mega']) &&
        wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rcc_mega'])), 'rcc_save_rcc_mega') &&
        isset($_POST['mega_booth_images'])
    ) {
        $arr = json_decode(wp_unslash($_POST['mega_booth_images']), true);
        if (!is_array($arr)) { $arr = []; }
        update_option('rcc_opt_mega_booth_images', wp_json_encode(array_values(array_filter(array_map('esc_url_raw', $arr)))));
    }
    ?>
    <div class="wrap">
        <h1>MEGASTRUCT AFRICA Settings</h1>
        <?php if ($saved) : ?>
            <div class="notice notice-success is-dismissible"><p>Settings saved successfully.</p></div>
        <?php endif; ?>
        <form method="post">
            <?php wp_nonce_field('rcc_save_rcc_mega', 'rcc_mega'); ?>
            <table class="form-table">
                <?php
                rcc_admin_field_row('text',  'mega_date',  'Event Date',  '11th - 13th October 2026', ['description' => 'Formal event date shown on the MEGASTRUCT AFRICA page and structured event data.']);
                rcc_admin_field_row('text',  'mega_venue', 'Venue',       'Landmark Centre, Victoria Island, Lagos, Nigeria', ['description' => 'Official venue text for the event hero, contact prompts, and schema markup.']);
                rcc_admin_field_row('text',  'mega_email', 'Event Email', 'megastruct@radiantccafrica.com', ['description' => 'Primary email used for MEGASTRUCT enquiries. Create this mailbox on your hosting/email provider before going live.']);
                rcc_admin_field_row('text',  'mega_phone', 'Event Phone', '+234 903 491 4989', ['description' => 'Direct event phone or WhatsApp line.']);
                rcc_admin_field_row('image', 'mega_logo',  'MEGASTRUCT Logo (shown on event hero page)', '', ['description' => 'Optional admin override. For portable live deployment, keep the MEGASTRUCT logo file in assets/images.']);
                ?>
            </table>
            <?php rcc_admin_booth_gallery_section('mega_booth_images', 'rcc_mega_booth'); ?>
            <?php submit_button('Save MEGASTRUCT Settings'); ?>
        </form>
    </div>
    <?php
}

function rcc_admin_page_messo()
{
    $fields = ['messo_date', 'messo_venue', 'messo_email', 'messo_phone', 'messo_logo'];
    $saved  = rcc_admin_save_settings($fields, 'rcc_messo');

    // Save booth gallery images
    if (
        isset($_POST['rcc_messo']) &&
        wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rcc_messo'])), 'rcc_save_rcc_messo') &&
        isset($_POST['messo_booth_images'])
    ) {
        $arr = json_decode(wp_unslash($_POST['messo_booth_images']), true);
        if (!is_array($arr)) { $arr = []; }
        update_option('rcc_opt_messo_booth_images', wp_json_encode(array_values(array_filter(array_map('esc_url_raw', $arr)))));
    }
    ?>
    <div class="wrap">
        <h1>MESSODEX WEST AFRICA Settings</h1>
        <?php if ($saved) : ?>
            <div class="notice notice-success is-dismissible"><p>Settings saved successfully.</p></div>
        <?php endif; ?>
        <form method="post">
            <?php wp_nonce_field('rcc_save_rcc_messo', 'rcc_messo'); ?>
            <table class="form-table">
                <?php
                rcc_admin_field_row('text',  'messo_date',  'Event Date',  '19-21 August 2026', ['description' => 'Formal event date shown on the MESSODEX WEST AFRICA page and structured event data.']);
                rcc_admin_field_row('text',  'messo_venue', 'Venue',       'Landmark Centre, Victoria Island, Lagos, Nigeria', ['description' => 'Official venue text for the event hero, CTA sections, and schema markup.']);
                rcc_admin_field_row('text',  'messo_email', 'Event Email', 'messodex@radiantccafrica.com', ['description' => 'Primary email used for MESSODEX enquiries. Create this mailbox on your hosting/email provider before going live.']);
                rcc_admin_field_row('text',  'messo_phone', 'Event Phone', '+234 903 491 4989', ['description' => 'Direct event phone or WhatsApp line.']);
                rcc_admin_field_row('image', 'messo_logo',  'MESSODEX Logo (shown on event hero page)', '', ['description' => 'Optional admin override. For portable live deployment, keep mesodex-logo.jpeg inside assets/images.']);
                ?>
            </table>
            <?php rcc_admin_booth_gallery_section('messo_booth_images', 'rcc_messo_booth'); ?>
            <?php submit_button('Save MESSODEX Settings'); ?>
        </form>
    </div>
    <?php
}

function rcc_admin_page_gallery()
{
    $saved_msg = false;
    if (
        isset($_POST['rcc_gallery_nonce']) &&
        wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rcc_gallery_nonce'])), 'rcc_save_gallery')
    ) {
        $raw_images = isset($_POST['gallery_images']) ? wp_unslash($_POST['gallery_images']) : '[]';
        // Validate: must be a JSON array of URL strings
        $arr = json_decode($raw_images, true);
        if (!is_array($arr)) {
            $arr = [];
        }
        $clean = [];
        foreach ($arr as $url) {
            $url = esc_url_raw(trim((string) $url));
            if ($url) {
                $clean[] = $url;
            }
        }
        update_option('rcc_opt_gallery_images', wp_json_encode($clean));
        $saved_msg = true;
    }

    $saved_json = get_option('rcc_opt_gallery_images', '');
    $existing   = [];
    if ($saved_json) {
        $decoded = json_decode($saved_json, true);
        if (is_array($decoded)) {
            $existing = $decoded;
        }
    }
    $gallery_folder = get_template_directory() . '/assets/gallery';
    $gallery_files  = [];
    $extensions     = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (is_dir($gallery_folder)) {
        $files = @scandir($gallery_folder) ?: [];
        foreach ($files as $f) {
            if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $extensions, true)) {
                $gallery_files[] = $f;
            }
        }
        sort($gallery_files);
    }
    ?>
    <div class="wrap">
        <h1>Gallery Images</h1>
        <?php if ($saved_msg) : ?>
            <div class="notice notice-success is-dismissible"><p>Gallery saved successfully.</p></div>
        <?php endif; ?>

        <h2 style="margin-top:1.5rem;">&#128193; Theme Gallery Folder <code style="font-size:0.85em;">/assets/gallery/</code></h2>
        <p>
            Drop image files directly into <strong><?php echo esc_html($gallery_folder); ?></strong><br>
            Any image placed there <strong>automatically shows</strong> in the live Gallery page — no saving needed.<br>
            <em>This folder is the fallback source. The arranged Media Library list below is now the primary gallery source when it has images.</em>
        </p>
        <?php if (!empty($gallery_files)) : ?>
            <div style="display:flex;flex-wrap:wrap;gap:10px;margin:1rem 0 1.5rem;">
                <?php foreach ($gallery_files as $f) : ?>
                    <div style="text-align:center;width:120px;">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/gallery/' . $f); ?>"
                             style="width:120px;height:90px;object-fit:cover;border:1px solid #ddd;border-radius:4px;display:block;">
                        <span style="font-size:0.72rem;word-break:break-all;display:block;margin-top:4px;"><?php echo esc_html($f); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p style="color:#777;font-style:italic;">No images in folder yet. Add images to the folder above to get started.</p>
        <?php endif; ?>

        <hr>
        <h2>&#128444; Upload via WordPress Media Library</h2>
        <p>Use this section to add and arrange images from the WP Media Library.<br>
           <em>This ordered list is now the main gallery source on the live site. Use the arrow buttons to control display order.</em></p>
        <form method="post" id="rcc-gallery-form">
            <?php wp_nonce_field('rcc_save_gallery', 'rcc_gallery_nonce'); ?>
            <input type="hidden" name="gallery_images" id="rcc-gallery-data" value="<?php echo esc_attr(wp_json_encode($existing)); ?>">
            <div id="rcc-gallery-thumbs" style="display:flex;flex-wrap:wrap;gap:12px;margin:1rem 0 1.5rem;">
                <?php foreach ($existing as $index => $url) : ?>
                    <div class="rcc-gallery-thumb" style="position:relative;width:130px;padding-top:28px;">
                        <div style="position:absolute;top:0;left:0;display:flex;gap:4px;">
                            <button type="button" class="button rcc-gallery-move" data-index="<?php echo (int) $index; ?>" data-dir="-1" style="min-height:0;line-height:1.6;padding:0 8px;">&#8592;</button>
                            <button type="button" class="button rcc-gallery-move" data-index="<?php echo (int) $index; ?>" data-dir="1" style="min-height:0;line-height:1.6;padding:0 8px;">&#8594;</button>
                        </div>
                        <img src="<?php echo esc_url($url); ?>" style="width:130px;height:100px;object-fit:cover;display:block;border:1px solid #ddd;border-radius:4px;">
                        <button type="button" class="button rcc-gallery-remove" data-url="<?php echo esc_attr($url); ?>"
                                style="position:absolute;top:32px;right:4px;padding:0 6px;line-height:1.6;min-height:0;background:#c00;color:#fff;border-color:#a00;">&times;</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button button-primary" id="rcc-gallery-add">&#43; Add Images</button>
            <p class="description" style="margin-top:0.5rem;">Click <em>Add Images</em> to pick photos from the WordPress Media Library.</p>
            <?php submit_button('Save Gallery'); ?>
        </form>
    </div>
    <script>
    jQuery(function($){
        var data = <?php echo wp_json_encode($existing); ?>;

        function refresh(){
            $('#rcc-gallery-data').val(JSON.stringify(data));
            var html='';
            data.forEach(function(url, index){
                html+='<div class="rcc-gallery-thumb" style="position:relative;width:130px;padding-top:28px;">'
                    +'<div style="position:absolute;top:0;left:0;display:flex;gap:4px;">'
                    +'<button type="button" class="button rcc-gallery-move" data-index="'+index+'" data-dir="-1" style="min-height:0;line-height:1.6;padding:0 8px;">&#8592;</button>'
                    +'<button type="button" class="button rcc-gallery-move" data-index="'+index+'" data-dir="1" style="min-height:0;line-height:1.6;padding:0 8px;">&#8594;</button>'
                    +'</div>'
                    +'<img src="'+url+'" style="width:130px;height:100px;object-fit:cover;display:block;border:1px solid #ddd;border-radius:4px;">'
                    +'<button type="button" class="button rcc-gallery-remove" data-url="'+url+'" style="position:absolute;top:32px;right:4px;padding:0 6px;line-height:1.6;min-height:0;background:#c00;color:#fff;border-color:#a00;">&times;</button>'
                    +'</div>';
            });
            $('#rcc-gallery-thumbs').html(html);
        }

        $(document).on('click','#rcc-gallery-add',function(e){
            e.preventDefault();
            var frame=wp.media({title:'Add Gallery Images',multiple:true,library:{type:'image'}});
            frame.on('select',function(){
                frame.state().get('selection').each(function(att){
                    var url=att.toJSON().url;
                    if(url && data.indexOf(url)===-1){ data.push(url); }
                });
                refresh();
            });
            frame.open();
        });

        $(document).on('click','.rcc-gallery-remove',function(e){
            e.preventDefault();
            var url=$(this).data('url');
            data=data.filter(function(u){ return u!==url; });
            refresh();
        });
        $(document).on('click','.rcc-gallery-move',function(e){
            e.preventDefault();
            var dir=parseInt($(this).data('dir'),10);
            var index=parseInt($(this).data('index'),10);
            var swap=index+dir;
            if(swap < 0 || swap >= data.length){ return; }
            var temp=data[index];
            data[index]=data[swap];
            data[swap]=temp;
            refresh();
        });
    });
    </script>
    <?php
}

/* ══════════════════════════════════════════════════════════════
   ACF LOCAL FIELD GROUPS — edit fields directly on page screens
══════════════════════════════════════════════════════════════ */

add_filter('acf/location/rule_types', function ($choices) {
    if (!isset($choices['Page'])) {
        $choices['Page'] = [];
    }

    $choices['Page']['page_slug'] = 'Page Slug';

    return $choices;
});

add_filter('acf/location/rule_values/page_slug', function ($choices) {
    $pages = get_pages([
        'sort_column' => 'menu_order,post_title',
        'post_status' => ['publish', 'draft', 'pending', 'private'],
    ]);

    foreach ($pages as $page) {
        $choices[$page->post_name] = $page->post_title . ' (' . $page->post_name . ')';
    }

    return $choices;
});

add_filter('acf/location/rule_match/page_slug', function ($match, $rule, $options) {
    $post_id = $options['post_id'] ?? 0;
    $post = $post_id ? get_post($post_id) : null;

    if (!$post instanceof WP_Post || $post->post_type !== 'page') {
        return false;
    }

    $slug_match = ($post->post_name === $rule['value']);

    return $rule['operator'] === '!=' ? !$slug_match : $slug_match;
}, 10, 3);

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    // ── Gallery page ──
    acf_add_local_field_group([
        'key'    => 'group_rcc_gallery',
        'title'  => 'Gallery Images',
        'fields' => [
            [
                'key'          => 'field_gallery_images',
                'label'        => 'Gallery Images',
                'name'         => 'gallery_images',
                'type'         => 'gallery',
                'instructions' => 'Upload or select photos here. They will display in a masonry grid on the Gallery page.',
                'min'          => 0,
                'max'          => 0,
                'preview_size' => 'medium',
                'insert'       => 'append',
                'mime_types'   => 'jpg,jpeg,png,webp,gif',
            ],
        ],
        'location' => [
            [['param' => 'page_slug', 'operator' => '==', 'value' => 'gallery']],
        ],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
    ]);

    // ── About Us page ──
    acf_add_local_field_group([
        'key'    => 'group_rcc_about',
        'title'  => 'About Page Content',
        'fields' => [
            [
                'key'          => 'field_about_intro',
                'label'        => 'Intro Line',
                'name'         => 'about_intro',
                'type'         => 'textarea',
                'instructions' => 'Short tagline shown beneath the hero title.',
                'default_value'=> 'We are a leading event management and exhibition company dedicated to creating unforgettable experiences for businesses, brands, and communities across Africa.',
                'rows'         => 2,
            ],
            [
                'key'          => 'field_about_who',
                'label'        => 'Who We Are',
                'name'         => 'about_who',
                'type'         => 'textarea',
                'instructions' => 'Full company description paragraph.',
                'default_value'=> 'Radiant Creative Concepts Limited (RCCL) is a dynamic and innovative event management company specializing in the planning and execution of international exhibitions, trade shows, corporate events, social events, product launches, conferences, workshops, virtual & hybrid events, and cultural celebrations. With creativity, precision, and global-standard professionalism, we bring ideas to life, beautifully and seamlessly.',
                'rows'         => 5,
            ],
            [
                'key'          => 'field_about_vision',
                'label'        => 'Our Vision',
                'name'         => 'about_vision',
                'type'         => 'textarea',
                'default_value'=> 'To become Africa\'s most innovative and trusted events and exhibitions company, connecting brands to opportunities and transforming ideas into exceptional experiences.',
                'rows'         => 3,
            ],
            [
                'key'          => 'field_about_mission',
                'label'        => 'Our Mission',
                'name'         => 'about_mission',
                'type'         => 'textarea',
                'default_value'=> 'To deliver world-class events through excellence, professionalism, and creativity while providing clients with seamless planning, coordination, and execution.',
                'rows'         => 3,
            ],
        ],
        'location' => [
            [['param' => 'page_slug', 'operator' => '==', 'value' => 'about-us']],
        ],
        'menu_order' => 0,
        'position'   => 'normal',
        'style'      => 'default',
    ]);

    // ── Contact Us page ──
    acf_add_local_field_group([
        'key'    => 'group_rcc_contact',
        'title'  => 'Contact Page Content',
        'fields' => [
            [
                'key'          => 'field_contact_intro',
                'label'        => 'Hero Intro Text',
                'name'         => 'contact_intro',
                'type'         => 'text',
                'instructions' => 'Short line shown below the Contact page hero title.',
                'default_value'=> 'Let\'s bring your event to life. Talk to us today for bookings, partnerships, vendor collaboration, or exhibition space inquiries.',
            ],
        ],
        'location' => [
            [['param' => 'page_slug', 'operator' => '==', 'value' => 'contact-us']],
        ],
        'menu_order' => 0,
        'position'   => 'normal',
        'style'      => 'default',
    ]);

    // ── Home page ──
    acf_add_local_field_group([
        'key'    => 'group_rcc_home',
        'title'  => 'Home Page Content',
        'fields' => [
            [
                'key'           => 'field_home_tagline',
                'label'         => 'Hero Tagline',
                'name'          => 'home_hero_tagline',
                'type'          => 'text',
                'instructions'  => 'Tagline shown beneath the hero title on the home page.',
                'default_value' => 'Building Connections, Creating Opportunities.',
            ],
            [
                'key'           => 'field_home_hero_image',
                'label'         => 'Hero Background Image',
                'name'          => 'home_hero_image',
                'type'          => 'image',
                'instructions'  => 'Background image for the home page hero section.',
                'return_format' => 'url',
                'preview_size'  => 'medium',
            ],
        ],
        'location' => [
            [['param' => 'page_type', 'operator' => '==', 'value' => 'front_page']],
        ],
        'menu_order' => 0,
        'position'   => 'normal',
        'style'      => 'default',
    ]);
});

/* ══════════════════════════════════════════════════════════════
   WORDPRESS CUSTOMIZER — edit everything from Appearance → Customize
══════════════════════════════════════════════════════════════ */

add_action('customize_register', function (WP_Customize_Manager $wp_customize) {

    // ── Panel ──
    $wp_customize->add_panel('rcc_panel', [
        'title'       => 'RCC Site Settings',
        'description' => 'Control logos, contact details, and event information.',
        'priority'    => 30,
    ]);

    // ─────────────────────────────────────────────
    // Section: Logos
    // ─────────────────────────────────────────────
    $wp_customize->add_section('rcc_logos', [
        'title'    => 'Logos',
        'panel'    => 'rcc_panel',
        'priority' => 10,
    ]);

    $logo_controls = [
        ['rcc_opt_rcc_logo',   'RCC Main Logo',   'Site header & footer logo.'],
        ['rcc_opt_mega_logo',  'MEGASTRUCT Logo',  'Hero on the MEGASTRUCT event page.'],
        ['rcc_opt_messo_logo', 'MESSODEX Logo',    'Hero on the MESSODEX event page.'],
    ];
    foreach ($logo_controls as $lc) {
        $wp_customize->add_setting($lc[0], [
            'type'       => 'option',
            'capability' => 'manage_options',
            'transport'  => 'refresh',
        ]);
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, $lc[0], [
            'label'       => $lc[1],
            'description' => $lc[2],
            'section'     => 'rcc_logos',
        ]));
    }

    // ─────────────────────────────────────────────
    // Section: General Info
    // ─────────────────────────────────────────────
    $wp_customize->add_section('rcc_general', [
        'title'    => 'General Info',
        'panel'    => 'rcc_panel',
        'priority' => 20,
    ]);

    $general_controls = [
        ['rcc_opt_rcc_phone',            'Phone Number',   'text'],
        ['rcc_opt_rcc_email',            'Email Address',  'text'],
        ['rcc_opt_rcc_address',          'Office Address', 'textarea'],
        ['rcc_opt_rcc_social_facebook',  'Facebook URL',   'text'],
        ['rcc_opt_rcc_social_instagram', 'Instagram URL',  'text'],
        ['rcc_opt_rcc_social_youtube',   'YouTube URL',    'text'],
        ['rcc_opt_rcc_social_linkedin',  'LinkedIn URL',   'text'],
    ];
    foreach ($general_controls as $gc) {
        $wp_customize->add_setting($gc[0], [
            'type'       => 'option',
            'capability' => 'manage_options',
            'transport'  => 'refresh',
        ]);
        $wp_customize->add_control($gc[0], [
            'label'   => $gc[1],
            'section' => 'rcc_general',
            'type'    => $gc[2],
        ]);
    }

    // ─────────────────────────────────────────────
    // Section: MEGASTRUCT Event
    // ─────────────────────────────────────────────
    $wp_customize->add_section('rcc_megastruct', [
        'title'    => 'MEGASTRUCT Event',
        'panel'    => 'rcc_panel',
        'priority' => 30,
    ]);

    $mega_controls = [
        ['rcc_opt_mega_date',  'Event Date'],
        ['rcc_opt_mega_venue', 'Venue'],
        ['rcc_opt_mega_email', 'Event Email'],
        ['rcc_opt_mega_phone', 'Event Phone'],
    ];
    foreach ($mega_controls as $mc) {
        $wp_customize->add_setting($mc[0], [
            'type'       => 'option',
            'capability' => 'manage_options',
            'transport'  => 'refresh',
        ]);
        $wp_customize->add_control($mc[0], [
            'label'   => $mc[1],
            'section' => 'rcc_megastruct',
            'type'    => 'text',
        ]);
    }

    // ─────────────────────────────────────────────
    // Section: MESSODEX Event
    // ─────────────────────────────────────────────
    $wp_customize->add_section('rcc_messodex', [
        'title'    => 'MESSODEX Event',
        'panel'    => 'rcc_panel',
        'priority' => 40,
    ]);

    $messo_controls = [
        ['rcc_opt_messo_date',  'Event Date'],
        ['rcc_opt_messo_venue', 'Venue'],
        ['rcc_opt_messo_email', 'Event Email'],
        ['rcc_opt_messo_phone', 'Event Phone'],
    ];
    foreach ($messo_controls as $mc) {
        $wp_customize->add_setting($mc[0], [
            'type'       => 'option',
            'capability' => 'manage_options',
            'transport'  => 'refresh',
        ]);
        $wp_customize->add_control($mc[0], [
            'label'   => $mc[1],
            'section' => 'rcc_messodex',
            'type'    => 'text',
        ]);
    }
});
