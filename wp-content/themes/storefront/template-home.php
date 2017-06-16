<?php
/**
 * The template for displaying full home page.
 *
 * Template Name: Home Page
 *
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<link rel="stylesheet" href="https://www.trymake.com/wp-content/themes/storefront/home-style.css">

<?php
        do_action( 'tm_inside_head' );
         wp_head();
?>
</head>
<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
        <?php
        do_action( 'storefront_before_header' ); ?>

        <header id="masthead" class="site-header" role="banner" style="<?php storefront_header_styles(); ?>">
                <div class="col-full">

                        <?php
                        /**
                         * Functions hooked into storefront_header action
                         *
                         * @hooked storefront_skip_links                       - 0
                         * @hooked storefront_social_icons                     - 10
                         * @hooked storefront_site_branding                    - 20
                         * @hooked storefront_secondary_navigation             - 30
                         * @hooked storefront_product_search                   - 40
                         * @hooked storefront_primary_navigation_wrapper       - 42
                         * @hooked storefront_primary_navigation               - 50
                         * @hooked storefront_header_cart                      - 60
                         * @hooked storefront_primary_navigation_wrapper_close - 68
                         */
                        do_action( 'storefront_header' ); ?>

 <script src="https://www.trymake.com/wp-content/themes/storefront/js/jquery.js"></script>
<script src="https://www.trymake.com/wp-content/themes/storefront/js/flip.js"></script>
               </div>
        </header><!-- #masthead -->
<header id="masthead" class="site-header" role="banner" style="position:relative; z-index:0; margin-bottom:0 !important;<?php storefront_header_styles(); ?>">
                <div class="col-full">

                        <?php
                        /**
                         * Functions hooked into storefront_header action
                         *
                         * @hooked storefront_skip_links                       - 0
                         * @hooked storefront_social_icons                     - 10
                         * @hooked storefront_site_branding                    - 20
                         * @hooked storefront_secondary_navigation             - 30
                         * @hooked storefront_product_search                   - 40
                         * @hooked storefront_primary_navigation_wrapper       - 42
                         * @hooked storefront_primary_navigation               - 50
                         * @hooked storefront_header_cart                      - 60
                         * @hooked storefront_primary_navigation_wrapper_close - 68
                         */
                        do_action( 'storefront_header' ); ?>

 <script src="https://www.trymake.com/wp-content/themes/storefront/js/jquery.js"></script>

