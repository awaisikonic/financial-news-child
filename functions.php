<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package UnderstrapChild
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Removes the parent themes stylesheet and scripts from inc/enqueue.php
 */
function understrap_remove_scripts() {
	wp_dequeue_style( 'understrap-styles' );
	wp_deregister_style( 'understrap-styles' );

	wp_dequeue_script( 'understrap-scripts' );
	wp_deregister_script( 'understrap-scripts' );
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );

/**
 * Enqueue our stylesheet and javascript file
 */
function theme_enqueue_styles() {

	// Get the theme data.
	$the_theme = wp_get_theme();

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	// Grab asset urls.
	$theme_styles  = "/css/child-theme.css";
	$theme_scripts = "/js/child-theme{$suffix}.js";

	wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . $theme_styles, array(), $the_theme->get( 'Version' ) );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . $theme_scripts, array(), $the_theme->get( 'Version' ), true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
    wp_localize_script('child-understrap-scripts', 'ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

/**
 * Load the child theme's text domain
 */
function add_child_theme_textdomain() {
	load_child_theme_textdomain( 'understrap-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );

/**
 * Overrides the theme_mod to default to Bootstrap 5
 *
 * This function uses the `theme_mod_{$name}` hook and
 * can be duplicated to override other theme settings.
 *
 * @return string
 */
function understrap_default_bootstrap_version() {
	return 'bootstrap5';
}
add_filter( 'theme_mod_understrap_bootstrap_version', 'understrap_default_bootstrap_version', 20 );

/**
 * Loads javascript for showing customizer warning dialog.
 */
function understrap_child_customize_controls_js() {
	wp_enqueue_script(
		'understrap_child_customizer',
		get_stylesheet_directory_uri() . '/js/customizer-controls.js',
		array( 'customize-preview' ),
		'20130508',
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'understrap_child_customize_controls_js' );

/**
 * Mega Menu Custom Fields.
 */
function mega_menu_custom_fields($item_id, $item, $depth, $args) {
    // Enable Mega Menu (Only for top-level items)
	if ($depth == 0) {
        $mega_menu_enabled = get_post_meta($item_id, '_mega_menu_enabled', true);
        $market_data_column = get_post_meta($item_id, '_market_data_column', true);
        ?>
        <p>
            <input type="checkbox" id="mega-menu-enabled-<?php echo $item_id; ?>" name="mega_menu_enabled[<?php echo $item_id; ?>]" value="yes" <?php checked($mega_menu_enabled, 'yes'); ?> />
            <label for="mega-menu-enabled-<?php echo $item_id; ?>">Enable Mega Menu</label>
        </p>
        <p>
            <input type="checkbox" id="market-data-column-<?php echo $item_id; ?>" name="market_data_column[<?php echo $item_id; ?>]" value="yes" <?php checked($market_data_column, 'yes'); ?> />
            <label for="market-data-column-<?php echo $item_id; ?>">Display Market Data Column</label>
        </p>
        <?php
    }

    // Column Title Checkbox (Only for submenu items)
    if ($depth == 1) {
        $is_column_title = get_post_meta($item_id, '_mega_menu_column_title', true);
        ?>
        <p>
            <input type="checkbox" id="mega-menu-column-title-<?php echo $item_id; ?>" name="mega_menu_column_title[<?php echo $item_id; ?>]" value="yes" <?php checked($is_column_title, 'yes'); ?> />
            <label for="mega-menu-column-title-<?php echo $item_id; ?>">Column Title</label>
        </p>
        <?php
    }
}
add_action('wp_nav_menu_item_custom_fields', 'mega_menu_custom_fields', 10, 4);

/**
 * Save Mega Menu Custom Fields.
 */
function mega_menu_save_custom_fields($menu_id, $menu_item_db_id) {
    if (isset($_POST['mega_menu_enabled'][$menu_item_db_id])) {
        update_post_meta($menu_item_db_id, '_mega_menu_enabled', 'yes');
    } else {
        delete_post_meta($menu_item_db_id, '_mega_menu_enabled');
    }

    if (isset($_POST['mega_menu_column_title'][$menu_item_db_id])) {
        update_post_meta($menu_item_db_id, '_mega_menu_column_title', 'yes');
    } else {
        delete_post_meta($menu_item_db_id, '_mega_menu_column_title');
    }
	if (isset($_POST['market_data_column'][$menu_item_db_id])) {
        update_post_meta($menu_item_db_id, '_market_data_column', 'yes');
    } else {
        delete_post_meta($menu_item_db_id, '_market_data_column');
    }
}
add_action('wp_update_nav_menu_item', 'mega_menu_save_custom_fields', 10, 2);

/**
 * Mega Menu Nav Walker
 */
class Mega_Menu_Walker extends Walker_Nav_Menu {
    private $in_mega_menu = false;
    private $first_column = true;
    private $has_market_data = false;
    private $mega_menu_parent_id = null;

    function start_lvl(&$output, $depth = 0, $args = null) {
        if ($depth == 0) {
            $output .= '<div class="mega-menu desktop-dropdown"><div class="mega-menu-columns desktop-dropdown-wrapper container">';
            $this->in_mega_menu = true;
            $this->first_column = true;
        } else {
            $output .= '<ul class="sub-menu">';
        }
    }

    function end_lvl(&$output, $depth = 0, $args = null) {
        if ($depth == 0 && $this->in_mega_menu) {
            // Ensure the Market Data column is inserted before closing
            if ($this->has_market_data) {
                $output .= '</div><div class="dropdown-column mega-menu-column market-data-column security-column"><h3>Top Securities</h3><div class="security-wrapper" id="market-container">' . do_shortcode('[market_data]') . '</div></div>';
            }

            $output .= '</div>'; // Close mega menu columns & mega menu container
            $this->in_mega_menu = false;
        } else {
            $output .= '</ul>';
        }
    }

    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $mega_menu_enabled = get_post_meta($item->ID, '_mega_menu_enabled', true);
        $market_data_column = get_post_meta($item->ID, '_market_data_column', true);
        $is_column_title = get_post_meta($item->ID, '_mega_menu_column_title', true);

        if ($depth == 0) {
            $output .= '<li class="desktop-nav-menu-item menu-item ' . ($mega_menu_enabled === 'yes' ? 'mega-menu-parent nav-menu-item-dropdwon' : '') . '">';
            $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>'. ($mega_menu_enabled === 'yes' ? '<i class="fa-solid fa-chevron-down"></i>' : '');

            if ($mega_menu_enabled === 'yes') {
                $this->mega_menu_parent_id = $item->ID; // Store parent ID
                if ($market_data_column === 'yes') {
                    $this->has_market_data = true;
                }
            }
        } elseif ($depth == 1 && $this->in_mega_menu) {
            if ($is_column_title === 'yes') {
                if (!$this->first_column) {
                    $output .= '</ul></div>';
                }
                $output .= '<div class="mega-menu-column dropdown-column"><h3>' . esc_html($item->title) . '</h3><ul class="sub-menu">';
                $this->first_column = false;
            } else {
                $output .= '<li><a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
            }
        } else {
            $output .= '<li><a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
        }
    }

    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= '</li>';
    }
}

/**
 * Fetch Market Data
 */
function fetch_market_data() {
    $api_key = 'c88df2cb2c1882efa232813667aca20c0192b64d2b554196bb19c94732a76551';
    $target_indices = [
        ".DJI:INDEXDJX" => ["name" => "Dow Jones Industrial Average", "googleSymbol" => ".DJI:INDEXDJX"],
        ".IXIC:INDEXNASDAQ" => ["name" => "Nasdaq Composite", "googleSymbol" => ".IXIC:INDEXNASDAQ"],
        "SX5E:INDEXSTOXX" => ["name" => "STOXX Europe 600", "googleSymbol" => "SXXP:INDEXSTOXX"]
    ];
    
    $query = implode(",", array_keys($target_indices));
    $url = "https://serpapi.com/search.json?engine=google_finance&q={$query}&api_key={$api_key}";
    
    $response = wp_remote_get($url);
    
    if (is_wp_error($response)) {
        return '<p class="error">Error fetching market data</p>';
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (empty($data['markets'])) {
        return '<p class="error">Market data not available</p>';
    }
    
    $all_markets = array_merge(
        $data['markets']['us'] ?? [],
        $data['markets']['europe'] ?? [],
        $data['markets']['asia'] ?? [],
        $data['markets']['futures'] ?? []
    );
    
    $indices_data = array_filter($all_markets, function ($market) use ($target_indices) {
        return isset($target_indices[$market['stock']]);
    });
    
    if (empty($indices_data)) {
        return '<p class="error">Market data not available</p>';
    }
    
    $output = '<div class="market-container">';
    foreach ($indices_data as $market) {
        $index = $target_indices[$market['stock']];
        $change_value = $market['price_movement']['percentage'] ?? 0;
        $direction = $change_value >= 0 ? 'up' : 'down';
        $price = isset($market['price']) ? number_format($market['price'], 2) : 'N/A';
        
        $output .= '<a href="https://www.google.com/finance/quote/' . esc_attr($index['googleSymbol']) . '" target="_blank" class="security-box">';
        $output .= '<p class="security-title">' . esc_html($index['name']) . '</p>';
        $output .= '<p class="security-price">$' . esc_html($price) . '</p>';
        $output .= '<p class="security-change" style="color: ' . ($direction === 'up' ? '#3F8A42' : '#B74940') . ';">';
        $output .= ($direction === 'up' ? '▲' : '▼') . ' ' . abs($change_value) . '%';
        $output .= '</p></a>';
    }
    $output .= '</div>';
    
    return $output;
}

/**
 * Market Data Shortcode
 */
function market_data_shortcode() {
    return fetch_market_data();
}
add_shortcode('market_data', 'market_data_shortcode');

/**
 * Fetch Market Ticker Data
 */
function fetch_market_ticker_data($category) {
    $api_key = 'c88df2cb2c1882efa232813667aca20c0192b64d2b554196bb19c94732a76551';
    $data_config = [
        'securities' => [
            ".INX:INDEXSP" => "S&P 500",
            ".IXIC:INDEXNASDAQ" => "Nasdaq Composite",
            "UKX:INDEXFTSE" => "FTSE",
            "US10:LON" => "US 10Y Yield",
            "CLW00:NYMEX" => "Crude Oil",
            "GCW00:COMEX" => "Gold",
            "EUR-USD" => "Euro/USD",
            "GBP-USD" => "GBP/USD"
        ],
        'indices' => [
            ".DJI:INDEXDJX" => "Dow Jones Industrial Average",
            "SXXP:INDEXSTOXX" => "STOXX Europe 600",
            "UKX:INDEXFTSE" => "FTSE 100",
            "NI225:INDEXNIKKEI" => "Nikkei 225",
            "HSI:INDEXHANGSENG" => "Hang Seng"
        ],
        'rates' => [
            "FVX:CBOE" => "US 5Y Yield",
            "DE10YR:CBOE" => "Germany 10Y",
            "JP10YRY:CBOE" => "Japan 10Y"
        ]
    ];

    if (!isset($data_config[$category])) {
        return '<p class="error">Invalid category</p>';
    }

    $query = implode(",", array_keys($data_config[$category]));
    $url = "https://serpapi.com/search.json?engine=google_finance&q={$query}&api_key={$api_key}";

    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        return '<p class="error">Error fetching market ticker data</p>';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (empty($data['markets'])) {
        return '<p class="error">Market data not available</p>';
    }

    $all_markets = array_merge(
        $data['markets']['us'] ?? [],
        $data['markets']['europe'] ?? [],
        $data['markets']['asia'] ?? [],
        $data['markets']['commodities'] ?? [],
        $data['markets']['currencies'] ?? [],
        $data['markets']['futures'] ?? []
    );

    $output = '';
    foreach ($data_config[$category] as $symbol => $name) {
        $market = array_filter($all_markets, function ($m) use ($symbol) {
            return $m['stock'] === $symbol;
        });
        
        $market = reset($market) ?: [
            'price' => 'N/A',
            'price_movement' => ['percentage' => 0, 'movement' => 'flat']
        ];
        
        $change_value = $market['price_movement']['percentage'] ?? 0;
        $direction = strtolower($market['price_movement']['movement'] ?? 'flat');
        $price = isset($market['price']) && is_numeric($market['price']) ? number_format((float) $market['price'], 2) : 'N/A';

        $output .= '<button class="market-ticker-btn">';
        $output .= '<div class="market-ticker-name">' . esc_html($name) . '</div>';
        $output .= '<div class="market-ticker-price">$' . esc_html($price) . '</div>';
        $output .= '<div class="market-ticker-change ' . esc_attr($direction) . '">';
        $output .= ($direction === 'up' ? '▲' : '▼') . ' ' . abs($change_value) . '%';
        $output .= '</div></button>';
    }
    
    return $output;
}

/**
 * Market Ticker Shortcode
 */
function market_ticker_shortcode($atts) {
    $atts = shortcode_atts(['category' => 'securities'], $atts);
    return fetch_market_ticker_data($atts['category']);
}
add_shortcode('market_ticker', 'market_ticker_shortcode');

/**
 * Market Ticker Ajax Handler
 */
function market_ticker_ajax_handler() {
    if (!isset($_GET['category'])) {
        wp_send_json_error(['message' => 'Missing category parameter'], 400);
    }

    $category = sanitize_text_field($_GET['category']);
    $html = fetch_market_ticker_data($category);

    wp_send_json_success(['html' => wp_kses_post($html)]);
}
add_action('wp_ajax_market_ticker', 'market_ticker_ajax_handler');
add_action('wp_ajax_nopriv_market_ticker', 'market_ticker_ajax_handler');

/**
 * Gpt Enqueue Editor Script
 */
function gpt_enqueue_editor_script($hook) {
    if ('post.php' === $hook || 'post-new.php' === $hook) {
        wp_enqueue_script(
            'gpt-editor-rewrite',
            get_stylesheet_directory_uri() .'/js/rewrite.js',
            ['jquery'],
            '1.0',
            true
        );

        $openai_platform = get_field('openai_platform', 'option');

        wp_localize_script('gpt-editor-rewrite', 'gpt_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'api_key'  => $openai_platform,
        ]);
    }
}
add_action('admin_enqueue_scripts', 'gpt_enqueue_editor_script');

/**
 * GPT Rewrite Description
 */
function gpt_rewrite_description() {
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(['message' => 'Unauthorized'], 403);
    }

    $post_id = intval($_POST['post_id']);
    $description = sanitize_text_field($_POST['description']);
    $openai_platform = get_field('openai_platform', 'option');

    $response = wp_remote_post('https://api.openai.com/v1/completions', [
        'headers' => [
            'Authorization' => 'Bearer ' . $openai_platform,
            'Content-Type'  => 'application/json',
        ],
        'body' => json_encode([
            'model' => 'gpt-4',
            'prompt' => "Rewrite the following post description in a more engaging way:\n\n{$description}",
            'max_tokens' => 150,
        ]),
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'API request failed']);
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    $rewritten_description = $body['choices'][0]['text'] ?? '';

    wp_send_json_success(['description' => trim($rewritten_description)]);
}
add_action('wp_ajax_gpt_rewrite_description', 'gpt_rewrite_description');

/**
 * GPT Rewrite Custom Field
 */
function gpt_rewrite_custom_field() {
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(['message' => 'Unauthorized'], 403);
    }

    $post_id = intval($_POST['post_id']);
    $custom_field_content = sanitize_textarea_field($_POST['custom_field_content']);
    $openai_platform = get_field('openai_platform', 'option');

    // Call OpenAI API
    $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
        'headers' => [
            'Authorization' => 'Bearer ' . $openai_platform,
            'Content-Type'  => 'application/json',
        ],
        'body' => json_encode([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful AI that rewrites content in an engaging way.'],
                ['role' => 'user', 'content' => "Rewrite the following post description, make sure to add html tags as well but dont add any extra div and styles just tags for headings and pragraphs. All headings should be in h3 tags:\n\n" . $custom_field_content],
            ],
            'max_tokens' => 250,
        ]),
        'timeout' => 60, // Increased timeout to 30 seconds
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'WP_Error: ' . $response->get_error_message()]);
    }
    
    $body = json_decode(wp_remote_retrieve_body($response), true);
    
    if (!$body || isset($body['error'])) {
        wp_send_json_error(['message' => 'OpenAI Error: ' . ($body['error']['message'] ?? 'Unknown error')]);
    }
    
    wp_send_json_success(['rewritten_content' => trim($body['choices'][0]['message']['content'])]);
}
add_action('wp_ajax_gpt_rewrite_custom_field', 'gpt_rewrite_custom_field');

