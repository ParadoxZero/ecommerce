<?php
/**
 *	Plugin Name: Live Composer - Video Embed Module
 *	Plugin URI: http://www.livecomposer.io
 *	Description: Adds a new module for embeding videos.
 *	Author: Live Composer Team
 *	Version: 1.0.2
 * Author URI: http://livecomposer.io
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /lang
 *
 * Video Embed Module Plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Video Embed Module Plugin. If not, see <http://www.gnu.org/licenses/>.
 *
 * Idea, initial development and inspiration by
 * Slobodan Kustrimovic https://github.com/BobaWebDev
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Register Module
 *
 * @since 1.0
 */
function lc_video_embed_module_init() {

	// Live Composer not active, do not proceed.
	if ( ! defined( 'DS_LIVE_COMPOSER_VER' ) ) {
		return;
	}

	dslc_register_module( 'LC_Video_Embed_Module' );

} add_action( 'dslc_hook_register_modules', 'lc_video_embed_module_init' );

/**
 * Load plugin textdomain.
 *
 * @since 1.0
 */
function lc_video_embed_module_i18n() {

	load_plugin_textdomain( 'lc-video-embed', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

} add_action( 'plugins_loaded', 'lc_video_embed_module_i18n' );

/**
 * Procceed only when all the plugins loaded
 */
function lc_video_embed_moduled_class() {

	if ( ! defined( 'DS_LIVE_COMPOSER_VER' ) ) {
		return;
	}

	/**
	 * The Module Class
	 */
	class LC_Video_Embed_Module extends DSLC_Module {

		var $module_id;
		var $module_title;
		var $module_icon;
		var $module_category;

		function __construct() {

			$this->module_id = 'LC_Video_Embed_Module';
			$this->module_title = __( 'Video Embed', 'lc-video-embed' );
			$this->module_icon = 'play';
			$this->module_category = 'Extensions';

		}

		/**
		 * Module Options
		 *
		 * @since 1.0
		 */
		function options() {

			$dslc_options = array(

				array(
					'label' => __( 'Show On', 'lc-video-embed' ),
					'id' => 'css_show_on',
					'std' => 'desktop tablet phone',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Desktop', 'lc-video-embed' ),
							'value' => 'desktop',
						),
						array(
							'label' => __( 'Tablet', 'lc-video-embed' ),
							'value' => 'tablet',
						),
						array(
							'label' => __( 'Phone', 'lc-video-embed' ),
							'value' => 'phone',
						),
					),
				),
				array(
					'label' => __( 'Video URL', 'lc-video-embed' ),
					'id' => 'video_url',
					'std' => '',
					'type' => 'text',
				),
				array(
					'label' => __( 'Height', 'lc-video-embed' ),
					'id' => 'video_height',
					'std' => '',
					'type' => 'text',
				),
				array(
					'label' => __( 'Width', 'lc-video-embed' ),
					'id' => 'video_width',
					'std' => '',
					'type' => 'text',
				),

				/**
				 * Hidden
				 */

				array(
					'label' => 'Embed Bottom Margin',
					'id' => 'css_embed_margin_bottom',
					'std' => '0',
					'type' => 'text',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed iframe',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
				),

				/**
				 * General
				 */

				array(
					'label' => __( 'Align', 'lc-video-embed' ),
					'id' => 'css_align',
					'std' => 'left',
					'type' => 'text_align',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed',
					'affect_on_change_rule' => 'text-align',
					'section' => 'styling',
				),
				array(
					'label' => __( 'BG Color', 'lc-video-embed' ),
					'id' => 'css_bg_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
				),
				array(
					'label' => __( 'BG Color - Hover', 'lc-video-embed' ),
					'id' => 'css_bg_color_hover',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed:hover',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
				),
				array(
					'label' => __( 'Border Color', 'lc-video-embed' ),
					'id' => 'css_border_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
				),
				array(
					'label' => __( 'Border Color - Hover', 'lc-video-embed' ),
					'id' => 'css_border_color_hover',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed:hover',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
				),
				array(
					'label' => __( 'Border Width', 'lc-video-embed' ),
					'id' => 'css_border_width',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Borders', 'lc-video-embed' ),
					'id' => 'css_border_trbl',
					'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'lc-video-embed' ),
							'value' => 'top',
						),
						array(
							'label' => __( 'Right', 'lc-video-embed' ),
							'value' => 'right',
						),
						array(
							'label' => __( 'Bottom', 'lc-video-embed' ),
							'value' => 'bottom',
						),
						array(
							'label' => __( 'Left', 'lc-video-embed' ),
							'value' => 'left',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
				),
				array(
					'label' => __( 'Border Radius', 'lc-video-embed' ),
					'id' => 'css_border_radius',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed',
					'affect_on_change_rule' => 'border-radius',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Bottom', 'lc-video-embed' ),
					'id' => 'css_margin_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Minimum Height', 'lc-video-embed' ),
					'id' => 'css_min_height',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed',
					'affect_on_change_rule' => 'min-height',
					'section' => 'styling',
					'ext' => 'px',
					'min' => 0,
					'max' => 1000,
					'increment' => 5,
				),
				array(
					'label' => __( 'Padding Vertical', 'lc-video-embed' ),
					'id' => 'css_padding_vertical',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Horizontal', 'lc-video-embed' ),
					'id' => 'css_padding_horizontal',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Box Shadow', 'live-composer-page-builder' ),
					'id' => 'css_box_shadow',
					'std' => '',
					'type' => 'box_shadow',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed',
					'affect_on_change_rule' => 'box-shadow',
					'section' => 'styling',
				),

				/**
				 * Responsive tablet
				 */

				array(
					'label' => __( 'Responsive Styling', 'lc-video-embed' ),
					'id' => 'css_res_t',
					'std' => 'disabled',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Disabled', 'lc-video-embed' ),
							'value' => 'disabled',
						),
						array(
							'label' => __( 'Enabled', 'lc-video-embed' ),
							'value' => 'enabled',
						),
					),
					'section' => 'responsive',
					'tab' => __( 'tablet', 'lc-video-embed' ),
				),
				array(
					'label' => __( 'Padding Vertical', 'lc-video-embed' ),
					'id' => 'css_res_t_padding_vertical',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'responsive',
					'tab' => __( 'tablet', 'lc-video-embed' ),
					'max' => 500,
					'increment' => 1,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Horizontal', 'lc-video-embed' ),
					'id' => 'css_res_t_padding_horizontal',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'responsive',
					'tab' => __( 'tablet', 'lc-video-embed' ),
					'ext' => 'px',
				),

				/**
				 * Responsive phone
				 */

				array(
					'label' => __( 'Responsive Styling', 'lc-video-embed' ),
					'id' => 'css_res_p',
					'std' => 'disabled',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Disabled', 'lc-video-embed' ),
							'value' => 'disabled',
						),
						array(
							'label' => __( 'Enabled', 'lc-video-embed' ),
							'value' => 'enabled',
						),
					),
					'section' => 'responsive',
					'tab' => __( 'phone', 'lc-video-embed' ),
				),
				array(
					'label' => __( 'Padding Vertical', 'lc-video-embed' ),
					'id' => 'css_res_p_padding_vertical',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'responsive',
					'tab' => __( 'phone', 'lc-video-embed' ),
					'max' => 500,
					'increment' => 1,
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Horizontal', 'lc-video-embed' ),
					'id' => 'css_res_p_padding_horizontal',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.lc-video-embed',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'responsive',
					'tab' => __( 'phone', 'lc-video-embed' ),
					'ext' => 'px',
				),

			);

			$dslc_options = array_merge( $dslc_options, $this->presets_options() );

			return apply_filters( 'dslc_module_options', $dslc_options, $this->module_id );

		}

		/**
		 * Module Output
		 *
		 * @since 1.0
		 * @param array $options Saved Module Options.
		 */
		function output( $options ) {

			$this->module_start( $options );

			/* Module output stars here */
			echo '<div class="lc-video-embed" style="line-height:0">';

			// Embed Arguments.
			$embed_args = array();

			// Embed Argument Height.
			if ( isset( $options['video_height'] ) && $options['video_height'] && '' !== $options['video_height'] ) {
				$embed_args['height'] = $options['video_height'];
			}

			// Embed Argument Width.
			if ( isset( $options['video_width'] ) && $options['video_width'] && '' !== $options['video_width']  ) {
				$embed_args['width'] = $options['video_width'];
			}

			// If a video URL is set.
			if ( isset( $options['video_url'] ) && '' !== $options['video_url'] ) {

				// Get embed code ( false if wrong ).
				$embed_code = wp_oembed_get( $options['video_url'], $embed_args );

				// If embed code false.
				if ( ! $embed_code ) {

					// Show meessage if editor is active.
					if ( dslc_is_editor_active() ) {
						echo '<div class="dslc-notification dslc-red">';
							esc_html_e( 'Make sure you entered a valid URL ( ex. https://www.youtube.com/watch?v=ONHBaC-pfsk )', 'lc-video-embed' );
						echo '</div>';
					}
				} else {
					// If embed code ok, display it.
					echo $embed_code;
				}

				// If no video URL supplied.
			} else {

				// Show message if editor active.
				if ( dslc_is_editor_active() ) {
					echo '<div class="dslc-notification dslc-red">';
						esc_html_e( 'A video URL needs to be set in the module options.', 'lc-video-embed' );
					echo '</div>';
				}
			}

			echo '</div>'; // .lc-video-embed.

			/* Module output ends here */
			$this->module_end( $options );
		}
	}

} add_action( 'plugins_loaded', 'lc_video_embed_moduled_class' );