<script src="https://www.trymake.com/wp-content/themes/storefront/js/flip.js"></script>
               </div>
        </header><!-- #masthead -->
      
	<div class="container">
        <div id="section-0"></div>
	<div id="section-1">
            <div id="sec-1-colomn-1" class="sec-1-col">
                <div id="sec-1-card-1" class="card" onclick="location.href='#';">
                    <div class="front" style="background-color: #19979c ;"></div>
                    <div class="back" style="background-color: #19979c ;">

			<h3  style="margin-top:0; text-align: center;font-style: italic ">Rasberry pi</h3>
                        <br/><br/>
                        <p style="margin: 1em">Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                            Nam viverra vestibulum arcu quis condimentum. Mauris malesuada
                            sed est tincidunt sodales. Curabitur ligula eros, ullamcorper ac
                            risus fermentum, ullamcorper tincidunt justo. Sed sit amet aliquet
                            ante, quis blandit lorem. In sit amet nibh quis sem condimentum ornare.</p>
                                     </div>
                </div>
                <div id="sec-1-card-1-1" class="card" onclick="location.href='#';" >
                    <div class="front" style="background-color: #19979c ;"></div>
                    <div class="back" style="background-color: #19979c ;">
	       
			<h3  style="margin-top:0; text-align: center;font-style: italic ">Robo - Display</h3>
                        <br/><br/>
                        <p style="margin: 1em">Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                            Nam viverra vestibulum arcu quis condimentum. Mauris malesuada
                            sed est tincidunt sodales. Curabitur ligula eros, ullamcorper ac
                            risus fermentum, ullamcorper tincidunt justo. Sed sit amet aliquet
                            ante, quis blandit lorem. In sit amet nibh quis sem condimentum ornare.</p>
                                     </div>
                </div>
            </div>
            <div id="sec-1-colomn-2" class="sec-1-col">
                <div id="sec-1-card-2" class="card" onclick="location.href='#';">
                    <div class="front" >

                    </div>
                    <div class="back" style="background-color: #00adef ; ">
                        <h3 style="margin-top:0; text-align: center;font-style: italic ">Arduino - kit Complete set</h3>
                        <br/><br/>
                        <p style="margin: 3em">Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                            Nam viverra vestibulum arcu quis condimentum. Mauris malesuada
                            sed est tincidunt sodales. Curabitur ligula eros, ullamcorper ac
                            risus fermentum, ullamcorper tincidunt justo. Sed sit amet aliquet
                            ante, quis blandit lorem. In sit amet nibh quis sem condimentum ornare.</p>
                    
                    </div>
                </div>
                <div id="sec-1-card-3" class="card" onclick="location.href='#';" >
                    <div class="front" style="background-color: #B7695C ;" ></div>
                    <div class="back" style="background-color: #acbbb7 ; ">
                        <h3 style="margin-top:0; text-align: center;font-style: italic ">All Terrian Cart</h3>
                        <br/><br/>
                        <p style="margin: 3em">Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                            Nam viverra vestibulum arcu quis condimentum. Mauris malesuada
                            sed est tincidunt sodales. Curabitur ligula eros, ullamcorper ac
                            risus fermentum, ullamcorper tincidunt justo. Sed sit amet aliquet
                            ante, quis blandit lorem. In sit amet nibh quis sem condimentum ornare.</p>
                    
                    </div>
                </div>
            </div>
            <div id="sec-1-colomn-3" class="sec-1-col">
                <div id="sec-1-card-4" class="card" onclick="location.href='#';" >
                    <div class="front" style="background-color: #26bdce ; "></div>
                    <div class="back" style="background-color: #26bdce ;">
                        <h3 style="margin-top:0; text-align: center;font-style: italic ">Plastic ball - Caster wheel</h3>
                        <br/><br/>
                        <p style="margin: 1em">Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                            Nam viverra vestibulum arcu quis condimentum. Mauris malesuada
                            sed est tincidunt sodales. Curabitur ligula eros, ullamcorper ac
                            risus fermentum, ullamcorper tincidunt justo. Sed sit amet aliquet
                            ante, quis blandit lorem. In sit amet nibh quis sem condimentum ornare.</p>
                    
                    </div>
                </div>
                <div id="sec-1-card-4-1" class="card" onclick="location.href='#';" >
                    <div class="front" style="background-color: #26bdce ; "></div>
                    <div class="back" style="background-color: #26bdce ;">
                        Back content
                    </div>
                </div>
            </div>
        </div>
        <div id="section-2">
        </div>
        <div id="section-3">
            <div id="sec-3-colomn-1" class="sec-1-col">
                <div id="sec-3-card-1" class="card" onclick="location.href='#';" >
                    <div class="front" style="background-color: #CCCC9A ;"></div>
                    <div class="back" style="background-color: #CCCCCC ;">
                        Back content
                    </div>
                </div>
                <div id="sec-3-card-2" class="card" onclick="location.href='#';" >
                    <div class="front" style="background-color: #98B1C4 ;">
                        Front content
                    </div>
                    <div class="back" style="background-color: #C8D7E3 ; ">
                        Back content
                    </div>
                </div>
            </div>
            <div id="sec-3-colomn-2" class="sec-1-col">
                <div id="sec-3-card-3" class="card" onclick="location.href='#';" >
                    <div class="front" style="background-color: #2F4E6F ;" ></div>
                    <div class="back" style="background-color: #98B1C4 ; ">
                        Back content
                    </div>
                </div>
            </div>
            <div id="sec-3-colomn-3"  class="sec-1-col">
                <div id="sec-3-card-4" class="card" onclick="location.href='#';"  >
                    <div class="front" style="background-color: white ;" >
                    </div>
                    <div class="back" style="background-color: #FF7182 ; ">
                        Back content
                    </div>
                </div>
                <div id="sec-3-card-5" class="card" onclick="location.href='#';"  >
                    <div class="front" style="background-color: #FFAE5D ;" ></div>
                    <div class="back" style="background-color: #FF7182 ; ">
                        Back content
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    $(".card").flip()
</script>
<script src="https://www.trymake.com/wp-content/themes/storefront/js/flip_modified.js"></script>
<?php
get_footer();
