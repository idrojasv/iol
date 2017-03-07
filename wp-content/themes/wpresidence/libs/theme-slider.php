<?php





if( !function_exists('wpestate_present_theme_slider') ):
    function wpestate_present_theme_slider(){
    
        
        $theme_slider   =   get_option( 'wp_estate_theme_slider_type', true); 
    
        if($theme_slider=='type2'){
            wpestate_present_theme_slider_type2();
            return;
        }
        
        $attr=array(
            'class'	=>'img-responsive'
        );

        $theme_slider   =   get_option( 'wp_estate_theme_slider', ''); 

        if(empty($theme_slider)){
            return; // no listings in slider - just return
        }
        $currency       =   esc_html( get_option('wp_estate_currency_symbol', '') );
        $where_currency =   esc_html( get_option('wp_estate_where_currency_symbol', '') );

        $counter    =   0;
        $slides     =   '';
        $indicators =   '';
        $args = array(  
                    'post_type'        =>   'estate_property',
                    'post_status'      =>   'publish',
                    'post__in'         =>   $theme_slider,
                    'posts_per_page'   =>   -1
                  );
       
        $recent_posts = new WP_Query($args);
        $slider_cycle   =   get_option( 'wp_estate_slider_cycle', true); 
        if($slider_cycle == 0){
            $slider_cycle = false;
        }
        
        $extended_search    =   get_option('wp_estate_show_adv_search_extended','');
        $extended_class     =   '';

        if ( $extended_search =='yes' ){
            $extended_class='theme_slider_extended';
        }

        print '<div class="theme_slider_wrapper '.$extended_class.' carousel  slide" data-ride="carousel" data-interval="'.$slider_cycle.'" id="estate-carousel">';

        while ($recent_posts->have_posts()): $recent_posts->the_post();
               $theid=get_the_ID();
               $slide= get_the_post_thumbnail( $theid, 'property_full_map',$attr );

               if($counter==0){
                    $active=" active ";
                }else{
                    $active=" ";
                }
                $measure_sys    =   get_option('wp_estate_measure_sys','');
                $price          =   floatval( get_post_meta($theid, 'property_price', true) );
                $price_label    =   '<span class="">'.esc_html ( get_post_meta($theid, 'property_label', true) ).'</span>';
                $price_label_before   =   '<span class="">'.esc_html ( get_post_meta($theid, 'property_label_before', true) ).'</span>';
                $beds           =   floatval( get_post_meta($theid, 'property_bedrooms', true) );
                $baths          =   floatval( get_post_meta($theid, 'property_bathrooms', true) );
                $size           =   wpestate_sizes_no_format ( floatval( get_post_meta($theid, 'property_size', true) ) ,1);

                if($measure_sys=='ft'){
                    $size.=' '.__('ft','wpestate').'<sup>2</sup>';
                }else{
                    $size.=' '.__('m','wpestate').'<sup>2</sup>';
                }
                
                if ($price != 0) {
                   $price  = wpestate_show_price($theid,$currency,$where_currency,1);  
                }else{
                    $price=$price_label_before.''.$price_label;
                }


               $slides.= '
               <div class="item '.$active.'">
                    <a class="theme_slider_slide" href="'.get_permalink().'"> '.$slide.' </a>
                    <div class="slider-content-wrapper">  
                    <div class="slider-content">

                        <h3><a href="'.get_permalink().'">'.get_the_title().'</a> </h3>
                        <span> '. wpestate_strip_words( get_the_excerpt(),20).' ...<a href="'.get_permalink().'" class="read_more">'.__('Read more','wpestate').'<i class="fa fa-angle-right"></i></a></span>

                         <div class="theme-slider-price">
                            '.$price.'  
                            <div class="listing-details">';
                            if($beds!=0){
                                $slides.= '<img src="'.get_template_directory_uri().'/css/css-images/icon_bed.png"  alt="listings-beds">'.$beds;
                            }
                            if($baths!=0){
                                $slides.= '  <img src="'.get_template_directory_uri().'/css/css-images/icon_bath.png" alt="lsitings_baths">'.$baths;
                            }
                            if($size!=0){
                                $slides.= '  <img src="'.get_template_directory_uri().'/css/css-images/icon-size.png" alt="lsitings_size">'.$size;
                            }
                            
                            $slides.= '    
                            </div>
                         </div>

                         <a class="carousel-control-theme-next" href="#estate-carousel" data-slide="next"><i class="fa fa-angle-right"></i></a>
                         <a class="carousel-control-theme-prev" href="#estate-carousel" data-slide="prev"><i class="fa fa-angle-left"></i></a>
                    </div> 
                    </div>
                </div>';

               $indicators.= '
               <li data-target="#estate-carousel" data-slide-to="'.($counter).'" class="'. $active.'">

               </li>';

               $counter++;
        endwhile;
        wp_reset_query();
        print '<div class="carousel-inner" role="listbox">
                  '.$slides.'
               </div>

               <ol class="carousel-indicators">
                    '.$indicators.'
               </ol>





               </div>';
    } 
