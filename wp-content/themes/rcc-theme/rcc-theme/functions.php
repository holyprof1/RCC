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
    wp_enqueue_style(
        'rcc-fonts',
        'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&family=DM+Sans:wght@300;400;500;600;700&display=swap',
        [],
        null
    );
    wp_enqueue_style('rcc-style', get_stylesheet_uri(), [], '2.0.0');
    wp_enqueue_style('rcc-main', get_template_directory_uri() . '/assets/css/rcc-main.css', ['rcc-fonts'], '2.1.0');
    wp_enqueue_script('rcc-main', get_template_directory_uri() . '/assets/js/main.js', [], '2.1.0', true);
});

add_action('wp_head', function () {
    $images = [
        rcc_get_logo_url(),
        rcc_get_logo_url('mega'),
        rcc_get_logo_url('messo'),
        rcc_upload_asset_candidates(['2026/03/hero.png', 'hero.png']),
        rcc_upload_asset_candidates(['2026/03/MEGASTRUCT.png', 'MEGASTRUCT.png', 'megastruct.jpeg']),
        rcc_upload_asset_candidates(['2026/03/MESSODEX.png', 'MESSODEX.png', '2026/03/MESSEDEX.png', 'MESSEDEX.png']),
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
        'phone'         => rcc_acf_option('rcc_phone',   '+234 903 491 4948'),
        'email'         => rcc_acf_option('rcc_email',   'info@radiantcc.com'),
        'website'       => 'www.radiantcc.com',
        'website_url'   => 'https://www.radiantcc.com',
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
        return get_template_directory_uri() . '/assets/images/' . $basename;
    }
    return content_url('/uploads/' . ltrim($filename, '/'));
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

function rcc_get_logo_url($event = '')
{
    if ($event === 'mega') {
        $saved = rcc_acf_option('mega_logo');
        if ($saved) {
            return $saved;
        }
        return rcc_upload_asset_candidates(['2026/03/megastruct-logo.jpeg', 'megastruct-logo.jpeg']);
    }
    if ($event === 'messo') {
        $saved = rcc_acf_option('messo_logo');
        if ($saved) {
            return $saved;
        }
        return rcc_upload_asset_candidates(['2026/03/messodex-logo.jpeg', 'messodex-logo.jpeg']);
    }
    $saved = rcc_acf_option('rcc_logo');
    if ($saved) {
        return $saved;
    }
    return rcc_upload_asset_candidates(['2026/03/rcc.PNG', 'rcc.PNG']);
}

function rcc_upload_asset_candidates($candidates)
{
    // Check theme assets/images/ first (enables fully portable theme export)
    foreach ($candidates as $candidate) {
        $basename  = basename($candidate);
        $theme_abs = get_template_directory() . '/assets/images/' . $basename;
        if (file_exists($theme_abs)) {
            return get_template_directory_uri() . '/assets/images/' . $basename;
        }
    }

    // Fall back to wp-content/uploads
    foreach ($candidates as $candidate) {
        $relative = ltrim($candidate, '/');
        $absolute = wp_normalize_path(WP_CONTENT_DIR . '/uploads/' . $relative);
        if (file_exists($absolute)) {
            return content_url('/uploads/' . $relative);
        }
    }

    // Last resort: return theme path for first candidate
    return get_template_directory_uri() . '/assets/images/' . basename($candidates[0]);
}

function rcc_get_home_data()
{
    return [
        'hero_title_html' => 'Shaping Africa\'s <span>Premier Events</span> &amp; <span>Exhibitions</span>',
        'hero_text' => 'Connecting Industries. Showcasing Innovation. Creating Lasting Opportunities.',
        'hero_image' => rcc_upload_asset_candidates(['2026/03/hero.png', 'hero.png']),
        'events' => [
            [
                'title' => 'MEGASTRUCT AFRICA',
                'subtitle' => 'Mega Infrastructure, Construction & Mining Equipment Expo',
                'description' => 'An international platform for construction machinery, infrastructure solutions, mining equipment, investors, and industry leaders ready to shape Africa\'s growth.',
                'url' => rcc_get_page_url('megastruct-africa'),
                'image' => rcc_upload_asset_candidates(['2026/03/MEGASTRUCT.png', 'MEGASTRUCT.png', 'megastruct.jpeg']),
                'meta' => '11th - 13th October 2026 | Landmark Centre, Victoria Island, Lagos',
                'theme' => 'megastruct',
            ],
            [
                'title' => 'MESSODEX WEST AFRICA',
                'subtitle' => 'Media, Stage & Sound Technology Expo',
                'description' => 'A bold showcase for media technology, stage engineering, professional audio, lighting systems, and creative production built for the West African market.',
                'url' => rcc_get_page_url('messodex-west-africa'),
                'image' => rcc_upload_asset_candidates(['2026/03/MESSODEX.png', 'MESSODEX.png', '2026/03/MESSEDEX.png', 'MESSEDEX.png']),
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
        'email'   => rcc_acf_option('mega_email', 'megastruct@radiantcc.com'),
        'phone'   => rcc_acf_option('mega_phone', '+234 903 491 4948'),
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
        'email'   => rcc_acf_option('messo_email', 'messodex@radiantcc.com'),
        'phone'   => rcc_acf_option('messo_phone', '+234 903 491 4948'),
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
        echo '<div class="rcc-notice rcc-notice-error">We could not send your enquiry right now. Please try again or email us directly at info@radiantcc.com.</div>';
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

function rcc_preloader_images()
{
    return [
        'rcc_logo'   => rcc_get_logo_url(),
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
                    <h2>Why Choose<br>Radiant<br><span>Creative?</span></h2>
                    <div class="rcc-why-divider"></div>
                    <p>We are more than an events company. We are architects of opportunity — building platforms that connect Africa to the world.</p>
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
                <p>Organizing Africa&apos;s Leading Exhibitions &amp; Trade Shows &mdash; Building Connections, Creating Opportunities.</p>
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
        ? rcc_upload_asset_candidates(['2026/03/MEGASTRUCT.png', 'MEGASTRUCT.png'])
        : rcc_upload_asset_candidates(['2026/03/MESSODEX.png', 'MESSODEX.png', '2026/03/MESSEDEX.png', 'MESSEDEX.png']);

    $booth_img = rcc_upload_asset_candidates(['2026/03/BOOTH.png', 'BOOTH.png']);
    $theme_cls = $is_mega ? 'rcc-event-hero--mega' : 'rcc-event-hero--messo';

    $jsonld = [
        '@context'            => 'https://schema.org',
        '@type'               => 'ExhibitionEvent',
        'name'                => $data['title'],
        'description'         => $data['subtitle'] . '. ' . $data['date'] . ' at ' . $data['venue'],
        'image'               => $hero_img,
        'startDate'           => $is_mega ? '2026-10-11' : '2026-08-19',
        'endDate'             => $is_mega ? '2026-10-13' : '2026-08-21',
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
            'url'   => 'https://www.radiantcc.com',
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
                <h1 class="rcc-event-hero__title"><?php echo esc_html($data['title']); ?></h1>
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
                        <?php for ($ci = 0; $ci < 5; $ci++) : ?>
                            <div class="rcc-carousel__slide">
                                <img src="<?php echo esc_url($booth_img); ?>"
                                     alt="Exhibition booth <?php echo esc_attr($ci + 1); ?>">
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <button class="rcc-carousel__btn rcc-carousel__next" aria-label="Next">&#8250;</button>
                <div class="rcc-carousel__dots"></div>
            </div>
        </div>
    </section>

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
                    <p>&#9993; <?php echo esc_html(rcc_acf_option('mega_email', 'megastruct@radiantcc.com')); ?></p>
                    <p>&#9742; <?php echo esc_html(rcc_acf_option('mega_phone', $site['phone'])); ?></p>
                </div>
                <div class="rcc-ev-contact-group">
                    <h3>MESSODEX</h3>
                    <p>&#9993; <?php echo esc_html(rcc_acf_option('messo_email', 'messodex@radiantcc.com')); ?></p>
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
                    <label>Company Website<input type="url" name="company_website"></label>
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

function rcc_render_gallery_page()
{
    // Fetch ACF gallery images if available
    $acf_images = [];
    if (function_exists('get_field')) {
        $gallery_page = get_page_by_path('gallery');
        if ($gallery_page) {
            $acf_images = get_field('gallery_images', $gallery_page->ID) ?: [];
        }
    }
    ?>
    <section class="rcc-page-hero">
        <div class="rcc-shell">
            <div class="rcc-page-hero__inner">
                <span class="rcc-kicker">Media</span>
                <h1>Visual Highlights</h1>
                <div class="rcc-page-hero__divider"></div>
                <p>Booth designs, live event moments, and partner visibility from our flagship exhibitions.</p>
            </div>
        </div>
    </section>

    <section class="rcc-section">
        <div class="rcc-shell">
            <?php if (!empty($acf_images)) : ?>
                <div class="rcc-masonry-gallery">
                    <?php foreach ($acf_images as $img) :
                        $url  = is_array($img) ? ($img['sizes']['large'] ?? $img['url'] ?? '') : $img;
                        $alt  = is_array($img) ? ($img['alt'] ?? '') : '';
                        if (!$url) { continue; }
                    ?>
                        <div class="rcc-masonry-gallery__item">
                            <img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <?php
                $groups = [
                    'Photos Of Booths'     => ['Shell Scheme (Standard Booth)', 'Custom Booths'],
                    'Live Events Pictures' => ['Conference sessions', 'Networking moments', 'Product showcases'],
                    'Partners/Sponsors'    => ['Featured partner wall', 'Sponsor branding', 'Collaboration highlights'],
                ];
                foreach ($groups as $title => $items) : ?>
                    <article class="rcc-card" style="margin-bottom:2rem;">
                        <h2><?php echo esc_html($title); ?></h2>
                        <div class="rcc-gallery-grid">
                            <?php foreach ($items as $item) : ?>
                                <div class="rcc-gallery-item">
                                    <img class="rcc-gallery-item__visual-image" src="<?php echo esc_url(rcc_upload_asset_candidates(['2026/03/BOOTH.png', 'BOOTH.png'])); ?>" alt="<?php echo esc_attr($item); ?>">
                                    <span><?php echo esc_html($item); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
                <p style="text-align:center;color:var(--rcc-muted);font-size:0.875rem;margin-top:1rem;">
                    <strong>Tip:</strong> Edit the <em>Gallery</em> page in WordPress and upload images in the <em>Gallery Images</em> field to populate this section.
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
                        <label>Company Website (Optional)<input type="url" name="company_website" placeholder="https://"></label>
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
                        <p>Email: megastruct@radiantcc.com</p>
                        <p>Phone: <?php echo esc_html($site['phone']); ?></p>
                    </div>
                    <div class="rcc-enquiry-contact__group">
                        <strong>MESSODEX</strong>
                        <p>Email: messodex@radiantcc.com</p>
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

    $sent = wp_mail($site['email'], 'New Exhibition Enquiry - ' . $form_type, implode("\n\n", $body), $headers);
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
add_filter('document_title_parts', function ($parts) {
    $slug = rcc_get_current_slug();
    if ($slug === 'megastruct-africa') {
        $parts['title'] = 'MEGASTRUCT AFRICA 2026 — Infrastructure, Construction & Mining Expo | Lagos';
    } elseif ($slug === 'messodex-west-africa') {
        $parts['title'] = 'MESSODEX WEST AFRICA 2026 — Media, Stage & Sound Technology Expo | Lagos';
    }
    return $parts;
});

add_action('wp_head', function () {
    $slug = rcc_get_current_slug();

    $map = [
        'megastruct-africa' => [
            'title'       => 'MEGASTRUCT AFRICA 2026',
            'description' => 'West Africa\'s premier infrastructure, construction & mining expo. October 11–13, 2026 at Landmark Centre, Victoria Island, Lagos. Exhibit, network and connect with industry leaders.',
            'image_fn'    => function () { return rcc_upload_asset_candidates(['2026/03/MEGASTRUCT.png', 'MEGASTRUCT.png']); },
            'url_slug'    => 'megastruct-africa',
        ],
        'messodex-west-africa' => [
            'title'       => 'MESSODEX WEST AFRICA 2026',
            'description' => 'West Africa\'s premier media, stage & sound technology expo. August 19–21, 2026 at Landmark Centre, Victoria Island, Lagos. Connect global AV and media brands with African buyers.',
            'image_fn'    => function () { return rcc_upload_asset_candidates(['2026/03/MESSODEX.png', 'MESSODEX.png']); },
            'url_slug'    => 'messodex-west-africa',
        ],
    ];

    if (!isset($map[$slug])) {
        return;
    }

    $m   = $map[$slug];
    $img = ($m['image_fn'])();
    $url = esc_url(rcc_get_page_url($m['url_slug']));
    $t   = esc_attr($m['title']);
    $d   = esc_attr($m['description']);

    echo '<meta name="description" content="' . $d . '">' . "\n";
    echo '<meta property="og:title" content="' . $t . '">' . "\n";
    echo '<meta property="og:description" content="' . $d . '">' . "\n";
    echo '<meta property="og:type" content="event">' . "\n";
    echo '<meta property="og:url" content="' . $url . '">' . "\n";
    if ($img) {
        echo '<meta property="og:image" content="' . esc_url($img) . '">' . "\n";
    }
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . $t . '">' . "\n";
    echo '<meta name="twitter:description" content="' . $d . '">' . "\n";
    if ($img) {
        echo '<meta name="twitter:image" content="' . esc_url($img) . '">' . "\n";
    }
    echo '<link rel="canonical" href="' . $url . '">' . "\n";
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
    echo '<tr>';
    echo '<th scope="row"><label for="' . $id . '">' . esc_html($label) . '</label></th>';
    echo '<td>';
    if ($type === 'textarea') {
        echo '<textarea id="' . $id . '" name="' . esc_attr($key) . '" rows="' . (int) ($extra['rows'] ?? 3) . '" class="large-text">' . esc_textarea($val) . '</textarea>';
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
        echo '<input type="text" id="' . $id . '" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '" class="regular-text">';
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
                rcc_admin_field_row('text',     'rcc_phone',            'Phone Number',    '+234 903 491 4948');
                rcc_admin_field_row('text',     'rcc_email',            'Main Email',      'info@radiantcc.com');
                rcc_admin_field_row('textarea', 'rcc_address',          'Office Address',  'Thuraya Crescent, Along Lagos-Ibadan Expressway, Mowe, Ogun State, Nigeria.', ['rows' => 3]);
                rcc_admin_field_row('text',     'rcc_social_facebook',  'Facebook URL');
                rcc_admin_field_row('text',     'rcc_social_instagram', 'Instagram URL');
                rcc_admin_field_row('text',     'rcc_social_youtube',   'YouTube URL');
                rcc_admin_field_row('text',     'rcc_social_linkedin',  'LinkedIn URL');
                rcc_admin_field_row('image',    'rcc_logo',             'RCC Logo');
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
                rcc_admin_field_row('text',  'mega_date',  'Event Date',  '11th – 13th October 2026');
                rcc_admin_field_row('text',  'mega_venue', 'Venue',       'Landmark Centre, Victoria Island, Lagos, Nigeria');
                rcc_admin_field_row('text',  'mega_email', 'Event Email', 'megastruct@radiantcc.com');
                rcc_admin_field_row('text',  'mega_phone', 'Event Phone', '+234 903 491 4948');
                rcc_admin_field_row('image', 'mega_logo',  'MEGASTRUCT Logo (shown on event hero page)');
                ?>
            </table>
            <?php submit_button('Save MEGASTRUCT Settings'); ?>
        </form>
    </div>
    <?php
}

function rcc_admin_page_messo()
{
    $fields = ['messo_date', 'messo_venue', 'messo_email', 'messo_phone', 'messo_logo'];
    $saved  = rcc_admin_save_settings($fields, 'rcc_messo');
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
                rcc_admin_field_row('text',  'messo_date',  'Event Date',  '19–21 August 2026');
                rcc_admin_field_row('text',  'messo_venue', 'Venue',       'Landmark Centre, Victoria Island, Lagos, Nigeria');
                rcc_admin_field_row('text',  'messo_email', 'Event Email', 'messodex@radiantcc.com');
                rcc_admin_field_row('text',  'messo_phone', 'Event Phone', '+234 903 491 4948');
                rcc_admin_field_row('image', 'messo_logo',  'MESSODEX Logo (shown on event hero page)');
                ?>
            </table>
            <?php submit_button('Save MESSODEX Settings'); ?>
        </form>
    </div>
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
