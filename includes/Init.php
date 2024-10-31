<?php 
namespace Muzaara;

class Init {
	public $config;
	public $api, $ajax, $gapi;

	public function __construct()
	{
		$defaults = array(
			"accounts" => array()
		);

		$this->config = get_option( "muzaara", $defaults );

		if (defined( "MUZAARA_FUNC_PATH" ) ) { 
			$this->inc();
			$this->api = new WP_API( $this );
			$this->ajax = new Ajax( $this );
			$this->gapi = new GApi( $this );
		}

		$this->_actions();
	}

	private function _actions()
	{
		add_action( "admin_init", array($this, "check_dep" ) );
		add_action( "admin_menu", array( $this, "add_menu" ) );
		add_action( "admin_enqueue_scripts", array( $this, "enqueue" ) );
		add_action( "wp_dashboard_setup", array( $this, "add_dashboard_widget" ) );
		
		add_action( "rest_api_init", array( $this->api, "register_routes" ) );

		add_action( "wp_ajax_muzaara_link_status", array( $this->ajax, "check_status" ) );
		add_action( "wp_ajax_muzaara_choose_account", array( $this->ajax, "choose_account" ) );
		add_action( "wp_ajax_muzaara_get_metrics", array( $this->ajax, "get_metrics" ) );
		add_action( "wp_ajax_muzaara_get_recommendations", array( $this->ajax, "get_recommendations" ) );
		add_action( "wp_ajax_muzaara_save_setting", array($this->ajax, "save_settings" ) );
		add_action( "wp_ajax_muzaara_apply", array($this->ajax, "apply_recommendations" ) );
		add_action( "wp_ajax_muzaara_unlink", array($this->ajax, "unlink" ) );
	}

	private function inc()
	{
		
			require_once MUZAARA_FUNC_PATH . "access.php";
			require_once MUZAARA_FUNC_PATH . "plugins.php";
			require_once MUZAARA_FUNC_PATH . "GoogleAds.php";
		

			// require_once( MUZAARA[ "abspath" ] . "includes/lib/google-ads/vendor/autoload.php" );
			require_once( MUZAARA[ "abspath" ] . "includes/WPAPI.php" );
			require_once( MUZAARA[ "abspath" ] . "includes/Ajax.php" );
			require_once( MUZAARA[ "abspath" ] . "includes/extras/Recommendations.php" );
			require_once( MUZAARA[ "abspath" ] . "includes/extras/Debugging.php" );
			require_once( MUZAARA[ "abspath" ] . "includes/Auth.php" );
		
	}

	public static function activation() {
        if ( defined( "MUZAARA_FUNC_PATH" ) ) {
            require_once MUZAARA_FUNC_PATH . "plugins.php";

            \Muzaara\Engine\Functions\Plugins\addActive(MUZAARA_GADS_BASE);
        }
    }

	public static function deactivation()
	{
		if ( defined( "MUZAARA_FUNC_PATH" ) ) {
            require_once MUZAARA_FUNC_PATH . "plugins.php";

            \Muzaara\Engine\Functions\Plugins\removeActive(MUZAARA_GADS_BASE);
            delete_option( "muzaara_gaccess" );
			delete_option( "google_account_data" );
			delete_option( "muzaara_customer" );

			\Muzaara\Engine\Functions\Access\unlink(MUZAARA_GOOGLE_SCOPES["adwords"]);
		}	
	}

	public function check_dep() {
        if ( is_admin() && current_user_can("activate_plugins") && !defined( "MUZAARA_PATH" ) ) {
            add_action( "admin_notices", function() {
                ?><div class="error"><p><?php printf( __( "%s plugin requires parent plugin (Muzaara Engine) to be installed and active", "muzaara-gads" ), MUZAARA_GADS_BASE )?></p></div><?php 
            });

            deactivate_plugins( MUZAARA_GADS_BASE );
            
            if ( isset( $_GET[ "activate" ] ) ) 
                unset( $_GET[ "activate" ] );
        }
    }

