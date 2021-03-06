<?php
/**
 * WordPress unit test plugin.
 *
 * @package     Optimole-WP
 * @subpackage  Tests
 * @copyright   Copyright (c) 2017, ThemeIsle
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Class Test_Generic.
 */
class Test_Replacer extends WP_UnitTestCase {
	const IMG_TAGS = '<div id="wp-custom-header" class="wp-custom-header"><img src="http://example.org/wp-content/themes/twentyseventeen/assets/images/header.jpg" width="2000" height="1200" alt="Test" /></div></div> ';
	const IMG_TAGS_WITH_SRCSET = '<img class="alignnone size-full wp-image-26" src="http://example.org/wp-content/uploads/2019/01/september-2018-wordpress-news-w-codeinwp.jpg" alt="" width="1450" height="740" srcset="http://example.org/wp-content/uploads/2019/01/september-2018-wordpress-news-w-codeinwp.jpg 1450w, http://example.org/wp-content/uploads/2019/01/september-2018-wordpress-news-w-codeinwp-300x153.jpg 300w, http://example.org/wp-content/uploads/2019/01/september-2018-wordpress-news-w-codeinwp-768x392.jpg 768w, http://example.org/wp-content/uploads/2019/01/september-2018-wordpress-news-w-codeinwp-1024x523.jpg 1024w" sizes="(max-width: 1450px) 100vw, 1450px"> ';
	const IMG_TAGS_WITH_SRCSET_SCHEMALESS = '<img class="alignnone size-full wp-image-26" src="//example.org/wp-content/uploads/2019/01/september-2018-wordpress-news-w-codeinwp.jpg" alt="" width="1450" height="740" srcset="//example.org/wp-content/uploads/2019/01/september-2018-wordpress-news-w-codeinwp.jpg 1450w, //example.org/wp-content/uploads/2019/01/september-2018-wordpress-news-w-codeinwp-300x153.jpg 300w, //example.org/wp-content/uploads/2019/01/september-2018-wordpress-news-w-codeinwp-768x392.jpg 768w, //example.org/wp-content/uploads/2019/01/september-2018-wordpress-news-w-codeinwp-1024x523.jpg 1024w" sizes="(max-width: 1450px) 100vw, 1450px"> ';
	const IMG_TAGS_WITH_SRCSET_RELATIVE = '<img class="alignnone size-full wp-image-26" src="/wp-content/uploads/2019/01/september-2018-wordpress-news-w-codeinwp.jpg" alt="" width="1450" height="740" srcset="/wp-content/uploads/2019/01/september-2018-wordpress-news-w-codeinwp.jpg 1450w, /wp-content/uploads/2019/01/september-2018-wordpress-news-w-codeinwp-300x153.jpg 300w, /wp-content/uploads/2019/01/september-2018-wordpress-news-w-codeinwp-768x392.jpg 768w, /wp-content/uploads/2019/01/september-2018-wordpress-news-w-codeinwp-1024x523.jpg 1024w" sizes="(max-width: 1450px) 100vw, 1450px"> ';
	const IMG_TAGS_PNG = '<div id="wp-custom-header" class="wp-custom-header"><img src="http://example.org/wp-content/themes/twentyseventeen/assets/images/header.png" width="2000" height="1200" alt="Test" /></div></div>';
	const IMG_TAGS_GIF = '<div id="wp-custom-header" class="wp-custom-header"><img src="http://example.org/wp-content/themes/twentyseventeen/assets/images/header.gif" width="2000" height="1200" alt="Test" /></div></div>';
	const DECODED_UNICODE2 = "/wp-content/uploads/2018/05//umlau1ts_image_a\u0308o\u0308u\u0308.";
	const DECODED_UNICODE = "/wp-content/uploads/2018/05/umlau1ts_image_äöü";
	const NOROMAL_URL = "/wp-content/themes/test/assets/images/header";
	const IMG_URLS = '
	http://example.org/wp-content/themes/test/assets/images/header.png 
	http://example.org/wp-content/themes/test/assets/images/header.jpeg
	http://example.org/wp-content/plugins/optimole-wp/assets/img/logo1.png 
	http://example.org/wp-content/plugins/optimole-wp/assets/img/logo2.png?width=500&cr=small
	http://example.org/wp-content/plugins/optimole-wp/assets/img/logo3.png%3Fwidth%3D500%26cr%3Dsmall
	http://example.org/wp-content/uploads/2018/05/umlauts_image_äöü.jpg
	http://example.org/uploads/2018/05/umlauts_image_a\u0308o\u0308u\u0308.jpg
	//example.org/wp-content/themes/test/assets/images/header2.png 
	//example.org/wp-content/themes/test/assets/images/header2.jpeg
	//example.org/wp-content/plugins/optimole-wp/assets/img/logo4.png 
	//example.org/wp-content/plugins/optimole-wp/assets/img/logo2.png?width=500&cr=small
	//example.org/wp-content/plugins/optimole-wp/assets/img/logo3.png%3Fwidth%3D500%26cr%3Dsmall
	//example.org/wp-content/uploads/2018/05/umlauts_im4age_äöü.jpg
	//example.org/uploads/2018/05/umlauts_5image_a\u0308o\u0308u\u0308.jpg
	/wp-content/themes/test/assets/images/header4.png 
	/wp-content/themes/test/assets/images/header7.jpeg
	/wp-content/plugins/optimole-wp/assets/img/logo9.png 
	/wp-content/plugins/optimole-wp/assets/img/lo2go.png?width=500&cr=small
	/wp-content/plugins/optimole-wp/assets/img/log4.png%3Fwidth%3D500%26cr%3Dsmall
	/wp-content/uploads/2018/05/umlau1ts_image_äöü.jpg
	/wp-content/uploads/2018/05/umlau1ts_image_a\u0308o\u0308u\u0308.jpg
	 ';
	const CSS_STYLE = '
	<style>
	.body{
		background-image:url("http://example.org/wp-content/themes/test/assets/images/header-300x300.png");
	}
	.body div {
		background-image:url("//example.org/wp-content/themes/test/assets/images/header3-300x300.png");
	}
	.body div {
		background-image:url("/wp-content/themes/test/assets/images/header2-300x300.png");
	}
	.body div {
		background-image:url(/wp-content/themes/test/assets/images/head1er2-300x300.png);
	}
	.body{
		background-image:url(http://example.org/wp-content/themes/test/assets/images/heade2r-300x300.png);
	}
	.body div {
		background-image:url(//example.org/wp-content/themes/test/assets/images/he3ader3-300x300.png);
	}
	</style>
	 ';
	const WRONG_EXTENSION = '   http://example.org/wp-content/themes/twentyseventeen/assets/images/header.gif   ';
	const IMAGE_SIZE_DATA = '
		http://example.org/wp-content/uploads/optimole-wp/assets/img/logo-282x123.png
		http://example.org/wp-content/plugins/optimole-wp/assets/img/test-282x123.png
		//example.org/wp-content/uploads/optimole-wp/assets/img/log2o-282x123.png
		//example.org/wp-content/plugins/optimole-wp/assets/img/tes3t-282x123.png
	';
	const IMAGE_SIZE_NO_CLASS = '<div id="wp-custom-header" class="wp-custom-header"><img src="http://example.org/wp-content/themes/twentyseventeen/assets/images/header-100x100.png" alt="Test" /></div></div>';

	const TEST_STAGING = '<div class="before-footer">
				<div class="codeinwp-container">
					<p class="featuredon">Featured On</p>
					<img src="https://www.example.org/wp-content/uploads/2018/05/brands.png">
				</div>
			</div>';
	const TEST_WRONG_URLS = '<div class="before-footer">
				<div class="codeinwp-container">
					<p class="featuredon">Featured On</p>
					<img src="https://www.codeinwp.org/wp-content/uploads/2018/05/brands.png">https://www.codeinwp.org/wp-content/uploads/2018/05/brands.png
				</div>
			</div>';

	public static $sample_post;
	public static $sample_attachement;

	public function setUp() {


		parent::setUp();
		$settings = new Optml_Settings();
		$settings->update( 'service_data', [
			'cdn_key'    => 'test123',
			'cdn_secret' => '12345',
			'whitelist'  => [ 'example.com', 'example.org' ],

		] );
		$settings->update( 'lazyload', 'disabled' );

		Optml_Url_Replacer::instance()->init();
		Optml_Tag_Replacer::instance()->init();
		Optml_Manager::instance()->init();

		self::$sample_post        = self::factory()->post->create( [
				'post_title'   => 'Test post',
				'post_content' => self::IMG_TAGS
			]
		);
		self::$sample_attachement = self::factory()->attachment->create_upload_object( OPTML_PATH . 'assets/img/logo.png' );

	}

	public function test_wc_json_replacement() {
		$html = [
			'image'  => "https://www.example.org/wp-content/uploads/2018/05/brands.png",
			'image2' => "https://www.example.org/wp-content/uploads/2018/05/brands2.png?test=123",
			'image3' => "https://www.example.org/wp-content/uploads/2018/05/brands2.png?test=123&amp;new=val",
		];

		$html             = wp_json_encode( $html );
		$html             = _wp_specialchars( $html, ENT_QUOTES, 'UTF-8', true );
		$replaced_content = Optml_Manager::instance()->process_urls_from_content( $html );
		$this->assertEquals( 3, substr_count( $replaced_content, 'i.optimole.com' ) );

		$replaced_content = wp_specialchars_decode( $replaced_content, ENT_QUOTES );
		$replaced_content = json_decode( $replaced_content, true );

		$this->assertArrayHasKey( 'image', $replaced_content );

	}

	public function test_image_tags() {

		$found_images = Optml_Manager::parse_images_from_html( self::IMG_TAGS );

		$this->assertCount( 5, $found_images );
		$this->assertCount( 1, $found_images['img_url'] );

		$replaced_content = Optml_Manager::instance()->process_images_from_content( self::IMG_TAGS );

		$this->assertContains( 'i.optimole.com', $replaced_content );
		$this->assertContains( '/w:2000/', $replaced_content );
		$this->assertContains( '/h:1200/', $replaced_content );
		$this->assertContains( 'http://example.org', $replaced_content );

		$replaced_content = Optml_Manager::instance()->process_images_from_content( self::TEST_STAGING );
		$this->assertContains( 'i.optimole.com', $replaced_content );
		$this->assertContains( '/https://www.example.org', $replaced_content );

	}

	public function test_optimization_url() {
		$replaced_content = Optml_Manager::instance()->process_images_from_content( self::IMG_TAGS );

		$this->assertContains( 'i.optimole.com', $replaced_content );
		$this->assertContains( 'http://example.org', $replaced_content );

		$replaced_content = Optml_Manager::instance()->replace_content( self::IMG_URLS );

		$this->assertEquals( 21, substr_count( $replaced_content, 'i.optimole.com' ) );
	}

	public function test_style_replacement() {
		$replaced_content = Optml_Manager::instance()->replace_content( self::CSS_STYLE );

		$this->assertContains( 'i.optimole.com', $replaced_content );
		$this->assertContains( 'http://example.org', $replaced_content );
		$this->assertEquals( 6, substr_count( $replaced_content, 'i.optimole.com' ) );

	}

	public function test_replacement_non_whitelisted_urls() {
		$replaced_content = Optml_Manager::instance()->replace_content( self::TEST_WRONG_URLS );

		$this->assertNotContains( 'i.optimole.com', $replaced_content );
		$this->assertContains( 'https://www.codeinwp.org', $replaced_content );
	}

	public function test_replacement_remove_query_arg() {
		$content          = '<div class="before-footer">
				<div class="codeinwp-container">
					<p class="featuredon">Featured On</p>
					<img src="https://www.example.org/wp-content/uploads/2018/05/brands.png?param=123&2782=dasda"> 
				</div>
			</div>';
		$replaced_content = Optml_Manager::instance()->replace_content( $content );

		$this->assertContains( 'i.optimole.com', $replaced_content );
		$this->assertContains( '?param=123', $replaced_content );
	}

	public function test_replacement_with_relative_url() {
		$content = '<div class="before-footer">
				<div class="codeinwp-container">
					<p class="featuredon">Featured On</p>
					<img src="/wp-content/uploads/2018/05/brands.png"> 
				</div>
			</div>';

		$replaced_content = Optml_Manager::instance()->replace_content( $content );

		$this->assertContains( 'i.optimole.com', $replaced_content );

	}
	public function test_replacement_without_quotes() {
		$content = '<div  > 
					<p custom-attr=http://example.org/wp-content/uploads/2018/05/brands.png>  
			</div>';

		$replaced_content = Optml_Manager::instance()->replace_content( $content );

		$this->assertContains( 'i.optimole.com', $replaced_content );

	}

	public function test_replacement_strange_chars() {
		$content          = '
		https://www.example.org/wp-content/uploads/2018/05/@brands.png
		https://www.example.org/wp-content/uploads/2018/05/%brands.png
		';
		$replaced_content = Optml_Manager::instance()->replace_content( $content );
		$this->assertEquals( 2, substr_count( $replaced_content, 'i.optimole.com' ) );

	}

	// TODO We need to extend this to single url replacement. If we make the url extractor regex with option scheme, the parsing will take huge amount of time. We need to think alternatives.

	public function test_replacement_without_scheme() {
		$content          = '<div class="before-footer">
				<div class="codeinwp-container">
					<p class="featuredon">Featured On</p>
					<img src="//www.example.org/wp-content/uploads/2018/05/brands.png"> 
				</div>
			</div>';
		$replaced_content = Optml_Manager::instance()->replace_content( $content );
		$this->assertContains( 'i.optimole.com', $replaced_content );
		$this->assertContains( 'http://www.example.org', $replaced_content );
	}

	public function test_non_allowed_extensions() {
		$replaced_content = Optml_Manager::instance()->replace_content( ( self::CSS_STYLE . self::IMG_TAGS . self::WRONG_EXTENSION ) );
		$this->assertContains( 'i.optimole.com', $replaced_content );
		//Test if wrong extension is still present in the output.
		$this->assertContains( 'http://example.org/wp-content/themes/twentyseventeen/assets/images/header.gif', $replaced_content );
	}

	public function test_elementor_data() {
		$html             = self::get_html_array();
		$replaced_content = Optml_Manager::instance()->replace_content( json_encode( $html ) );

		$this->assertContains( 'i.optimole.com', $replaced_content );
		$this->assertEquals( 54, substr_count( $replaced_content, 'i.optimole.com' ) );

		//Ensure the json is not corrupted after replacement.
		$this->assertTrue( is_array( json_decode( $replaced_content, true ) ) );

		//The content should be sucessfully processed.
		$this->assertNotContains( "\"https:\/\/www.example.org\/wp-content", $replaced_content );
		$this->assertNotContains( "\"\/\/www.example.org\/wp-content", $replaced_content );
		$this->assertNotContains( "\"\/wp-content", $replaced_content );
		$count_unicode = 0;
		$replaced_html = json_decode( $replaced_content, true );
		foreach ( $replaced_html as $value ) {
			if ( strpos( $value, self::DECODED_UNICODE ) !== false ) {
				$count_unicode ++;
			}
		}
		$this->assertEquals( $count_unicode, 27 );

	}

	public static function get_html_array() {

		$html = [];

		$html['relative_normal']  = self::NOROMAL_URL . 'a.jpg';
		$html['relative_unicode'] = self::DECODED_UNICODE . 'a.jpg';

		$html['schemaless_normal'] = "//example.org" . self::NOROMAL_URL . 'b.jpg';
		$html['unicode_normal']    = "//example.org" . self::DECODED_UNICODE . 'b.jpg';

		$html['full_normal']  = "http://example.org" . self::NOROMAL_URL . 'c.jpg';
		$html['full_unicode'] = "http://example.org" . self::DECODED_UNICODE . 'c.jpg';
		$i                    = 0;
		foreach ( $html as $key => $value ) {
			$i ++;
			$value                                      = str_replace( [ "a.jpg", "b.jpg", "c.jpg" ], [
				"a" . $i . ".jpg",
				"b" . $i . ".jpg",
				"c" . $i . ".jpg",
			], $value );
			$html[ $key . '_img_simple' ]               = '<img src="' . $value . '" > ';
			$html[ $key . '_img_with_alt' ]             = '<img alt="" src="' . $value . '" > ';
			$html[ $key . '_img_with_alt_near_src' ]    = '<img alt=""src="' . $value . '" > ';
			$html[ $key . '_img_with_ending' ]          = '<img src="' . $value . '" /> ';
			$html[ $key . '_img_with_ending_no_space' ] = '<img src="' . $value . '"/> ';
			$html[ $key . '_img_with_class' ]           = '<img class="one-class" src="' . $value . '" /> ';
			$html[ $key . '_img_anchor' ]               = '<a href="http://example.org/blog/how-to-monetize-a-blog/">                      <img class="one-class" src="' . $value . '" /> </a> ';
			$html[ $key . '_img_more_html' ]            = '<div class="before-footer">
				<div class="codeinwp-container"> 
				<img class="one-class" src="' . $value . '" /> 
				</div>
				</div> ';
		};

		return $html;
	}

	public function test_max_size_height() {
		$new_url = Optml_Manager::instance()->replace_content( ' http://example.org/wp-content/themes/test/assets/images/header.png ', [
			'width'  => 99999,
			'height' => 99999
		] );
		$this->assertContains( 'i.optimole.com', $new_url );
		$this->assertNotContains( '99999', $new_url );

	}

	public function test_cropping_sizes() {

		$attachement_url = wp_get_attachment_image_src( self::$sample_attachement, 'sample_size_crop' );

		$this->assertContains( 'w:100', $attachement_url[0] );
		$this->assertContains( 'h:100', $attachement_url[0] );
		$this->assertContains( 'rt:fill', $attachement_url[0] );
		global $_test_posssible_values_y_sizes;
		global $_test_posssible_values_x_sizes;
		$allowed_gravities = array(
			'left'         => Optml_Resize::GRAVITY_WEST,
			'right'        => Optml_Resize::GRAVITY_EAST,
			'top'          => Optml_Resize::GRAVITY_NORTH,
			'bottom'       => Optml_Resize::GRAVITY_SOUTH,
			'lefttop'      => Optml_Resize::GRAVITY_NORTH_WEST,
			'leftbottom'   => Optml_Resize::GRAVITY_SOUTH_WEST,
			'righttop'     => Optml_Resize::GRAVITY_NORTH_EAST,
			'rightbottom'  => Optml_Resize::GRAVITY_SOUTH_EAST,
			'centertop'    => array( 0.5, 0 ),
			'centerbottom' => array( 0.5, 1 ),
			'leftcenter'   => array( 0, 0.5 ),
			'rightcenter'  => array( 1, 0.5 ),
		);

		foreach ( $_test_posssible_values_x_sizes as $x_value ) {
			foreach ( $_test_posssible_values_y_sizes as $y_value ) {
				if ( $x_value === true && $y_value === true ) {
					continue;
				}
				$x_value = $x_value === true ? '' : $x_value;
				$y_value = $y_value === true ? '' : $y_value;

				if ( ! isset( $allowed_gravities[ $x_value . $y_value ] ) ) {
					$gravity_key = Optml_Resize::GRAVITY_CENTER;
				} else {
					$gravity_key = $allowed_gravities[ $x_value . $y_value ];
				}

				$attachement_url = wp_get_attachment_image_src( self::$sample_attachement, 'sample_size_h_' . $x_value . $y_value );
				$this->assertContains( 'rt:fill', $attachement_url[0] );
				if ( ! is_array( $gravity_key ) ) {
					$this->assertContains( 'g:' . $gravity_key, $attachement_url[0], sprintf( ' %s for X %s for Y should contain %s gravity', $x_value, $y_value, $gravity_key ) );
				} else {
					$this->assertContains( 'g:fp:' . $gravity_key[0] . ':' . $gravity_key[1], $attachement_url[0] );
				}
			}
		}

	}

	public function test_post_content() {
		$content = apply_filters( 'the_content', get_post_field( 'post_content', self::$sample_post ) );

		$this->assertContains( 'i.optimole.com', $content );
	}

	public function test_strip_image_size() {
		$replaced_content = Optml_Manager::instance()->replace_content( self::IMAGE_SIZE_DATA );

		//Test fake sample image size.
		$this->assertContains( 'i.optimole.com', $replaced_content );
		$this->assertContains( '282x123', $replaced_content );
		$this->assertEquals( 4, substr_count( $replaced_content, 'i.optimole.com' ) );

		//Test valid wordpress image size, it should strip the size suffix.
		$attachement_url  = wp_get_attachment_image_src( self::$sample_attachement, 'medium' );
		$replaced_content = Optml_Manager::instance()->replace_content( $attachement_url[0] );

		$this->assertNotContains( '282x123', $replaced_content );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function test_custom_domain() {
		define( 'OPTML_SITE_MIRROR', 'https://mycnd.com' );
		Optml_Url_Replacer::instance()->init();
		Optml_Tag_Replacer::instance()->init();
		Optml_Manager::instance()->init();

		$replaced_content = Optml_Manager::instance()->replace_content( self::IMG_TAGS );

		//Test custom source.
		$this->assertContains( 'i.optimole.com', $replaced_content );
		$this->assertNotContains( 'http://example.org', $replaced_content );
		$this->assertNotContains( 'example.org', $replaced_content );
		$this->assertContains( 'mycnd.com', $replaced_content );

	}

	public function test_replace_on_feeds() {
		$this->go_to( '/?feed=rss2' );

		$replaced_content = Optml_Manager::instance()->replace_content( Test_Replacer::IMG_TAGS_WITH_SRCSET );
		$this->assertContains( 'i.optimole.com', $replaced_content );
		$this->assertEquals( 5, substr_count( $replaced_content, 'i.optimole.com' ) );

		$replaced_content = Optml_Manager::instance()->replace_content( Test_Replacer::IMG_TAGS_WITH_SRCSET_SCHEMALESS );
		$this->assertContains( 'i.optimole.com', $replaced_content );
		$this->assertEquals( 5, substr_count( $replaced_content, 'i.optimole.com' ) );

		$replaced_content = Optml_Manager::instance()->replace_content( Test_Replacer::IMG_TAGS_WITH_SRCSET_RELATIVE );
		$this->assertContains( 'i.optimole.com', $replaced_content );
		$this->assertEquals( 5, substr_count( $replaced_content, 'i.optimole.com' ) );
	}

	public function test_double_replacement() {

		$replaced_content = Optml_Manager::instance()->replace_content( Test_Replacer::IMG_TAGS );

		$doubled_ccontent = Optml_Manager::instance()->replace_content( $replaced_content . Test_Replacer::IMG_TAGS );

		$this->assertContains( 'i.optimole.com', $doubled_ccontent );
		$this->assertEquals( 2, substr_count( $doubled_ccontent, 'i.optimole.com' ) );
	}

	public function test_image_size_2_crop() {
		$replaced_content = Optml_Manager::instance()->replace_content( self::IMAGE_SIZE_NO_CLASS );

		$this->assertContains( 'rt:fill', $replaced_content );
		$this->assertContains( 'i.optimole.com', $replaced_content );
	}

	public function test_replacement_with_image_size() {
		//Nasty hack to fetch old url from
		$attachement = wp_get_attachment_image_src( self::$sample_attachement, 'medium' );

		$old_url = explode( 'http://', $attachement[0] );
		$old_url = 'http://' . $old_url[1];

		//Adds possible image size format.
		$content = str_replace( '.png', '-300x300.png', $old_url );

		$replaced_content = Optml_Manager::instance()->replace_content( " " . $content . " " );

		$this->assertContains( 'i.optimole.com', $replaced_content );
		$this->assertNotContains( '-300x300.png', $replaced_content );
		$this->assertContains( 'w:300', $replaced_content );
		$this->assertContains( 'h:300', $replaced_content );

	}

	public function test_parse_json_data_disabled() {

		$some_html_content = [
			'html' => '<a href="http://example.org/blog/how-to-monetize-a-blog/"><img class="alignnone wp-image-36442 size-full" src="http://example.org/wp-content/uploads/2018/06/start-a-blog-1-5.png" alt="How to monetize a blog" width="490" height="256"></a> http://example.org/wp-content/uploads/2018/06/start-a-blog-1-5.png '
		];

		$content           = wp_json_encode( $some_html_content );
		$replaced_content  = Optml_Manager::instance()->replace_content( $content );
		$replaced_content2 = Optml_Manager::instance()->replace_content( $replaced_content );

		$this->assertEquals( $replaced_content, $replaced_content2 );
		$this->assertArrayHasKey( 'html', json_decode( $replaced_content2, true ) );

		$this->assertEquals( 2, substr_count( $replaced_content2, '/http:\/\/' ) );
	}

	public function test_filter_sizes_attr() {

		global $wp_current_filter;
		$wp_current_filter = array( 'the_content' );

		$sizes    = array(
			'width'  => 1000,
			'height' => 1000
		);
		$response = apply_filters( 'wp_calculate_image_sizes', $sizes, array( 10000 ) );
		$this->assertContains( '(max-width: 1000px) 100vw, 1000px', $response );
		$wp_current_filter = array();
		$response          = apply_filters( 'wp_calculate_image_sizes', $sizes, array( 10000 ) );
		$this->assertTrue( ! empty( $response ) );
		$this->assertTrue( is_array( $response ) );

		global $content_width;
		$content_width = 5000;
		$response      = apply_filters( 'wp_calculate_image_sizes', $sizes, array( 1 ) );
		$this->assertTrue( ! empty( $response ) );
		$this->assertTrue( is_array( $response ) );
	}

	public function test_replacement_hebrew() {
		$content          = '<div class="codeinwp-container">
					<img src="https://www.example.org/wp-content/uploads/2018/05/ס@וככי-תבל-לוגו.jpg"> 
					<img src="https://www.example.org/wp-content/uploads/2018/05/סוtextדךי-תב700ל-לוגו.jpg"> 
					<img src="https://www.example.org/wp-content/uploads/2018/כwhateverכי-ת.png"> 
				</div>
			';
		$replaced_content = Optml_Manager::instance()->replace_content( $content );

		$this->assertContains( 'i.optimole.com', $replaced_content );

		$this->assertEquals( 3, substr_count( $replaced_content, 'i.optimole.com' ) );

	}

	public function test_replacement_chinese() {
		$content          = '<div class="codeinwp-container">
					<img src="https://www.example.org/wp-content/uploads/2020/03/年轮钟2号-Annual-Rings-Clock-II-白背景.jpg"> 
					<img src="https://www.example.org/wp-content/uploads/2020/03/年轮钟2号-白背景.jpg"> 
					<img src="https://www.example.org/wp-content/uploads/2020/03/年轮钟3年轮钟.jpg"> 
				</div>
			';
		$replaced_content = Optml_Manager::instance()->replace_content( $content );

		$this->assertContains( 'i.optimole.com', $replaced_content );

		$this->assertEquals( 3, substr_count( $replaced_content, 'i.optimole.com' ) );

	}

}