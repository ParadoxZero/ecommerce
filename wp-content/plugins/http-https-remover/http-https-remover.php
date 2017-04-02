<?php
/**
 * Plugin Name: HTTP / HTTPS Remover
 * Plugin URI: https://de.wordpress.org/plugins/http-https-remover/
 * Description: I changed the way how „HTTP / HTTPS Remover“ is working. It doesn’t remove http and https from links in source code anymore. Now it converts all links to https!
 * Version: 1.4
 * Author: CONDACORE
 * Author URI: https://condacore.com/
 * License: GPLv3
 */
 
 
#     _____ ____  _   _ _____          _____ ____  _____  ______ 
#    / ____/ __ \| \ | |  __ \   /\   / ____/ __ \|  __ \|  ____|
#   | |   | |  | |  \| | |  | | /  \ | |   | |  | | |__) | |__   
#   | |   | |  | | . ` | |  | |/ /\ \| |   | |  | |  _  /|  __|  
#   | |___| |__| | |\  | |__| / ____ \ |___| |__| | | \ \| |____ 
#    \_____\____/|_| \_|_____/_/    \_\_____\____/|_|  \_\______|
#


if (!defined('ABSPATH')) exit;
class HTTP_HTTPS_REMOVER

{
	
	
	//Add some links on the plugin page

	// ###########################################
	// ##### Apply Plugin on the whole Site ######
	// ###########################################
	public function __construct()

	{
		
		add_action('wp_loaded', array(
			$this,
			'letsGo'
		) , 99, 1);
	}
	// #########################
	// ##### More Code... ######
	// #########################
	public function letsGo()

	{
		global $pagenow;
		ob_start(array(
			$this,
			'mainPath'
		));
	}
	public function mainPath($buffer)

	{
		$content_type = NULL;
		foreach(headers_list() as $header) {
			if (strpos(strtolower($header) , 'content-type:') === 0) {
				$pieces = explode(':', strtolower($header));
				$content_type = trim($pieces[1]);
				break;
			}
		}
		if (is_null($content_type) || substr($content_type, 0, 9) === 'text/html') {
			// ###############################
			// ##### The important Path ######
			// ###############################
			
			
			
			// All Intern Links
			$buffer = str_replace('http://' . $_SERVER['HTTP_HOST'], 'https://' . $_SERVER['HTTP_HOST'], $buffer);
			$buffer = str_replace('"//' . $_SERVER['HTTP_HOST'], '"https://' . $_SERVER['HTTP_HOST'], $buffer);
			$buffer = str_replace('\'//' . $_SERVER['HTTP_HOST'], '\'https://' . $_SERVER['HTTP_HOST'], $buffer);
			
			// Google APIs			
			$buffer = preg_replace('|http://(.*?).googleapis.com|', 'https://$1.googleapis.com', $buffer);
			$buffer = preg_replace('|"//(.*?).googleapis.com|', '"https://$1.googleapis.com', $buffer);
			$buffer = preg_replace("|'//(.*?).googleapis.com|", "'https://$1.googleapis.com", $buffer);
			
			$buffer = preg_replace('|http://(.*?).google.com|', 'https://$1.google.com', $buffer);
			$buffer = preg_replace('|"//(.*?).google.com|', '"https://$1.google.com', $buffer);
			$buffer = preg_replace("|'//(.*?).google.com|", "'https://$1.google.com", $buffer);
			
			// Facebook URLs			
			$buffer = preg_replace('|http://(.*?).fbcdn.net|', 'https://$1.fbcdn.net', $buffer);
			$buffer = preg_replace('|"//(.*?).facebook.net|', '"https://$1.facebook.net', $buffer);
			$buffer = preg_replace("|'//(.*?).facebook.com|", "'https://$1.facebook.com", $buffer);
			
			// Instagram URLs			
			$buffer = preg_replace('|http://(.*?).instagram.com|', 'https://$1.instagram.com', $buffer);
			$buffer = preg_replace('|http://(.*?).cdninstagram.com|', 'https://$1.cdninstagram.com', $buffer);
			
			
			// Some CDNs
			$buffer = str_replace('http://amazonaws.com', 'https://amazonaws.com', $buffer);
			$buffer = preg_replace('|http://(.*?).amazonaws.com|', 'https://$1.amazonaws.com', $buffer);
			$buffer = preg_replace('|http://(.*?).cloudfront.net|', 'https://$1.cloudfront.net', $buffer);
			$buffer = preg_replace('|http://(.*?).cloudfront.com|', 'https://$1.cloudfront.com', $buffer);
			$buffer = preg_replace('|http://(.*?).cloudflare.com|', 'https://$1.cloudflare.com', $buffer);
			$buffer = preg_replace('|http://(.*?).jsdelivr.net|', 'https://$1.jsdelivr.net', $buffer);
			$buffer = preg_replace('|http://(.*?).bootstrapcdn.com|', 'https://$1.bootstrapcdn.com', $buffer);
			$buffer = preg_replace('|http://(.*?).rawgit.com|', 'https://$1.rawgit.com', $buffer);
			$buffer = preg_replace('|http://(.*?).maxcdn.com|', 'https://$1.maxcdn.com', $buffer);

			
			// Disqus URLs			
			$buffer = preg_replace('|http://(.*?).disquscdn.com|', 'https://$1.disquscdn.com', $buffer);
			$buffer = preg_replace('|http://(.*?).disqus.com|', 'https://$1.disqus.com', $buffer);
			
			// Twitter
			$buffer = preg_replace('|http://(.*?).twitter.com|', 'https://$1.twitter.com', $buffer);
			
			// Akamai
			$buffer = preg_replace('|http://(.*?).akamaihd.net|', 'https://$1.akamaihd.net', $buffer);
			
			// Gravatar
			$buffer = preg_replace("|http://(.+).gravatar.com|", "'https://$1.gravatar.com", $buffer);
			
			// WordPress
			$buffer = preg_replace("|'//(.*?).w.org|", "'https://$1.w.org", $buffer);
			
			
		}
		return $buffer;
	}
}
new HTTP_HTTPS_REMOVER();

//Add some links on the plugin page
add_filter('plugin_row_meta', 'http_https_remover_extra_links', 10, 2);

function http_https_remover_extra_links($links, $file) {
	if ( $file == plugin_basename(dirname(__FILE__).'/http-https-remover.php') ) {
		$links[] = '<a href="https://condacore.com/portfolio/http-https-remover/#beta" target="_blank">' . esc_html__('Become a Beta Tester', 'http-https-remover') . '</a>';
		$links[] = '<a href="https://twitter.com/condacore" target="_blank">' . esc_html__('Twitter', 'http-https-remover') . '</a>';
		$links[] = '<a href="https://paypal.me/MariusBolik" target="_blank">' . esc_html__('Donate', 'http-https-remover') . '</a>';
	}
	return $links;
}