	public function enqueue()
	{
		if ( !defined( "MUZAARA_FUNC_PATH" ) ) {
            return;
        }
		require_once MUZAARA_FUNC_PATH . "access.php";
		
		wp_register_style( "muzaara-gfont", "https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700&display=swap" );
		wp_register_style( "muzaara-datepicker", sprintf( "%scss/daterangepicker.css", MUZAARA[ "assets_url" ] ) );
		wp_enqueue_style( "muzaara", sprintf( "%scss/admin.css", MUZAARA[ "assets_url" ] ), array( "muzaara-gfont", "muzaara-datepicker" ) );

		wp_register_script( "gcharts", "https://www.gstatic.com/charts/loader.js" );
		wp_register_script( "muzaara-momentjs", sprintf( "%sjs/moment.min.js", MUZAARA[ "assets_url" ] ) );
		wp_register_script( "muzaara-datepicker", sprintf( "%sjs/daterangepicker.js", MUZAARA[ "assets_url" ] ), array( "jquery") );
		wp_enqueue_script( "muzaara", sprintf( "%sjs/muzaara.js", MUZAARA[ "assets_url" ] ), array( "gcharts", "muzaara-momentjs", "muzaara-datepicker" ), false, true );
		wp_localize_script( "muzaara", "MUZAARA", array( 
			"ajax" => admin_url( "admin-ajax.php" ),
			"currency" => $this->get_customer_currency(),
			"auth_url" => \Muzaara\Engine\Functions\Access\generateOAuthURL(array( MUZAARA_GOOGLE_SCOPES[ "adwords" ] ) )
		) );
	}

	public function add_menu()
	{
		$icon = sprintf( "%sassets/images/icon.svg", MUZAARA[ "abspath" ] );
		$encoded_icon = base64_encode( file_get_contents( $icon ) );
		add_menu_page( __( "Muzaara", "muzaara-gads" ), __( "Muzaara", "muzaara-gads" ), "manage_options", "muzaara", array( $this, "display_template" ), sprintf( "data:image/svg+xml;base64,%s", $encoded_icon ) );

		add_submenu_page( "muzaara", __( "Settings", "muzaara-gads" ), __( "Settings", "muzaara-gads" ), "manage_options", "muzaara-settings", array( $this, "display_settings" ) );
	}

	public function add_dashboard_widget()
	{
		if( !empty( $this->config[ "show_in_dashboard" ] ) )
			wp_add_dashboard_widget( "muzaara", __( "Google Ads (Muzaara)", "muzaara-gads" ), array( $this, "dashboard_widget_template" ) );
	}

	public function dashboard_widget_template()
	{
		require_once( MUZAARA[ "template_path" ] . "dashboard-widget.php" );
	}

	public function display_template()
	{
		if( !$this->get_access_token() )
			require_once( MUZAARA[ "template_path" ] . "admin-main-noaccount.php" );
		elseif( !$this->get_customer_id() ) {
			
			printf( "<script>window.location='%s'</script>", admin_url( "admin.php?page=muzaara-settings" ) );
		}
		else {
			require_once( MUZAARA["template_path" ] . "overview.php" );
		}
	}

	public function display_settings()
	{
		if( !$this->get_access_token() )
			require_once( MUZAARA[ "template_path" ] . "admin-main-noaccount.php" );
		else {
			$customers = $this->gapi->getCustomers();
			$current_customer_id = $this->get_customer_id();
			$current_customer = $this->get_customer();
			$account_data = $this->get_account();
			
			require_once( MUZAARA[ "template_path" ] . "admin-main.php" );
		}
	}

	public function set_setting( $setting, $value )
	{
		$this->config[$setting]=$value;
		update_option( "muzaara", $this->config );
	}

	public function get_access_token()
	{
		return \Muzaara\Engine\Functions\Access\hasAccess( [ MUZAARA_GOOGLE_SCOPES[ "adwords" ] ] ) ? \Muzaara\Engine\Functions\Access\getAccess() : false;
	}

	public function get_account()
	{
		return get_option( "google_account_data", array( "email" => null ));
	}

	public function get_customer()
	{
		$account = get_option( "muzaara_customer", array( "id" => null, "timezone" => null, "name" => null ) );
		return $account;
	}

	public function get_customer_id()
	{
		$account = get_option( "muzaara_customer", array( "id" => null, "timezone" => null ) );
		return $account[ "id" ];
	}

	public function get_customer_timezone()
	{
		$account = get_option( "muzaara_customer", array( "id" => null, "timezone" => null ) );
		return $account[ "timezone" ];
	}

	public function get_customer_currency()
	{
		$account = get_option( "muzaara_customer", array( "id" => null, "timezone" => null, "currency" => "" ) );
		return $account[ "currency" ];
	}

	public function get_key() {
		return \Muzaara\Engine\Functions\Access\getSiteHash();

		// $host = parse_url( site_url(), PHP_URL_HOST );
		// $name = bin2hex( strchr( $host, ".", 1 ) );

		// $key = sha1( sprintf( "%s:%s", $host, $name ) );
		// return $key;
	}

	public function unlink()
	{
		delete_option( "muzaara_gaccess" );
		delete_option( "google_account_data" );
		delete_option( "muzaara_customer" );

		\Muzaara\Engine\Functions\Access\unlink(MUZAARA_GOOGLE_SCOPES["adwords"]);
	}
}