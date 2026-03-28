<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class BlocksForDiscogsSellers_init {

	public $class_admin;
	public $class_api;
	public $class_cache;
	public $class_logging;
	public $plugin_data = [
		'key'=>'gf_blocks_for_discogs',
		'slug'=>'blocks-for-discogs-sellers',
		'title'=>'Blocks for Discogs Sellers',
		'classes'=>'BlocksForDiscogsSellers'
	];

	///////////////////////////////////////////////////////////////////////////
	function __construct()
	{
		add_action('init',					[$this,'required_notice'] );
		add_action('init',					[$this,'load_blocks'] );
		add_action('wp_enqueue_scripts',	[$this,'stylesheets'] );
		add_action('admin_enqueue_scripts',	[$this,'stylesheets'] );
		add_action('enqueue_block_assets',	[$this,'stylesheets'] );		
		add_filter('block_categories_all',	[$this,'custom_block_category'] );
		//add_filter('wp_enqueue_scripts',	[$this,'frontend_scripts'] );
		add_filter('plugin_action_links_'.GF_BLOCKS_FOR_DISCOGS_SELLERS_PLUGIN_BASENAME, [$this,'plugin_links'] );

		$this->load_classes();
		$this->class_logging = new BlocksForDiscogsSellers_Logging($this);
		$this->class_api 	 = new BlocksForDiscogsSellers_API($this);
		$this->class_cache 	 = new BlocksForDiscogsSellers_Cache($this);
		$this->class_admin 	 = new BlocksForDiscogsSellers_Admin($this);

	}
	///////////////////////////////////////////////////////////////////////////
	public function load_blocks()
	{
		$blocks = glob(dirname(__DIR__).'/blocks/*');

		foreach ($blocks as $block) :
			register_block_type_from_metadata( $block );
		endforeach;
	}
	///////////////////////////////////////////////////////////////////////////
	public function load_classes()
	{ 
		include(__DIR__.'/logging.php');
		include(__DIR__.'/admin.php');
		include(__DIR__.'/api.php');
		include(__DIR__.'/cache.php');
		include(__DIR__.'/functions.php');
	}
	///////////////////////////////////////////////////////////////////////////
	public function custom_block_category( $categories ) {
		$categories[] = array(
			'slug'  => 'blocks-for-discogs',
			'title' => 'Discogs',
			'icon'  => 'album',
		);
		return $categories;
	}
	///////////////////////////////////////////////////////////////////////////
	public function plugin_links($links)
	{ 
		$settings_link = '<a aria-label="View Settings" href="options-general.php?page='.$this->plugin_data['key'].'">Settings</a>'; 
		array_unshift($links, $settings_link);
		return $links; 
	}
	///////////////////////////////////////////////////////////////////////////
	public function stylesheets()
	{

		//if (is_plugin_active('blocks-for-discogs-users/blocks-for-discogs-users.php')) {
		//	echo 'Blocks for Discogs Users active!';
		//}

		wp_enqueue_style(
			GF_BLOCKS_FOR_DISCOGS_SELLERS_PLUGIN_SLUG.'-stylesheet', 
			GF_BLOCKS_FOR_DISCOGS_SELLERS_PLUGIN_URL.'/assets/css/css.css',
			'',
			GF_BLOCKS_FOR_DISCOGS_SELLERS_PLUGIN_VERSION,
			'all'
		);

	}
	///////////////////////////////////////////////////////////////////////////
	public function required_notice()
	{
		if (!function_exists('register_block_type_from_metadata')) {
			if (!function_exists('gf_blocks_for_discogs__gutenberg_activation_check_notice')) {
				function gf_blocks_for_discogs__gutenberg_activation_check_notice() {
					echo '<div class="notice notice-error"><p>Notice: Blocks for Discogs require Gutenberg to be active. Plugin has been deactivated.</p></div>';
				}
			}
			deactivate_plugins(GF_BLOCKS_FOR_DISCOGS_SELLERS_PLUGIN_BASENAME);
			add_action( 'admin_notices', 'gf_blocks_for_discogs__gutenberg_activation_check_notice' );
			return;
		}
	}
	///////////////////////////////////////////////////////////////////////////

}