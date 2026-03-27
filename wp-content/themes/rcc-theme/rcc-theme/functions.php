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
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap',
        [],
        null
    );
    wp_enqueue_style('rcc-style', get_stylesheet_uri(), [], '2.0.0');
    wp_enqueue_style('rcc-main', get_template_directory_uri() . '/assets/css/rcc-main.css', ['rcc-fonts'], '2.0.0');
    wp_enqueue_script('rcc-main', get_template_directory_uri() . '/assets/js/main.js', [], '2.0.0', true);
});

function rcc_site_config()
{
    return [
        'company' => 'Radiant Creative Concepts Limited',
        'short_company' => 'RCC',
        'tagline' => 'Event Management | Exhibitions | Trade Shows | Corporate & Social Events',
        'phone' => '+234 903 491 4948',
        'email' => 'info@radiantcc.com',
        'website' => 'www.radiantcc.com',
        'website_url' => 'https://www.radiantcc.com',
        'address' => 'RADIANT CREATIVE CONCEPTS LIMITED (EVENTS & EXHIBITIONS), Thuraya Crescent, Along Lagos-Ibadan Expressway, Mowe, Ogun State, Nigeria.',
        'socials' => [
            ['label' => 'FB', 'url' => '#'],
            ['label' => 'IG', 'url' => '#'],
            ['label' => 'YouTube', 'url' => '#'],
            ['label' => 'LinkedIn', 'url' => '#'],
        ],
        'legal' => [
            ['label' => 'Privacy Policy', 'url' => '#'],
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
        ['label' => 'Home', 'slug' => ''],
        ['label' => 'About Us', 'slug' => 'about-us'],
        ['label' => 'Our Events', 'slug' => 'exhibitions'],
        ['label' => 'Gallery', 'slug' => 'gallery'],
        ['label' => 'Contact Us', 'slug' => 'contact-us'],
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
    return content_url('/uploads/' . ltrim($filename, '/'));
}

function rcc_upload_asset_candidates($candidates)
{
    foreach ($candidates as $candidate) {
        $relative = ltrim($candidate, '/');
        $absolute = wp_normalize_path(WP_CONTENT_DIR . '/uploads/' . $relative);
        if (file_exists($absolute)) {
            return content_url('/uploads/' . $relative);
        }
    }

    return content_url('/uploads/' . ltrim($candidates[0], '/'));
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
                'description' => 'An international platform for construction machinery, infrastructure solutions, mining equipment, investors, and industry leaders ready to shape Africa&apos;s growth.',
                'url' => rcc_get_page_url('megastruct-africa'),
                'image' => rcc_upload_asset_candidates(['2026/03/MEGASTRUCT.png', 'megastruct.jpeg']),
                'meta' => '11th - 13th October 2026 | Landmark Centre, Victoria Island, Lagos',
                'theme' => 'megastruct',
            ],
            [
                'title' => 'MESSODEX WEST AFRICA',
                'subtitle' => 'Media, Stage & Sound Technology Expo',
                'description' => 'A bold showcase for media technology, stage engineering, professional audio, lighting systems, and creative production built for the West African market.',
                'url' => rcc_get_page_url('messodex-west-africa'),
                'image' => rcc_upload_asset_candidates(['2026/03/MESSEDEX.png', '2026/03/MESSODEX.png', 'messodex.jpeg']),
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
        'title' => 'MEGASTRUCT AFRICA',
        'subtitle' => 'Mega Infrastructure, Construction & Mining Equipment Expo',
        'date' => '11th - 13th October 2026',
        'venue' => 'Landmark Centre, Victoria Island, Lagos, Nigeria',
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
    ];
}

function rcc_get_messodex_data()
{
    return [
        'title' => 'MESSODEX WEST AFRICA',
        'subtitle' => 'Premier Media, Stage, & Sound Technology Expo',
        'date' => '19-21 August 2026',
        'venue' => 'Landmark Centre, Victoria Island, Lagos, Nigeria',
        'intro' => 'Join the largest and most influential platform connecting global leaders in media technology, professional audio, lighting, broadcasting solutions, stage engineering, creative production, and live entertainment equipment with the fast-growing West African market.',
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
                <h1>Organizing Africa&apos;s Leading Exhibitions &amp; Trade Shows.<br><span>Building Connections, Creating Opportunities.</span></h1>
                <div class="rcc-actions rcc-actions--hero">
                    <?php rcc_button('Learn More', rcc_get_page_url('about-us')); ?>
                </div>
            </div>
        </div>
    </section>

    <section class="rcc-home-events">
        <div class="rcc-shell">
            <div class="rcc-section-title">
                <h2>Our Featured Events</h2>
            </div>
            <div class="rcc-home-events__grid">
                <?php foreach ($data['events'] as $event) : ?>
                    <article class="rcc-event-card-premium">
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
            <div class="rcc-section-title">
                <h2>Why Choose Radiant Creative?</h2>
                <p>We deliver world-class events through excellence, professionalism, and creativity.</p>
            </div>
            <div class="rcc-why-grid">
                <div class="rcc-why-item">
                    <div class="rcc-why-icon">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                    </div>
                    <div class="rcc-why-text">
                        <h3>Experience</h3>
                        <p><?php echo esc_html($data['reasons'][0]['text']); ?></p>
                    </div>
                </div>
                <div class="rcc-why-item">
                    <div class="rcc-why-icon">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.22 4.22l2.12 2.12M17.66 17.66l2.12 2.12M2 12h3M19 12h3M4.22 19.78l2.12-2.12M17.66 6.34l2.12-2.12"/></svg>
                    </div>
                    <div class="rcc-why-text">
                        <h3>Innovation</h3>
                        <p><?php echo esc_html($data['reasons'][2]['text']); ?></p>
                    </div>
                </div>
                <div class="rcc-why-item">
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
    </section>

    <section class="rcc-home-contact">
        <div class="rcc-shell">
            <div class="rcc-home-contact__grid">
                <div class="rcc-home-contact__form">
                    <h2>Get in Touch</h2>
                    <p>Reach out for bookings, partnerships, or exhibition inquiries.</p>
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
    ?>
    <section class="rcc-page-hero">
        <div class="rcc-shell">
            <h1>Radiant Creative Concepts Limited</h1>
            <p><?php echo esc_html($data['hero_title'] . ' ' . $data['hero_text']); ?></p>
        </div>
    </section>

    <section class="rcc-section">
        <div class="rcc-shell rcc-stack">
            <article class="rcc-card">
                <h2>Who We Are</h2>
                <p><?php echo esc_html($data['intro']); ?></p>
                <p><?php echo esc_html($data['who']); ?></p>
            </article>
            <article class="rcc-card">
                <h2>Our Core Services</h2>
                <?php rcc_render_list($data['services']); ?>
            </article>
            <article class="rcc-card">
                <h2>Why Choose RCCL?</h2>
                <?php rcc_render_list($data['why']); ?>
            </article>
            <div class="rcc-card-grid rcc-card-grid--two">
                <article class="rcc-card">
                    <h2>Our Vision</h2>
                    <p><?php echo esc_html($data['vision']); ?></p>
                </article>
                <article class="rcc-card">
                    <h2>Our Mission</h2>
                    <p><?php echo esc_html($data['mission']); ?></p>
                </article>
            </div>
            <div class="rcc-card-grid rcc-card-grid--two">
                <article class="rcc-card">
                    <h2>Our Core Values</h2>
                    <?php rcc_render_list($data['values']); ?>
                </article>
                <article class="rcc-card">
                    <h2>What Makes Us Different</h2>
                    <?php rcc_render_list($data['difference']); ?>
                </article>
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
            <h1>Our Event Solutions</h1>
            <p>From exhibitions to technical production, we deliver complete event solutions built around strategy, professionalism, and measurable impact.</p>
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
    $messodex = rcc_get_messodex_data();
    $mega = rcc_get_megastruct_data();
    ?>
    <section class="rcc-page-hero">
        <div class="rcc-shell">
            <h1>Our Flagship Exhibitions</h1>
            <p>Two high-impact event platforms connecting global exhibitors with African industries, buyers, and opportunities.</p>
        </div>
    </section>

    <section class="rcc-section">
        <div class="rcc-shell">
            <div class="rcc-card-grid rcc-card-grid--two">
                <article class="rcc-card rcc-event-card">
                    <span class="rcc-card-tag">Media, Stage & Sound</span>
                    <h2><?php echo esc_html($messodex['title']); ?></h2>
                    <p><?php echo esc_html($messodex['intro']); ?></p>
                    <p><strong><?php echo esc_html($messodex['date']); ?></strong><br><?php echo esc_html($messodex['venue']); ?></p>
                    <?php rcc_button('View Event', rcc_get_page_url('messodex-west-africa')); ?>
                </article>
                <article class="rcc-card rcc-event-card">
                    <span class="rcc-card-tag">Infrastructure & Construction</span>
                    <h2><?php echo esc_html($mega['title']); ?></h2>
                    <p><?php echo esc_html($mega['subtitle']); ?></p>
                    <p><strong><?php echo esc_html($mega['date']); ?></strong><br><?php echo esc_html($mega['venue']); ?></p>
                    <?php rcc_button('View Event', rcc_get_page_url('megastruct-africa')); ?>
                </article>
            </div>
        </div>
    </section>
    <?php
}

function rcc_render_event_page($slug)
{
    $data = $slug === 'megastruct-africa' ? rcc_get_megastruct_data() : rcc_get_messodex_data();
    $book_url = rcc_get_page_url('book-a-stand');
    ?>
    <section class="rcc-page-hero rcc-page-hero--event">
        <div class="rcc-shell">
            <h1><?php echo esc_html($data['title']); ?></h1>
            <p><?php echo esc_html($data['subtitle']); ?></p>
            <div class="rcc-meta">
                <span><?php echo esc_html($data['date']); ?></span>
                <span><?php echo esc_html($data['venue']); ?></span>
            </div>
            <?php if (!empty($data['intro'])) : ?>
                <p class="rcc-lead"><?php echo esc_html($data['intro']); ?></p>
            <?php endif; ?>
            <p class="rcc-event-tagline"><?php echo esc_html($data['tagline']); ?></p>
            <div class="rcc-actions">
                <?php rcc_button('Register as Exhibitor', $book_url); ?>
                <?php rcc_button('Request Brochure', rcc_get_page_url('contact-us'), 'rcc-btn-secondary'); ?>
                <?php rcc_button('Book a Stand', $book_url, 'rcc-btn-secondary'); ?>
            </div>
        </div>
    </section>

    <section class="rcc-section">
        <div class="rcc-shell rcc-stack">
            <article class="rcc-card">
                <h2><?php echo $slug === 'megastruct-africa' ? 'About the Expo' : 'About MESSODEX'; ?></h2>
                <?php foreach ($data['about'] as $paragraph) : ?>
                    <p><?php echo esc_html($paragraph); ?></p>
                <?php endforeach; ?>
            </article>

            <article class="rcc-card rcc-card--image">
                <img src="<?php echo esc_url(rcc_upload_asset_candidates(['2026/03/BOOTH.png'])); ?>" alt="Exhibition booth setup">
            </article>

            <?php if ($slug === 'megastruct-africa') : ?>
                <div class="rcc-card-grid rcc-card-grid--two">
                    <article class="rcc-card">
                        <h2>Event Highlights</h2>
                        <?php rcc_render_list($data['highlights']); ?>
                    </article>
                    <article class="rcc-card">
                        <h2>Exhibitor Categories</h2>
                        <?php rcc_render_list($data['exhibitors']); ?>
                    </article>
                </div>
                <div class="rcc-card-grid rcc-card-grid--two">
                    <article class="rcc-card">
                        <h2>Visitor Categories</h2>
                        <?php rcc_render_list($data['visitors']); ?>
                    </article>
                    <article class="rcc-card">
                        <h2>Sponsorship Opportunities</h2>
                        <?php rcc_render_list($data['sponsorship']); ?>
                        <p>Gain unmatched visibility across West Africa&apos;s infrastructure and construction industry.</p>
                    </article>
                </div>
                <article class="rcc-card">
                    <h2>Booth Options</h2>
                    <div class="rcc-card-grid rcc-card-grid--three">
                        <?php foreach ($data['booths'] as $booth) : ?>
                            <div class="rcc-mini-card">
                                <h3><?php echo esc_html($booth['title']); ?></h3>
                                <p><?php echo esc_html($booth['text']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>
            <?php else : ?>
                <div class="rcc-card-grid rcc-card-grid--two">
                    <article class="rcc-card">
                        <h2>Why MESSODEX Matters</h2>
                        <?php rcc_render_list($data['matters']); ?>
                    </article>
                    <article class="rcc-card">
                        <h2>Why Exhibit</h2>
                        <?php rcc_render_list($data['why_exhibit']); ?>
                    </article>
                </div>
                <div class="rcc-card-grid rcc-card-grid--two">
                    <article class="rcc-card">
                        <h2>Who Should Exhibit</h2>
                        <?php rcc_render_list($data['exhibitors']); ?>
                    </article>
                    <article class="rcc-card">
                        <h2>Visitors Profile</h2>
                        <?php rcc_render_list($data['visitors']); ?>
                    </article>
                </div>
                <div class="rcc-card-grid rcc-card-grid--two">
                    <article class="rcc-card">
                        <h2>Conference & Masterclasses</h2>
                        <?php rcc_render_list($data['conference']); ?>
                    </article>
                    <article class="rcc-card">
                        <h2>Exhibitor Value Package</h2>
                        <?php rcc_render_list($data['package']); ?>
                    </article>
                </div>
                <article class="rcc-card">
                    <h2>Sponsorship Opportunities</h2>
                    <?php rcc_render_list($data['sponsorship']); ?>
                    <p>Gain unmatched visibility across West Africa&apos;s creative and media ecosystem.</p>
                </article>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

function rcc_render_book_a_stand_page()
{
    $sent = isset($_GET['sent']) && $_GET['sent'] === '1';
    $failed = isset($_GET['sent']) && $_GET['sent'] === '0';
    ?>
    <section class="rcc-page-hero">
        <div class="rcc-shell">
            <h1>Exhibition Stand Enquiry</h1>
            <p>Tell us about your business, preferred event, and booth size. Our team will respond with the next steps.</p>
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
    $groups = [
        'Photos Of Booths' => ['Shell Scheme (Standard Booth)', 'Custom Booths'],
        'Live Events Pictures' => ['Conference sessions', 'Networking moments', 'Product showcases'],
        'Partners/Sponsors' => ['Featured partner wall', 'Sponsor branding', 'Collaboration highlights'],
    ];
    ?>
    <section class="rcc-page-hero">
        <div class="rcc-shell">
            <h1>Visual Highlights</h1>
            <p>Showcase pages for booth designs, live event moments, and partner visibility. You can replace these placeholders with your real photos anytime from WordPress media.</p>
        </div>
    </section>

    <section class="rcc-section">
        <div class="rcc-shell rcc-stack">
            <?php foreach ($groups as $title => $items) : ?>
                <article class="rcc-card">
                    <h2><?php echo esc_html($title); ?></h2>
                    <div class="rcc-gallery-grid">
                        <?php foreach ($items as $item) : ?>
                            <div class="rcc-gallery-item">
                                <img class="rcc-gallery-item__visual-image" src="<?php echo esc_url(rcc_upload_asset_candidates(['2026/03/BOOTH.png'])); ?>" alt="<?php echo esc_attr($item); ?>">
                                <span><?php echo esc_html($item); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
}

function rcc_render_contact_page()
{
    $site = rcc_site_config();
    $sent = isset($_GET['sent']) && $_GET['sent'] === '1';
    $failed = isset($_GET['sent']) && $_GET['sent'] === '0';
    ?>
    <section class="rcc-page-hero rcc-page-hero--enquiry">
        <div class="rcc-shell">
            <h1>Exhibiting Enquiry</h1>
            <p>Please complete the form below and our team will get back to you shortly.</p>
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
