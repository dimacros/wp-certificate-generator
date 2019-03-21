<?php

add_filter('post_class', function($classes){

    if( is_singular('tc_events') ) {
    		$classes[] = 'woocommerce';
    }

    return $classes;
});

add_filter('the_content', function($content){
        
		if( is_singular('tc_events') && in_the_loop() ) {
    		return wc_print_notices(true) . $content;
 		}

		return $content;
}); 

add_filter('tc_event_shortcode', function($content){

		return '[tc_wb_event 
				event_table_class="shop_table shop_table_responsive"
				ticket_type_title="Tipo"
				price_title="Precio"
				cart_title=""
				wrapper="div"
		]';

}, 20);