/**
 * Disable Block Editor
 */
add_filter('use_block_editor_for_post_type', '__return_false');

/**
 * Latest Posts Shortcode
 */
function latest_news_section() {
    ob_start();
    ?>
    <div class="future-section__latest-news">
        <div class="future-section__news-list">
            <div class="future-section__latest-header">
                <h1>Latest</h1>
                <div class="future-section__latest-header-catg">
                    <button class="future-section__latest-header-catg-btn" id="toggleDropdown">
                        All categories <i class="fas fa-chevron-down"></i>
                    </button>
                    <ul class="future-section__latest-header-catg-list">
                        <li class="future-section__latest-header-catg-item" data-cat="0">All categories</li>
                        <?php 
                        $categories = get_categories(['parent' => 0, 'hide_empty' => true, 'exclude'     => 1,]);
                        foreach ($categories as $category) : ?>
                            <li class="future-section__latest-header-catg-item" data-cat="<?php echo $category->term_id; ?>">
                                <?php echo esc_html($category->name); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div id="latest-news-container">
                <?php
                $latest_posts = new WP_Query([
                    'post_type'      => 'post',
                    'posts_per_page' => 6,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ]);

                if ($latest_posts->have_posts()) :
                    while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
                        <a href="<?php the_permalink(); ?>" class="future-section__news-link">
                            <article class="future-section__news-item">
                                <time class="future-section__news-time"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></time>
                                <h3 class="future-section__news-title"><?php the_title(); ?></h3>
                            </article>
                        </a>
                    <?php endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
        <div class="future-section__see-all-latest">
            <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>">See all latest <i class="fas fa-chevron-right"></i></a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('latest_news', 'latest_news_section');

/**
 * Ajax handler to fecth Latest Posts by category
 */
function fetch_latest_posts() {
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => 6,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    if ($category_id) {
        $args['cat'] = $category_id;
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post(); ?>
            <a href="<?php the_permalink(); ?>" class="future-section__news-link">
                <article class="future-section__news-item">
                    <time class="future-section__news-time"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></time>
                    <h3 class="future-section__news-title"><?php the_title(); ?></h3>
                </article>
            </a>
        <?php endwhile;
    else :
        echo '<p>No posts found.</p>';
    endif;

    wp_die();
}
add_action('wp_ajax_fetch_latest_posts', 'fetch_latest_posts');
add_action('wp_ajax_nopriv_fetch_latest_posts', 'fetch_latest_posts');

/**
 * track Post Views
 */
function track_post_views($post_id) {
    if (!is_single()) return;
    
    $views = get_post_meta($post_id, 'post_views_count', true);
    $views = ($views == '') ? 1 : (int) $views + 1;
    update_post_meta($post_id, 'post_views_count', $views);
}
add_action('wp_head', function() {
    if (is_single()) track_post_views(get_the_ID());
});

/**
 * Localize Ajax url
 */
function add_ajax_url_script() {
    ?>
    <script type="text/javascript">
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    </script>
    <?php
}
add_action('wp_footer', 'add_ajax_url_script');

/**
 * Ajax handler to fecth Latest Posts by category Blog Page
 */
function fetch_latest_posts_blog() {
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => 6,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    if ($category_id) {
        $args['cat'] = $category_id;
    }

    // Get total posts in this category
    $count_args = $args;
    $count_args['posts_per_page'] = -1; // Get all posts count
    $total_query = new WP_Query($count_args);
    $total_posts = $total_query->found_posts;

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post(); ?>
            <div class="latest-post-news-card">
                <div>
                    <p><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></p>
                    <h5 class="entry-title"><a href="<?php the_permalink(); ?>" class="future-section__news-link"><?php the_title(); ?></a></h5>
                </div>
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>" class="future-section__news-link">
                        <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" 
                            alt="<?php the_title_attribute(); ?>">
                    </a>
                <?php endif; ?>
            </div>
        <?php endwhile;
    else :
        echo '<p>No posts found.</p>';
    endif;

    $output = ob_get_clean();

    wp_send_json([
        'html' => $output,
        'total_posts' => $total_posts,
    ]);

    wp_die();
}
add_action('wp_ajax_fetch_latest_posts_blog', 'fetch_latest_posts_blog');
add_action('wp_ajax_nopriv_fetch_latest_posts_blog', 'fetch_latest_posts_blog');

/**
 * Ajax handler to fecth Latest Posts on Parent category page
 */

function load_more_parent_cat_news() {
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $posts_per_page = 6;
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    // Ensure 'displayed_posts' is set and not empty
    if (isset($_POST['displayed_posts']) && !empty($_POST['displayed_posts'])) {
        // Convert the comma-separated string to an array
        $displayed_posts = explode(',', $_POST['displayed_posts']);
        
        // Use array_map to ensure all values are integers
        $excluded_posts = array_map('intval', $displayed_posts);
    } else {
        $excluded_posts = [];
    }

    if ($category_id == 0) {
        echo 'no_more_posts';
        die();
    }

    $category_ids = get_term_children($category_id, 'category');
    $category_ids[] = $category_id;

    if (empty($category_ids)) {
        echo 'no_more_posts';
        die();
    }
    $more_news_query = new WP_Query([
        'category__in' => $category_ids,
        'posts_per_page' => $posts_per_page,
        'offset' => $offset,
        'post_status' => 'publish',
        'post__not_in' => $excluded_posts,
    ]);

    if ($more_news_query->have_posts()):
        while ($more_news_query->have_posts()): $more_news_query->the_post(); ?>
            <div class="industrie-news-card">
                <div>
                    <p><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></p>
                    <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                </div>
                <?php if (has_post_thumbnail()): ?>
                    <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
                <?php endif; ?>
            </div>
        <?php endwhile;
        wp_reset_postdata();
    else:
        echo 'no_more_posts';
    endif;

    die();
}
add_action('wp_ajax_load_more_parent_cat_news', 'load_more_parent_cat_news');
add_action('wp_ajax_nopriv_load_more_parent_cat_news', 'load_more_parent_cat_news');