endif;




if( !function_exists('wpestate_present_theme_slider_type2') ):
    function wpestate_present_theme_slider_type2(){
    
        
       
        
        $attr=array(
            'class'	=>'img-responsive'
        );

        $theme_slider   =   get_option( 'wp_estate_theme_slider', ''); 

        if(empty($theme_slider)){
            return; // no listings in slider - just return
        }
        $currency       =   esc_html( get_option('wp_estate_currency_symbol', '') );
        $where_currency =   esc_html( get_option('wp_estate_where_currency_symbol', '') );

        $counter    =   0;
        $slides     =   '';
        $indicators =   '';
        $args = array(  
                    'post_type'        =>   'estate_property',
                    'post_status'      =>   'publish',
                    'post__in'         =>   $theme_slider,
                    'posts_per_page'   =>   -1
                  );
       
        $recent_posts = new WP_Query($args);
        $slider_cycle   =   get_option( 'wp_estate_slider_cycle', true); 
       
        $extended_search    =   get_option('wp_estate_show_adv_search_extended','');
        $extended_class     =   '';

        if ( $extended_search =='yes' ){
            $extended_class='theme_slider_extended';
        }

        print '<div class="theme_slider_wrapper theme_slider_2 '.$extended_class.' " data-auto="'.$slider_cycle.'">';

        while ($recent_posts->have_posts()): $recent_posts->the_post();
               $theid=get_the_ID();
           
                $preview        =   wp_get_attachment_image_src(get_post_thumbnail_id(), 'property_full_map');
                if($preview[0]==''){
                    $preview[0]= get_template_directory_uri().'/img/defaults/default_property_featured.jpg';
                }

               if($counter==0){
                    $active=" active ";
                }else{
                    $active=" ";
                }
                $measure_sys    =   get_option('wp_estate_measure_sys','');
                $price          =   floatval( get_post_meta($theid, 'property_price', true) );
                $price_label    =   '<span class="">'.esc_html ( get_post_meta($theid, 'property_label', true) ).'</span>';
                $price_label_before   =   '<span class="">'.esc_html ( get_post_meta($theid, 'property_label_before', true) ).'</span>';
                $beds           =   floatval( get_post_meta($theid, 'property_bedrooms', true) );
                $baths          =   floatval( get_post_meta($theid, 'property_bathrooms', true) );
                $size           =   wpestate_sizes_no_format ( floatval( get_post_meta($theid, 'property_size', true) ) ,1);

                if($measure_sys=='ft'){
                    $size.=' '.__('ft','wpestate').'<sup>2</sup>';
                }else{
                    $size.=' '.__('m','wpestate').'<sup>2</sup>';
                }
                
                if ($price != 0) {
                   $price  = wpestate_show_price($theid,$currency,$where_currency,1);  
                }else{
                    $price=$price_label_before.''.$price_label;
                }


               $slides.= '
               <div class="item_type2 '.$active.'"  style="background-image:url('.$preview[0].')">
                   
                

                        <div class="prop_new_details" data-href="'.get_permalink().'">
                            <div class="prop_new_details_back"></div>
                            <div class="prop_new_detals_info">
                                <div class="theme-slider-price">
                                    '.$price.'  
                                </div>
                                <h3><a href="'.get_permalink().'">'.get_the_title().'</a> </h3>
                                
                            </div>
                        </div>
                   
                </div>';

            
               $counter++;
        endwhile;
        wp_reset_query();
        print $slides.'
            </div>';
    } 
endif;

?>
