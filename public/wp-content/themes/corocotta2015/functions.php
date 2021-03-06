<?php
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function corocotta_setup() {

  /*
   * Make theme available for translation.
   * Translations can be filed in the /languages/ directory.
   */
  load_theme_textdomain( 'corocotta', get_template_directory() . '/languages' );

  // Add default posts and comments RSS feed links to head.
  add_theme_support( 'automatic-feed-links' );

  /*
   * Let WordPress manage the document title.
   * By adding theme support, we declare that this theme does not use a
   * hard-coded <title> tag in the document head, and expect WordPress to
   * provide it for us.
   */
  add_theme_support( 'title-tag' );

  /*
   * Enable support for Post Thumbnails on posts and pages.
   *
   * See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
   */
  add_theme_support( 'post-thumbnails' );
  set_post_thumbnail_size( 825, 510, true );

  // This theme uses wp_nav_menu() in two locations.
  register_nav_menus( array(
    'primary' => __( 'Primary Menu',      'corocotta' ),
    'social'  => __( 'Social Links Menu', 'corocotta' ),
  ) );

  /*
   * Switch default core markup for search form, comment form, and comments
   * to output valid HTML5.
   */
  add_theme_support( 'html5', array(
    'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
  ) );

  /*
   * Enable support for Post Formats.
   *
   * See: https://codex.wordpress.org/Post_Formats
   */
  //add_theme_support( 'post-formats', array(
  //	'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
  //) );


  /*
   * This theme styles the visual editor to resemble the theme style,
   * specifically font, colors, icons, and column width.
   */
  add_editor_style( array( 'css/editor-style.css', 'genericons/genericons.css', corocotta_fonts_url() ) );
}
add_action( 'after_setup_theme', 'corocotta_setup' );

/**
 * Register widget area.
 *
 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
 */
function corocotta_widgets_init() {
  register_sidebar( array(
    'name'          => __( 'Widget Area', 'coroctta' ),
    'id'            => 'sidebar-1',
    'description'   => __( 'Add widgets here to appear in your sidebar.', 'corocotta' ),
    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
    'after_widget'  => '</aside>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
  ) );
}
add_action( 'widgets_init', 'corocotta_widgets_init' );

/**
 * Register Google fonts
 *
 * @return string Google fonts URL for the theme.
 */
function corocotta_fonts_url() {
  $fonts_url = '';
  $fonts     = array();
  $subsets   = 'latin';

  $fonts[] = 'Noto Sans:400italic,700italic,400,700';
  $fonts[] = 'Noto Serif:400italic,700italic,400,700';
  $fonts[] = 'Inconsolata:400,700';

  if ( $fonts ) {
    $fonts_url = add_query_arg( array(
      'family' => urlencode( implode( '|', $fonts ) ),
      'subset' => urlencode( $subsets ),
    ), '//fonts.googleapis.com/css' );
  }

  return $fonts_url;
}

/**
 * JavaScript Detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 */
function corocotta_javascript_detection() {
  echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'corocotta_javascript_detection', 0 );

/**
 * Enqueue scripts and styles.
 */
function corocotta_scripts() {
  // Add custom fonts, used in the main stylesheet.
  wp_enqueue_style( 'coroctta-fonts', corocotta_fonts_url(), array(), null );

  // Load our main stylesheet.
  wp_enqueue_style( 'corocotta-style', get_template_directory_uri() . '/css/main.css' );

  wp_enqueue_script( 'corocotta-script', get_template_directory_uri() . '/js/main.js', array( 'jquery' ), '0.1.0', true );



}
add_action( 'wp_enqueue_scripts', 'corocotta_scripts' );


/**
* update jQuery and load from CDN
*/
function corocotta_wp_print_scripts() {
  // Don't affect admin jQuery
  if (is_admin()) return;

  // Don't affect login or signup jQuery
  global $pagenow;
  if ($pagenow === 'wp-login.php' || $pagenow === 'wp-register.php') return;

  wp_deregister_script('jquery-core');
  wp_deregister_script('jquery-migrate');

  if (WP_DEBUG) {
    wp_register_script('jquery-core', '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.js', false, '1.11.1');
    wp_register_script('jquery-migrate', '//code.jquery.com/jquery-migrate-1.2.1.js', false, '1.2.1');
  } else {
    wp_register_script('jquery-core', '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js', false, '1.11.1');
    wp_register_script('jquery-migrate', '//code.jquery.com/jquery-migrate-1.2.1.min.js', false, '1.2.1');
  }
}
add_action('wp_print_scripts', 'corocotta_wp_print_scripts');


/**
 * Add featured image as background image to post navigation elements.
 *
 * @see wp_add_inline_style()
 */
function corocotta_post_nav_background() {
  if ( ! is_single() ) {
    return;
  }

  $previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
  $next     = get_adjacent_post( false, '', false );
  $css      = '';

  if ( is_attachment() && 'attachment' == $previous->post_type ) {
    return;
  }

  if ( $previous &&  has_post_thumbnail( $previous->ID ) ) {
    $prevthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $previous->ID ), 'post-thumbnail' );
    $css .= '
    .post-navigation .nav-previous { background-image: url(' . esc_url( $prevthumb[0] ) . '); }
    .post-navigation .nav-previous .post-title, .post-navigation .nav-previous a:hover .post-title, .post-navigation .nav-previous .meta-nav { color: #fff; }
    .post-navigation .nav-previous a:before { background-color: rgba(0, 0, 0, 0.4); }
    ';
  }

  if ( $next && has_post_thumbnail( $next->ID ) ) {
    $nextthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $next->ID ), 'post-thumbnail' );
    $css .= '
    .post-navigation .nav-next { background-image: url(' . esc_url( $nextthumb[0] ) . '); border-top: 0; }
    .post-navigation .nav-next .post-title, .post-navigation .nav-next a:hover .post-title, .post-navigation .nav-next .meta-nav { color: #fff; }
    .post-navigation .nav-next a:before { background-color: rgba(0, 0, 0, 0.4); }
    ';
  }

  wp_add_inline_style( 'corocotta-style', $css );
}
add_action( 'wp_enqueue_scripts', 'corocotta_post_nav_background' );

/**
 * Display descriptions in main navigation.
 *
 * @param string  $item_output The menu item output.
 * @param WP_Post $item        Menu item object.
 * @param int     $depth       Depth of the menu.
 * @param array   $args        wp_nav_menu() arguments.
 * @return string Menu item with possible description.
 */
function corocotta_nav_description( $item_output, $item, $depth, $args ) {
  if ( 'primary' == $args->theme_location && $item->description ) {
    $item_output = str_replace( $args->link_after . '</a>', '<div class="menu-item-description">' . $item->description . '</div>' . $args->link_after . '</a>', $item_output );
  }

  return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'corocotta_nav_description', 10, 4 );

/**
 * Add a `screen-reader-text` class to the search form's submit button.
 *
 * @param string $html Search form HTML.
 * @return string Modified search form HTML.
 */
function corocotta_search_form_modify( $html ) {
  return str_replace( 'class="search-submit"', 'class="search-submit screen-reader-text"', $html );
}
add_filter( 'get_search_form', 'corocotta_search_form_modify' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';
