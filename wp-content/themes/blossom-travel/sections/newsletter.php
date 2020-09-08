<?php
/**
 * Newsletter Section
 * 
 * @package Blossom_Travel
 */
if( is_active_sidebar( 'newsletter' ) ) { ?>
    <section id="newsletter_section" class="newsletter-section">
    	<div class="container">
    		<?php dynamic_sidebar( 'newsletter' ); ?>   
    	</div>
    </section>           
<?php }