<?php
namespace evias;

defined( 'ABSPATH' ) || exit;

final class CROWDFUNDING_REGISTRATION {

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	function __construct() {
		$this->include_shortcode();
	}

	// Include Shortcode
	public function include_shortcode() {
		if( class_exists( 'WooCommerce' ) ){
			include_once CFREG_DIR_PATH.'shortcode/RegistrationCustom.php';
			$wpcf_registration = new \WPCF\shortcode\RegistrationCustom();
		}
	}
}
