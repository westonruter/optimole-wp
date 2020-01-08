<?php

/**
 * Class Optml_print_support.
 *
 * @reason Print preview displays lazyload placeholders
 */
class Optml_print_support extends Optml_compatibility {
	/**
	 * Should we load the integration logic.
	 *
	 * @return bool Should we load.
	 */
	function should_load() {
		return true;
	}

	/**
	 * Register integration details.
	 */
	public function register() {
		add_action(
			'wp_enqueue_scripts',
			function () {
				wp_register_script( 'optml-typekit', false );
				wp_enqueue_script( 'optml-typekit' );
				$script = '
			(function(w, d){
			if (/^((?!chrome|android).)*safari/i.test(navigator.userAgent)) {			    
				var mediaQueryList = window.matchMedia("print");
						mediaQueryList.addListener(function(mql) {
							if(mql.matches) {
								let images = d.getElementsByTagName( "img" );
										for ( let i = 0; i < images.length; i++ ) {
												if ( "optSrc" in images[i].dataset ) {
													images[i].src = images[i].dataset.optSrc ;
												}
											}
							}
						});
			}
			else {
					w.addEventListener("beforeprint", function(){
						let images = d.getElementsByTagName( "img" );
							for ( let i = 0; i < images.length; i++ ) {
								 if ( "optSrc" in images[i].dataset ) {
								    images[i].src = images[i].dataset.optSrc ;
								 }
							}
					});
				}
			}(window, document));
		';
				wp_add_inline_script( 'optml-typekit', $script );
			}
		);
	}
}
