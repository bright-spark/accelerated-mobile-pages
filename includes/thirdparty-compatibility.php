<?php
//Remove ResponsifyWP #1131
add_action('plugins_loaded', 'ampforwp_filter_remove_function_responsifywp');
function ampforwp_filter_remove_function_responsifywp(){
  if(is_plugin_active('responsify-wp/responsify-wp.php')){
	add_filter('rwp_add_filters','removeFilterOfResponsify');
	function removeFilterOfResponsify($filter){
	  return '';
	}
  }
}

// Strange spaces when using Sassy Social Share #1185
add_filter('heateor_sss_disable_sharing','ampforwp_removing_sassy_social_share');
function ampforwp_removing_sassy_social_share(){
	if(function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint()){
		return 1;
	}
}

// Remove Schema theme Lazy Load #1170

add_action('pre_amp_render_post','schema_lazy_load_remover');
function schema_lazy_load_remover(){
	remove_filter( 'wp_get_attachment_image_attributes', 'mts_image_lazy_load_attr', 10, 3 );
	remove_filter('the_content', 'mts_content_image_lazy_load_attr');
}

//Updater to check license
require_once AMPFORWP_PLUGIN_DIR . '/includes/updater/update.php';


if(!function_exists('ampforwp_amp_nonamp_convert')){
	function ampforwp_amp_nonamp_convert($ampData, $type=""){
		$returnData = '';
		if("check" === $type){
			return ampforwp_is_non_amp('non_amp_check_convert');
		}
		if(!ampforwp_is_non_amp('non_amp_check_convert')){
			return $ampData;
		}
		switch($type){
			case 'filter':
				$returnData = str_replace(array(
												"</amp-img>",
												"amp-img",
												"<style amp-custom>",
												"<amp-sidebar ",
												"</amp-sidebar>",
												'on="tap:ampforwpConsent.dismiss"',
												'<div id="post-consent-ui"',
												'on="tap:ampforwpConsent.reject"',
												'on="tap:ampforwpConsent.accept"'

												),
											array(
												"",
												"img",
												"<style type=\"text/css\">",
												"<sidebar ",
												"</sidebar>",
												'onClick="ampforwp_gdrp_set()"',
												'<script>
												function ampforwp_gdpr_getCookie(name) {
												  var value = "; " + document.cookie;
												  var parts = value.split("; " + name + "=");
												  if (parts.length == 2) return parts.pop().split(";").shift();
												}
												function ampforwp_gdpr(){
											if(ampforwp_gdpr_getCookie(\'ampforwpcookie\') == \'1\'){document.getElementById(\'gdpr_c\').remove();}
											}ampforwp_gdpr();
											function ampforwp_gdrp_set(){document.getElementById(\'ampforwpConsent\').remove(); document.cookie = \'ampforwpcookie=1;expires;path=/\';}
												</script><div id="post-consent-ui"',
												'onClick="ampforwp_gdrp_set()"',
												'onClick="ampforwp_gdrp_set()"',
												)
											, $ampData);

				$returnData = preg_replace(
                '/<amp-youtube\sdata-videoid="(.*?)"(.*?)><\/amp-youtube>/',
                 '<iframe src="https://www.youtube.com/embed/$1" style="width:100%;height:360px;" ></iframe>', $returnData);
				$returnData = preg_replace_callback(
                '/<amp-iframe(.*?)src="(.*?)"(.*?)><\/amp-iframe>/', 
                function($matches){
                	return '<iframe src="'.esc_url($matches[2]).'" style="width:100%;height:400px;" ></iframe>';
                }, $returnData);
				// CSS
				
				if( false !== strpos($returnData, 'amp-carousel') ) {
					$galleryCss = '* {box-sizing: border-box}.mySlides{display: none}
						/* Slideshow container */
						.slideshow-container {
						  max-width: 1000px;
						  position: relative;
						  margin: auto;
						}
						/* Next & previous buttons */
						.nonamp-prev, .nonamp-next {
						  cursor: pointer;
						  position: absolute;
						  top: 50%;
						  width: auto;
						  padding: 16px;
						  margin-top: -22px;
						  color: white;
						  font-weight: bold;
						  font-size: 18px;
						  transition: 0.6s ease;
						  border-radius: 0 3px 3px 0;
						  user-select: none;
						}
						/* Position the "next button" to the right */
						.nonamp-next {
						  right: 0;
						  border-radius: 3px 0 0 3px;
						}
						/* On hover, add a black background color with a little bit see-through */
						.nonamp-prev:hover, .nonamp-next:hover {
						  background-color: rgba(0,0,0,0.8);
						}
						/* Caption text */
						.text {
						  color: #f2f2f2;
						  font-size: 15px;
						  padding: 8px 12px;
						  position: absolute;
						  bottom: 8px;
						  width: 100%;
						  text-align: center;
						}
						/* Number text (1/3 etc) */
						.numbertext {
						  color: #f2f2f2;
						  font-size: 12px;
						  padding: 8px 12px;
						  position: absolute;
						  top: 0;
						}
						/* The dots/bullets/indicators */
						.dot {
						  cursor: pointer;
						  height: 15px;
						  width: 15px;
						  margin: 0 2px;
						  background-color: #bbb;
						  border-radius: 50%;
						  display: inline-block;
						  transition: background-color 0.6s ease;
						}
						.active, .dot:hover {
						  background-color: #717171;
						}
						/* Fading animation */
						.fade {
						  -webkit-animation-name: fade;
						  -webkit-animation-duration: 1.5s;
						  animation-name: fade;
						  animation-duration: 1.5s;
						}
						@-webkit-keyframes fade {
						  from {opacity: .4} 
						  to {opacity: 1}
						}
						@keyframes fade {
						  from {opacity: .4} 
						  to {opacity: 1}
						}
						/* On smaller screens, decrease text size */
						@media only screen and (max-width: 300px) {
						  .nonamp-prev, .nonamp-next,.text {font-size: 11px}
						}';
					$galleryJs = '<script>
									var slideIndex = 1;
									showSlides(slideIndex);
									function plusSlides(n) {
									  showSlides(slideIndex += n);
									}
									function currentSlide(n) {
									  showSlides(slideIndex = n);
									}
									function showSlides(n) {
									  var i;
									  var slides = document.getElementsByClassName("mySlides");
									  var dots = document.getElementsByClassName("dot");
									  if (n > slides.length) {slideIndex = 1}    
									  if (n < 1) {slideIndex = slides.length}
									  for (i = 0; i < slides.length; i++) {
									      slides[i].style.display = "none";  
									  }
									  for (i = 0; i < dots.length; i++) {
									      dots[i].className = dots[i].className.replace(" active", "");
									  }
									  slides[slideIndex-1].style.display = "block";  
									  dots[slideIndex-1].className += " active";
									}
									</script>';
				}
				$nonampCss = '
				.cntr img{height:auto !important;}
				img{height:auto;width:100%;}
				.slid-prv{width:100%;text-align: center;margin-top: 10px;display: inline-block;}
				.amp-featured-image img{width:100%;height:auto;}
				.content-wrapper, .header, .header-2, .header-3{width:100% !important;}
				.image-mod img{width:100%;}
				';
				$re = '/<style\s*type="text\/css">(.*?)<\/style>/si';
				$subst = "<style type=\"text/css\">$1 ".$nonampCss.$galleryCss."</style>";
				$returnData = preg_replace($re, $subst, $returnData);

				$returnData = preg_replace_callback('/<amp-carousel\s(.*?)>(.*?)<\/amp-carousel>/s', 'ampforwp_non_amp_gallery', $returnData );
				$returnData = str_replace('</footer>', '</footer>'.$galleryJs, $returnData);
			break;
		}
		return $returnData;
	}
}

//
function ampforwp_non_amp_gallery($matches){
	$images =  $matches[2];
	$images = preg_replace_callback("/<img(.*?)>/", function($m){
		return '<li class="mySlides fade">'.$m[0].'</li>';
	}, $images);
	$imagesHTML = '<ul class="slideshow-container">'.$images.'<a class="nonamp-prev" onclick="plusSlides(-1)">&#10094;</a>
<a class="nonamp-next" onclick="plusSlides(1)">&#10095;</a></ul>';
	return $imagesHTML;
}

//Facility to create child theme For AMP
	add_filter( 'amp_post_template_file', 'ampforwp_child_custom_header_file', 20, 3 );
	add_filter( 'amp_post_template_file', 'ampforwp_child_designing_custom_template', 20, 3 );
	add_filter( 'amp_post_template_file', 'ampforwp_child_custom_footer_file', 20, 3 );
	function ampforwp_theme_template_directry(){
		$folder_name = 'ampforwp';
		$folder_name = apply_filters('ampforwp_template_locate', $folder_name);	
		return get_stylesheet_directory() . '/' . $folder_name;
	}
	// Custom Header
	function ampforwp_child_custom_header_file( $file, $type, $post ) {
		$currentFile = $file;
		if ( 'header' === $type ) {
			$file = ampforwp_theme_template_directry() . '/header.php';
			
		}
		if ( 'header-bar' === $type ) {
			$file = ampforwp_theme_template_directry() . '/header-bar.php';
		}
		if(!file_exists($file)){
			$file = $currentFile;
		}
		return $file;
	}

	// Custom Template Files
	function ampforwp_child_designing_custom_template( $file, $type, $post ) { 
	 global $redux_builder_amp;
	 $currentFile = $file;
	 $filePath = ampforwp_theme_template_directry();
		// Single file
	    if ( is_single() ) {
			if( 'single' === $type && ! ('product' === $post->post_type) ) {
				$file = $filePath . '/single.php';
		 	}
		}
		if ( is_page() ) {
			if( 'single' === $type && ! ('product' === $post->post_type) ) {
				$file = $filePath . '/page.php';
		 	}
		}
	    // Loop Template
	    if ( 'loop' === $type ) {
			$file = $filePath . '/loop.php';
		}
	    // Archive
		if ( is_archive() ) {
	        if ( 'single' === $type ) {
	            $file = $filePath . '/archive.php';
	        }
	    }
	    $ampforwp_custom_post_page = ampforwp_custom_post_page();
	    // Homepage
		if ( is_home() ) {
			if ( 'single' === $type ) {
	        	$file = $filePath . '/index.php';
	        
		        if ( $redux_builder_amp['amp-frontpage-select-option'] == 1 ) {
					$file = $filePath . '/page.php';
		        }
		        if ( ampforwp_is_blog() ) {
				 	$file = $filePath . '/index.php';
				}
		    }
	    }
	    // is_search
		if ( is_search() ) {
	        if ( 'single' === $type ) {
	            $file = $filePath . '/search.php';
	        }
	    }
	    // Template parts
	    if ( 4 != ampforwp_get_setting('amp-design-selector') ) {
	    	if ( 'ampforwp-the-title' === $type ) {
				$file = $filePath .'/elements/title.php' ;
			}
			if ( 'ampforwp-meta-info' === $type ) {
				$file = $filePath .'/elements/meta-info.php' ;
			}
			if ( 'ampforwp-featured-image' === $type ) {
				$file = $filePath .'/elements/featured-image.php' ;
			}
			if ( 'ampforwp-bread-crumbs' === $type ) {
				$file = $filePath .'/elements/bread-crumbs.php' ;
			}
			if ( 'ampforwp-the-content' === $type ) {
				$file = $filePath .'/elements/content.php' ;
			}
			if ( 'ampforwp-meta-taxonomy' === $type ) {
				$file = $filePath .'/elements/meta-taxonomy.php' ;
			}
			if ( 'ampforwp-social-icons' === $type ) {
				$file = $filePath .'/elements/social-icons.php' ;
			}
			if ( 'ampforwp-comments' === $type ) {
				$file = $filePath .'/elements/comments.php' ;
			}
			if ( 'ampforwp-simple-comment-button' === $type ) {
				$file = $filePath .'/elements/simple-comment-button.php' ;
			}
			if ( 'ampforwp-related-posts' === $type ) {
				$file = $filePath .'/elements/related-posts.php' ;
			}
			if ( 'ampforwp-addthis' === $type ) {
				$file = $filePath .'/elements/addthis.php' ;
			}
			if ( 'ampforwp-ad7' === $type ) {
				$file = $filePath .'/elements/ad7.php' ;
			}
			if ( 'ampforwp-ad8' === $type ) {
				$file = $filePath .'/elements/ad8.php' ;
			}
	    }
	    //For template pages
	    switch ( true ) {
	    	case ampforwp_is_front_page():
				$templates[] = $filePath . "/front-page.php";
				 foreach ( $templates as $key => $value ) {
					if ( 'single' == $type && file_exists($value) ) {
						$file = $value;
						break;
					}
				}
	    	break;
	    	case ampforwp_is_home():
				$templates[] = $filePath . "/home.php";
				$templates[] = $filePath . "/index.php";
				 foreach ( $templates as $key => $value ) { 
					if ( 'single' == $type && file_exists($value) ) {
						$file = $value;
						break;
					}
				} 
	    	break;
	    	case (is_tax()):
	    			$term = get_queried_object();
					$templates = array();
					if ( ! empty( $term->slug ) ) {
						$taxonomy = $term->taxonomy;
						$slug_decoded = urldecode( $term->slug );
						if ( $slug_decoded !== $term->slug ) {
							$templates[] = $filePath . "/taxonomy-$taxonomy-{$slug_decoded}.php";
						}
						$templates[] = $filePath . "/taxonomy-$taxonomy-{$term->slug}.php";
						$templates[] = $filePath . "/taxonomy-$taxonomy.php";
					}
					$templates[] = $filePath . "/taxonomy.php";
					foreach ( $templates as $key => $value ) {
						if ( 'single' === $type && file_exists($value) ) {
							$file = $value;
							break;
						}
					}
	    	break;
	    	case (is_category()):
	    		$category = get_queried_object();
				$templates = array();
				if ( ! empty( $category->slug ) ) {
					$slug_decoded = urldecode( $category->slug );
					if ( $slug_decoded !== $category->slug ) {
						$templates[] = $filePath . "/category-{$slug_decoded}.php";
					}
					$templates[] = $filePath . "/category-{$category->slug}.php";
					$templates[] = $filePath . "/category-{$category->term_id}.php";
				}
				$templates[] = $filePath . '/category.php';
				foreach ( $templates as $key => $value ) {
					if ( 'single' === $type && file_exists($value) ) {
						$file = $value;
						break;
					}
				}
	    	break;
	    	case (is_tag()):
	    		$tag = get_queried_object();
				$templates = array();
				if ( ! empty( $tag->slug ) ) {
					$slug_decoded = urldecode( $tag->slug );
					if ( $slug_decoded !== $tag->slug ) {
						$templates[] = $filePath . "/tag-{$slug_decoded}.php";
					}
					$templates[] = $filePath . "/tag-{$tag->slug}.php";
					$templates[] = $filePath . "/tag-{$tag->term_id}.php";
				}
				$templates[] = $filePath . '/tag.php';
				foreach ( $templates as $key => $value ) {
					if ( 'single' === $type && file_exists($value) ) {
						$file = $value;
						break;
					}
				}
	    	break;
	    	case is_author():
	    		$author = get_queried_object();

				$templates = array();

				if ( $author instanceof WP_User ) {
					$templates[] = $filePath . "/author-{$author->user_nicename}.php";
					$templates[] = $filePath . "/author-{$author->ID}.php";
				}
				$templates[] = $filePath . "/author.php";

				foreach ( $templates as $key => $value ) {
					if ( 'single' === $type && file_exists($value) ) {
						$file = $value;
						break;
					}
				}
	    	break;
	    	case (is_archive()):
	    		$post_types = array_filter( (array) get_query_var( 'post_type' ) );
				$templates = array();
				if ( count( $post_types ) == 1 ) {
					$post_type = reset( $post_types );
					$templates[] = $filePath . "/archive-{$post_type}.php";
				}
				$templates[] = $filePath . '/archive.php';
				foreach ( $templates as $key => $value ) {
					if ( 'single' === $type && file_exists($value) ) {
						$file = $value;
						break;
					}
				}
	    	break;
	    	case (is_post_type_archive()):
	    		$post_type = get_query_var( 'post_type' );
				if ( is_array( $post_type ) )
					$post_type = reset( $post_type );

				$obj = get_post_type_object( $post_type );
				if ( ! ($obj instanceof WP_Post_Type) || ! $obj->has_archive ) {
					//return '';
					break;
				}

				$post_types = array_filter( (array) get_query_var( 'post_type' ) );

				$templates = array();

				if ( count( $post_types ) == 1 ) {
					$post_type = reset( $post_types );
					$templates[] = $filePath . "/archive-{$post_type}.php";
				}
				$templates[] = $filePath . '/archive.php';
				foreach ( $templates as $key => $value ) {
					if ( 'single' === $type && file_exists($value) ) {
						$file = $value;
						break;
					}
				}
	    	break;
	    	case is_single(): 
	    		$object = get_queried_object();

				$templates = array();

				if ( ! empty( $object->post_type ) ) {
					$template = get_page_template_slug( $object );
					if ( $template && 0 === validate_file( $template ) ) {
						$templates[] = $filePath.'/'.$template;
					}

					$name_decoded = urldecode( $object->post_name );
					if ( $name_decoded !== $object->post_name ) {
						$templates[] = $filePath . "/single-{$object->post_type}-{$name_decoded}.php";
					}

					$templates[] = $filePath . "/single-{$object->post_type}-{$object->post_name}.php";
					$templates[] = $filePath . "/single-{$object->post_type}.php";
				}

				$templates[] = $filePath . "/single.php";
				
				foreach ( $templates as $key => $value ) {
					if ( 'single' === $type && file_exists($value) ) {
						$file = $value;
						break;
					}
				}
	    	break;
	    	case is_page():
	    		$id = get_queried_object_id();
				$template = get_page_template_slug();
				$pagename = get_query_var('pagename');

				if ( ! $pagename && $id ) {
					// If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object
					$post = get_queried_object();
					if ( $post )
						$pagename = $post->post_name;
				}

				$templates = array();
				if ( $template && 0 === validate_file( $template ) )
					$templates[] = $filePath.'/'.$template;
				if ( $pagename ) {
					$pagename_decoded = urldecode( $pagename );
					if ( $pagename_decoded !== $pagename ) {
						$templates[] = $filePath . "/page-{$pagename_decoded}.php";
					}
					$templates[] = $filePath . "/page-{$pagename}.php";
				}
				if ( $id )
					$templates[] = $filePath . "/page-{$id}.php";
				$templates[] = $filePath . "/page.php";

				foreach ( $templates as $key => $value ) {
					if ( 'single' == $type && file_exists($value) ) {
						$file = $value;
						break;
					}
				}
	    	break;
	    }
	    if(!file_exists($file)){
			$file = $currentFile;
		}
	 	return $file;
	}

	// Custom Footer
	function ampforwp_child_custom_footer_file( $file, $type, $post ) {
		$currentFile = $file;
		if ( 'footer' === $type ) {
			$file = ampforwp_theme_template_directry() . '/footer.php';
		}
		if(!file_exists($file)){
			$file = $currentFile;
		}
		return $file;
	}

add_action("ampforwp_pagebuilder_layout_filter","ampforwp_add_upcomminglayouts");
function ampforwp_add_upcomminglayouts($layoutTemplate){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if(function_exists('ampforwp_upcomming_layouts_demo') && !is_plugin_active('amp-layouts/amp-layouts.php') ){
		$layouts_demo = ampforwp_upcomming_layouts_demo();
		if(is_array($layouts_demo)){
			foreach($layouts_demo as $k=>$val){
				$layoutTemplate[$val['name'].'-upcomming'] =  array(
									'Upcoming'=>array(
											'name'=> $val['name'],
											'preview_demo'=>$val['link'],
											'preview_img'=>$val['image'],
											'layout_json'=>'{"rows":[],"totalrows":"23","totalmodules":"94",}',
												)
										);
			}
		}
	}
		return $layoutTemplate;

}




if(!function_exists('ampforwp_isexternal')){
  function ampforwp_isexternal($url) {
    $components = parse_url($url);
    if ( empty($components['host']) ) return false;  // we will treat url like '/relative.php' as relative
    if ( strcasecmp($components['host'], $_SERVER['HTTP_HOST']) === 0 ) return false; // url host looks exactly like the local host
    return strrpos(strtolower($components['host']), $_SERVER['HTTP_HOST']) !== strlen($components['host']) - strlen($_SERVER['HTTP_HOST']); // check if the url host is a subdomain
  }//Function function_exists
}// ampforwp_isexternal function_exists close
if(!function_exists('ampforwp_findInternalUrl')){
  function ampforwp_findInternalUrl($url){
    global $redux_builder_amp;
    if(isset($redux_builder_amp['convert-internal-nonamplinks-to-amp']) && ! $redux_builder_amp['convert-internal-nonamplinks-to-amp']){
        return $url;
    }
	$get_skip_media_path = array();
	$skip_media_extensions = array();
	$get_skip_media_path = pathinfo($url);
	$skip_media_extensions = array('jpg','jpeg','gif','png');
	if(isset($get_skip_media_path['extension'])){
		if(!in_array($get_skip_media_path['extension'],$skip_media_extensions) && $get_skip_media_path['extension']){
			$skip_media_extensions[] = $get_skip_media_path['extension'];
		}
	}
	$skip_media_extensions = apply_filters( 'ampforwp_internal_links_skip_media', $skip_media_extensions );
	if(isset($get_skip_media_path['extension'])){
		if(in_array($get_skip_media_path['extension'],$skip_media_extensions)){
			return $url;
		}
	}
	if ( false !== strpos($url, '#') && false === ampforwp_is_amp_inURL($url) && !ampforwp_isexternal($url) ) {
		$url_array = explode('#', $url);
		if ( !empty($url_array) && '' !== $url_array[0]) {
      		$url = ampforwp_url_controller($url_array[0]).'#'.$url_array[1];
			return $url;
		}
	}
    if( false === wp_http_validate_url($url)) {
    	return $url; 
    }

    if(!ampforwp_isexternal($url) && ampforwp_is_amp_inURL($url)===false){
      // Skip the URL's that have edit link to it
      $parts = parse_url($url);
      if ( isset( $parts['query'] ) && ! empty( $parts['query'] ) ) {
      parse_str($parts['query'], $query);
	      if ( (isset( $query['action'] ) && $query['action']) || (isset( $query['amp'] ) && $query['amp'] ) ) {
	          return $url;
	      }
      }

      $qmarkAmp = (isset($redux_builder_amp['amp-core-end-point']) ? $redux_builder_amp['amp-core-end-point']: false );//amp-core-end-point
      if ( $qmarkAmp ){
      	$url = add_query_arg( 'amp', '1', $url);
		return $url;
      }
      else{
      	if ( get_option('permalink_structure') ) {
	      	if ( strpos($url, "?") && strpos($url, "=") ){
	      		$url = explode('?', $url);
	      		$url = ampforwp_url_controller($url[0]).'?'.$url[1];
	      	}
	      	else
	      		$url = ampforwp_url_controller($url);
      	}
      	else
      		$url = add_query_arg( 'amp', '1', $url );
      }
      return $url;
    }
    return $url;
  }// function Close
}// function_exists ampforwp_findInternalUrl close
function ampforwp_is_amp_inURL($url){
  $urlArray = explode("/", $url);
  if(!in_array(AMPFORWP_AMP_QUERY_VAR, $urlArray)){
    return false;
  }
  return true;
}

if(is_admin()){
	add_action( 'redux/options/redux_builder_amp/saved', 'ampforwp_extension_individual_amp_page',10,2);
	function ampforwp_extension_individual_amp_page($options, $changed_values){
		if(isset($changed_values['amp-pages-meta-default']) && $options['amp-pages-meta-default']=='hide'){
			delete_transient('ampforwp_current_version_check');
		}
	}

	add_action( 'redux/options/redux_builder_amp/saved', 'ampforwp_app_banner_manifest_create',10,2);
	function ampforwp_app_banner_manifest_create($options, $changed_values){

		$changesExpected = array('ampforwp-amp-app-banner', 'ampforwp-apple-app', 'ampforwp-apple-app-id','ampforwp-apple-app-argument','ampforwp-app-manifest-path','ampforwp-app-banner-image','ampforwp-app-banner-text','ampforwp-app-banner-button-text');
		$changedKeys = array_keys($changed_values);
		if(array_intersect($changesExpected, $changedKeys) && $options['ampforwp-amp-app-banner'] == 1){
			ampforwp_amp_app_banner_manifest_json();
		}
	}

	add_filter("redux/options/redux_builder_amp/data/category_list_hierarchy", 'ampforwp_redux_category_list_hierarchy',10,1);
	function ampforwp_redux_category_list_hierarchy($data){
		if(!is_array($data)){ $data = array(); }// To avoid PHP Fatal error:  Cannot use string offset as an array
		$cats = get_categories();
		if ( ! empty ( $cats ) ) {
	        foreach ( $cats as $cat ) {
	        	if($cat->category_parent!=0){
	        		$data[ $cat->category_parent ]['child'][$cat->term_id] = $cat->name;
	        	}else{
	            	$data[ $cat->term_id ]['name'] = $cat->name;
	        	}
	        }//foreach
	    } // If

	    $data['set_category_hirarchy'] = 1;
		return $data;
	}


}//Is_admin Closed

/**
 * Added filter to Add tags & attribute
 *  sanitizer in all content filters
 */
add_filter("amp_content_sanitizers",'ampforwp_allows_tag_sanitizer');
add_filter("ampforwp_content_sanitizers",'ampforwp_allows_tag_sanitizer');

function ampforwp_allows_tag_sanitizer($sanitizer_classes){
	$sanitizer_classes['AMP_Tag_And_Attribute_Sanitizer'] = array();
	return $sanitizer_classes;
};

// Liberating Search from Relevanssi's Search Take Over
add_action('amp_init', 'ampforwp_remove_relevanssi_search_takeover');
function ampforwp_remove_relevanssi_search_takeover(){
	remove_filter( 'the_posts', 'relevanssi_query', 99, 2 );
	remove_filter( 'posts_request', 'relevanssi_prevent_default_request', 10, 2 );
} 

// Total Plus compatibility #2511
add_action('current_screen', 'ampforwp_totalplus_comp_admin');
function ampforwp_totalplus_comp_admin() {
	$screen = get_current_screen();
	if ( 'toplevel_page_amp_options' == $screen->base ) {
		remove_action('admin_enqueue_scripts', 'total_plus_admin_scripts', 100);
	}
}
// Simple Author Box Compatibility #2268
add_action('amp_post_template_css', 'ampforwp_simple_author_box');
function ampforwp_simple_author_box(){
	if( class_exists('Simple_Author_Box') ){ ?>
		.saboxplugin-wrap .saboxplugin-gravatar amp-img {max-width: 100px;height: auto;}
	<?php }
}
// WP-AppBox CSS #2791
add_action('amp_post_template_css', 'ampforwp_app_box_styles');
function ampforwp_app_box_styles(){
	if ( function_exists('wpAppbox_createAppbox') ) { ?>
		.wpappbox{clear:both;background-color:#F9F9F9;line-height:1.4;color:#545450;margin:16px 0;font-size:15px;border:1px solid #E5E5E5;box-shadow:0 0 8px 1px rgba(0,0,0,.11);border-radius:8px;display:inline-block;width:100%}.wpappbox a{transition:all .3s ease-in-out 0s}.wpappbox.compact .appicon{height:66px;width:68px;float:left;padding:6px;margin-right:15px}.appicon amp-img{max-width:92px;height:92px;border-radius:5%}.wpappbox a:hover amp-img{opacity:.9;filter:alpha(opacity=90);-webkit-filter:grayscale(100%)}.wpappbox .appicon{position:relative;height:112px;width:112px;float:left;padding:10px;background:#FFF;text-align:center;border-right:1px solid #E5E5E5;border-top-left-radius:6px;border-bottom-left-radius:6px;margin-right:10px}.wpappbox .appdetails{margin-top:15px}.wpappbox .appbuttons a{font-size:13px;box-shadow:0 1px 3px 0 rgba(0,0,0,.15);background:#F1F1F1;border-bottom:0;color:#323232;padding:3px 5px;display:inline-block;margin:12px 0 0;border-radius:3px;cursor:pointer;font-weight:400}.wpappbox .appbuttons a:hover{color:#fff;background:#111}.wpappbox div.applinks,div.wpappbox.compact a.applinks{float:right;position:relative;background:#FFF;text-align:center;border-left:1px solid #E5E5E5;border-top-right-radius:6px;border-bottom-right-radius:6px}.wpappbox div.applinks{height:112px;width:92px;display:block}.wpappbox .apptitle,.wpappbox .developer{margin-bottom:15px}.wpappbox .developer a{color:#333}.wpappbox .apptitle a{font-size:18px;font-weight:500;color:#333}.wpappbox .apptitle a:hover,.wpappbox .developer a:hover{color:#5588b5}.wpappbox .appbuttons span,.wpappbox .qrcode{display:none}.wpappbox.screenshots>div.screenshots{width:auto;margin:0 auto;padding:10px;clear:both;border-top:1px solid #E5E5E5}.wpappbox .screenshots .slider>ul>li{padding:0;margin:0 6px 0 0;list-style-type:none;display:inline-block}.wpappbox .screenshots .slider{overflow-x:scroll;overflow-y:hidden;height:320px;margin-top:0}.wpappbox .screenshots .slider>ul{display:inline-flex;width:100%}.wpappbox .screenshots .slider>ul>li amp-img{height:320px;display:inline-block;}
		div.wpappbox div.appbuttons {
		    position: absolute;
		    bottom: 30px;
		    width: 92px;
		}
		<?php $wpappbox_image_path = plugins_url().'/wp-appbox/img/'; ?>
		div.wpappbox:not(.colorful) div.applinks {
			filter: grayscale(100%);
		}
		div.wpappbox .applinks, div.wpappbox div.applinks{
		    background-color: #FFF;
		}
		div.wpappbox.amazonapps a.applinks, div.wpappbox.amazonapps div.applinks {
		    background: url(<?php echo $wpappbox_image_path.'amazonapps.png' ?>);
		    background-repeat: no-repeat;
		    background-size: auto 42px;
		    background-position: center 7px;
		}
		div.wpappbox.appstore a.applinks, div.wpappbox.appstore div.applinks {
			background: url(<?php echo $wpappbox_image_path.'appstore.png' ?>);
		    background-repeat: no-repeat;
		    background-size: auto 42px;
		    background-position: center 7px;
		}
		div.wpappbox.chromewebstore a.applinks, div.wpappbox.chromewebstore div.applinks{
			background: url(<?php echo $wpappbox_image_path.'chromewebstore.png' ?>);
		    background-repeat: no-repeat;
		    background-size: auto 42px;
		    background-position: center 7px;
		}
		div.wpappbox.firefoxaddon a.applinks, div.wpappbox.firefoxaddon div.applinks{
			background: url(<?php echo $wpappbox_image_path.'firefoxaddon.png' ?>);
		    background-repeat: no-repeat;
		    background-size: auto 42px;
		    background-position: center 7px;
		}
		div.wpappbox.googleplay a.applinks, div.wpappbox.googleplay div.applinks{
			background: url(<?php echo $wpappbox_image_path.'googleplay.png' ?>);
		    background-repeat: no-repeat;
		    background-size: auto 42px;
		    background-position: center 7px;
		}	
		div.wpappbox.operaaddons a.applinks, div.wpappbox.operaaddons div.applinks{
			background: url(<?php echo $wpappbox_image_path.'operaaddons.png' ?>);
		    background-repeat: no-repeat;
		    background-size: auto 42px;
		    background-position: center 7px;
		}
		div.wpappbox.steam a.applinks, div.wpappbox.steam div.applinks{
			background: url(<?php echo $wpappbox_image_path.'steam.png' ?>);
		    background-repeat: no-repeat;
		    background-size: auto 42px;
		    background-position: center 7px;
		}
		div.wpappbox.windowsstore a.applinks, div.wpappbox.windowsstore div.applinks{
			background: url(<?php echo $wpappbox_image_path.'windowsstore.png' ?>);
		    background-repeat: no-repeat;
		    background-size: auto 42px;
		    background-position: center 7px;
		}
		div.wpappbox.wordpress a.applinks, div.wpappbox.wordpress div.applinks{
			background: url(<?php echo $wpappbox_image_path.'wordpress.png' ?>);
		    background-repeat: no-repeat;
		    background-size: auto 42px;
		    background-position: center 7px;
		}
		div.wpappbox.xda a.applinks, div.wpappbox.xda div.applinks{
			background: url(<?php echo $wpappbox_image_path.'xda.png' ?>);
		    background-repeat: no-repeat;
		    background-size: auto 42px;
		    background-position: center 7px;
		}
		div.wpappbox div.stars-monochrome {
		    background: url(<?php echo $wpappbox_image_path.'stars-sprites-monochrome.png' ?>) no-repeat;
		}
		div.wpappbox div.rating-stars {
		    width: 65px;
		    height: 13px;
		    margin-left: 5px;
		    margin-top: 4px;
		    display: inline-block;
		}
		div.wpappbox div.stars50 {
		    background-position: 0 -130px;
		}
		div.wpappbox div.stars45 {
		    background-position: 0 -117px;
		}
		div.wpappbox div.stars40 {
		    background-position: 0 -104px;
		}
		div.wpappbox div.stars35 {
	    	background-position: 0 -91px;
		}	
		div.wpappbox div.stars30 {
		    background-position: 0 -78px;
		}
		div.wpappbox div.stars25 {
		    background-position: 0px -65px;
		}
		div.wpappbox div.stars20 {
		    background-position: 0px -52px;
		}
		div.wpappbox div.stars15 {
		    background-position: 0px -39px;
		}
		div.wpappbox div.stars10 {
		    background-position: 0px -26px;
		}
		div.wpappbox div.stars5 {
		    background-position: 0px -12px;
		}
		div.wpappbox div.stars0 {
		    background-position: 0px -0px;
		}

		@media(max-width:500px){.appicon amp-img{max-width:70px;height:70px}.wpappbox .appicon{height:90px;width:90px;display:inline-block;vertical-align:middle;}.wpappbox .apptitle a{font-size:14px}.wpappbox{font-size:13px;text-align:center;padding:10px 0}.wpappbox .apptitle,.wpappbox .developer{margin-bottom:6px}.wpappbox .appdetails{text-align:left;padding-left:10px}.wpappbox .screenshots .slider{height:290px}.wpappbox .screenshots .slider>ul>li amp-img{max-width:160px;height:280px}
		.wpappbox div.applinks{display:none;}
		}
	<?php 
	} // ampforwp_app_box_styles Function Ends 
}

//  Compatibility with the footnotes plugin. #2447
add_action('amp_post_template_css','ampforwp_footnote_support');
if ( ! function_exists('ampforwp_footnote_support') ) {
	function ampforwp_footnote_support(){
		if(class_exists('MCI_Footnotes')){?>
			.footnote_tooltip {display: none;}
		<?php }
	}
}

// yoast author twitter handle #2133
if ( ! function_exists('ampforwp_yoast_twitter_handle') ) {
	function ampforwp_yoast_twitter_handle() {
		$twitter = '';
		if (  class_exists('WPSEO_Frontend') ) {
		    global $post;
		    $twitter = get_the_author_meta( 'twitter', $post->post_author );
		}
		if($twitter){
		    return ' <span><a href="https://twitter.com/'.esc_attr($twitter).'" target="_blank">@'.esc_html($twitter).'</a></span>';
		}
		return '';
	}
}

add_action( 'activated_plugin', 'ampforwp_active_update_transient' );
function ampforwp_active_update_transient($plugin){
	delete_transient( 'ampforwp_themeframework_active_plugins' ); 
}

add_action( 'deactivated_plugin', 'ampforwp_deactivate_update_transient' );
function ampforwp_deactivate_update_transient($plugin){

	delete_transient( 'ampforwp_themeframework_active_plugins' ); 
	$check_plugin  = strpos($plugin, ampforwp_get_setting('amp-design-selector'));

	if ( false !== $check_plugin ) {
		$selectedOption = get_option('redux_builder_amp',true);		
		$selectedOption['amp-design-selector'] = 4;
		update_option('redux_builder_amp',$selectedOption);
	}
}
//Remove CSS header from the GoodLife Theme #2673
add_action('pre_amp_render_post','ampforwp_goodlife_css');
function ampforwp_goodlife_css(){
	remove_filter('amp_post_template_file', 'thb_custom_amp_templates');
	remove_action( 'amp_post_template_css', 'thb_amp_additional_css_styles' );
	/**
	* toc 
	**/
	if(class_exists('toc')){
		global $tic;
	   	remove_filter( 'the_content', array($tic, 'the_content'), 100 );
	   	add_filter('the_content', 'ampforwp_show_hide_toc');
	}
}


function ampforwp_levelup_compatibility($type='levelup_theme_and_elementor_check'){
	//check theme
	$returnVal = false;
	switch($type){
		case 'levelup_theme':
			if(function_exists('levelup_theme_is_active')){
				$returnVal = levelup_theme_is_active();
			}
		break;
		case 'levelup_elementor':
			if(function_exists('levelup_has_enable_elementor_builder')){
				$returnVal = levelup_has_enable_elementor_builder();				
			}
		break;
		case 'levelup_theme_and_elementor':
			if(function_exists('levelup_theme_is_active') && function_exists('levelup_has_enable_elementor_builder')){
				$returnVal = levelup_theme_is_active() && levelup_has_enable_elementor_builder();
			}
		break;
		case 'hf_builder_foot':
			if(function_exists('levelup_check_hf_builder')){
				$returnVal = levelup_check_hf_builder('foot');
			}
		break;
		case 'hf_builder_head':
			if(function_exists('levelup_check_hf_builder')){
				$returnVal = levelup_check_hf_builder('head');
			}
		break;
	}
	return $returnVal;
}

/**
* toc 
*/
function ampforwp_show_hide_toc($content){
   global $tic, $post;
	$items = $css_classes = $anchor = '';
	$custom_toc_position = strpos($content, '<!--TOC-->');
	$find = $replace = array();
	if ( $tic->is_eligible($custom_toc_position) ) {
		$items = $tic->extract_headings($find, $replace, $content);
		$options = $tic->get_options();

		if ( $items ) {
			// do we display the toc within the content or has the user opted
			// to only show it in the widget?  if so, then we still need to 
			// make the find/replace call to insert the anchors
			if ( $options['show_toc_in_widget_only'] && (in_array(get_post_type(), $options['show_toc_in_widget_only_post_types'])) ) {
				$content = $tic->mb_find_replace($find, $replace, $content);
			}
			else {
				// wrapping css classes
				switch( $options['wrapping'] ) {
					case TOC_WRAPPING_LEFT:
						$css_classes .= ' toc_wrap_left';
						break;
						
					case TOC_WRAPPING_RIGHT:
						$css_classes .= ' toc_wrap_right';
						break;

					case TOC_WRAPPING_NONE:
					default:
						// do nothing
				}
				
				// colour themes
				switch ( $options['theme'] ) {
					case TOC_THEME_LIGHT_BLUE:
						$css_classes .= ' toc_light_blue';
						break;
					
					case TOC_THEME_WHITE:
						$css_classes .= ' toc_white';
						break;
						
					case TOC_THEME_BLACK:
						$css_classes .= ' toc_black';
						break;
					
					case TOC_THEME_TRANSPARENT:
						$css_classes .= ' toc_transparent';
						break;
				
					case TOC_THEME_GREY:
					default:
						// do nothing
				}
				
				// bullets?
				if ( $options['bullet_spacing'] )
					$css_classes .= ' have_bullets';
				else
					$css_classes .= ' no_bullets';
				
				if ( $options['css_container_class'] ) $css_classes .= ' ' . $options['css_container_class'];

				$css_classes = trim($css_classes);
				
				// an empty class="" is invalid markup!
				if ( !$css_classes ) $css_classes = ' ';
				
				// add container, toc title and list items
				$html = '<amp-accordion class="sample ' . $css_classes . '"><section expanded>';
				if ( $options['show_heading_text'] ) {
					$toc_title = $options['heading_text'];
					if ( strpos($toc_title, '%PAGE_TITLE%') !== false ) $toc_title = str_replace( '%PAGE_TITLE%', get_the_title(), $toc_title );
					if ( strpos($toc_title, '%PAGE_NAME%') !== false ) $toc_title = str_replace( '%PAGE_NAME%', get_the_title(), $toc_title );
					$html .= '<p class="toc_title">' . htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' ) . '</p>';
				}
				$html .= '<ul class="toc_list">' . $items . '</ul></section></amp-accordion>' . "\n";
				
				if ( $custom_toc_position !== false ) {
					$find[] = '<!--TOC-->';
					$replace[] = $html;
					$content = ampforwp_toc_replace_content($find, $replace, $content);
				}
				else {	
					if ( count($find) > 0 ) {
						switch ( $options['position'] ) {
							case TOC_POSITION_TOP:
								$content = $html . ampforwp_toc_replace_content($find, $replace, $content);
								break;
							
							case TOC_POSITION_BOTTOM:
								$content = ampforwp_toc_replace_content($find, $replace, $content) . $html;
								break;
							
							case TOC_POSITION_AFTER_FIRST_HEADING:
								$replace[0] = $replace[0] . $html;
								$content = ampforwp_toc_replace_content($find, $replace, $content);
								break;
						
							case TOC_POSITION_BEFORE_FIRST_HEADING:
							default:
								$replace[0] = $html . $replace[0];
								$content = ampforwp_toc_replace_content($find, $replace, $content);
						}
					}
				}
			}
		}
	}
	else {
		// remove <!--TOC--> (inserted from shortcode) from content
		$content = str_replace('<!--TOC-->', '', $content);
	}
   return $content;
}



function ampforwp_toc_replace_content( &$find = false, &$replace = false, &$string = '' ){
	if ( is_array($find) && is_array($replace) && $string ) {
		// check if multibyte strings are supported
		if ( function_exists( 'content' ) ) {
			for ($i = 0; $i < count($find); $i++) {
				$string = 
					mb_substr( $string, 0, mb_strpos($string, $find[$i]) ) .	// everything befor $find
					$replace[$i] .												// its replacement
					mb_substr( $string, mb_strpos($string, $find[$i]) + mb_strlen($find[$i]) )	// everything after $find
				;
			}
		}
		else {
			for ($i = 0; $i < count($find); $i++) {
				$string = substr_replace(
					$string,
					$replace[$i],
					strpos($string, $find[$i]),
					strlen($find[$i])
				);
			}
		}
	}

return $string;
}

// Viewport appear more than once in Zox news theme. #2971
add_action('pre_amp_render_post','ampforwp_zox_news_viewport');
function ampforwp_zox_news_viewport(){
	if ( function_exists( 'mvp_setup' ) && ampforwp_get_setting('amp-design-selector') != 4 ) {
		remove_action( 'amp_post_template_head','ampforwp_add_meta_viewport');
	}
}
// SEOPress Compatibility #1589
add_action('amp_post_template_head', 'ampforwp_seopress_social');
function ampforwp_seopress_social(){
	$options = $facebook = $twitter = $advanced_options = $seopress_social_og_title = $seopress_social_og_desc = '';
	$post_id = ampforwp_get_the_ID();
	$options = get_option("seopress_social_option_name");
	$advanced_options = get_option("seopress_advanced_option_name");
	if ( !empty($options) ) {
		if (isset($options['seopress_social_facebook_og'])) {
			global $wp;
			if (isset($advanced_options['seopress_advanced_advanced_trailingslash']) ) {
				$current_url = home_url(add_query_arg(array(), $wp->request));
			} else {
				$current_url = trailingslashit(home_url(add_query_arg(array(), $wp->request)));
			}
			if (is_search()) {
				$seopress_social_og_url = '<meta property="og:url" content="'.esc_url(htmlspecialchars(urldecode(get_home_url().'/search/'.get_search_query()))).'" />';
			} else {
				$seopress_social_og_url = '<meta property="og:url" content="'.esc_url(htmlspecialchars(urldecode($current_url),ENT_COMPAT, 'UTF-8')).'" />';
			}
			//Hook on post OG URL - 'seopress_social_og_url'
			if (has_filter('seopress_social_og_url')) {
				$seopress_social_og_url = apply_filters('seopress_social_og_url', $seopress_social_og_url);
		    }			
			echo $seopress_social_og_url."\n";
		}
		if (isset($options['seopress_social_facebook_og'])) {
			$seopress_social_og_site_name = '<meta property="og:site_name" content="'.esc_html(get_bloginfo('name')).'" />';
			//Hook on post OG site name - 'seopress_social_og_site_name'
			if (has_filter('seopress_social_og_site_name')) {
				$seopress_social_og_site_name = apply_filters('seopress_social_og_site_name', $seopress_social_og_site_name);
		    }
			echo $seopress_social_og_site_name."\n";
		}
		if (isset($options['seopress_social_facebook_og'])) {
			$seopress_social_og_locale = '<meta property="og:locale" content="'.esc_attr(get_locale()).'" />';
			//Polylang
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( is_plugin_active( 'polylang/polylang.php' ) || is_plugin_active( 'polylang-pro/polylang.php' )) {
				//@credits Polylang
				if (did_action('pll_init') && function_exists('PLL')) {
					$alternates = array();

					foreach ( PLL()->model->get_languages_list() as $language ) {
						if ( PLL()->curlang->slug !== $language->slug && PLL()->links->get_translation_url( $language ) && isset( $language->facebook ) ) {
							$alternates[] = $language->facebook;
						}
					}
					// There is a risk that 2 languages have the same Facebook locale. So let's make sure to output each locale only once.
					$alternates = array_unique( $alternates );

					foreach ( $alternates as $lang ) {
						$seopress_social_og_locale .= "\n";
						$seopress_social_og_locale .= '<meta property="og:locale:alternate" content="'.esc_attr($lang).'" />';
					}
				}
			}
			//Hook on post OG locale - 'seopress_social_og_locale'
			if (has_filter('seopress_social_og_locale')) {
				$seopress_social_og_locale = apply_filters('seopress_social_og_locale', $seopress_social_og_locale);
		    }
			if (isset($seopress_social_og_locale) && $seopress_social_og_locale !='') {
				echo $seopress_social_og_locale."\n";
			}
		}
		if (isset($options['seopress_social_facebook_og'])) {
			if (is_home() || is_front_page()) {
				$seopress_social_og_type = '<meta property="og:type" content="website" />';
			} elseif (is_singular('product') || is_singular('download')) {
				$seopress_social_og_type = '<meta property="og:type" content="product" />';
			} elseif (is_singular()) {
				global $post;
				$seopress_video_disabled     	= get_post_meta($post->ID,'_seopress_video_disabled', true);
			  	$seopress_video     			= get_post_meta($post->ID,'_seopress_video');

			  	if (!empty($seopress_video[0][0]['url']) && $seopress_video_disabled =='') {
					$seopress_social_og_type = '<meta property="og:type" content="video.other" />';
			  	} else {
			  		$seopress_social_og_type = '<meta property="og:type" content="article" />';
			  	}
			} 
			elseif (is_search() || is_archive() || is_404()) {
				$seopress_social_og_type = '<meta property="og:type" content="object" />';
			}
			if (isset($seopress_social_og_type)) {
				//Hook on post OG type - 'seopress_social_og_type'
				if (has_filter('seopress_social_og_type')) {
					$seopress_social_og_type = apply_filters('seopress_social_og_type', $seopress_social_og_type);
			    }
				echo $seopress_social_og_type."\n";
			}
		}
		if ( isset($options['seopress_social_facebook_og']) && ( isset($options['seopress_social_accounts_facebook']) && '' != $options['seopress_social_accounts_facebook'] ) ) {
			if (is_singular() && !is_home() && !is_front_page()) {
				global $post;
				$seopress_video_disabled     	= get_post_meta($post->ID,'_seopress_video_disabled', true);
			  	$seopress_video     			= get_post_meta($post->ID,'_seopress_video');

			  	if (!empty($seopress_video[0][0]['url']) && $seopress_video_disabled =='') {		
					//do nothing
				} else {
					$seopress_social_og_author = '<meta property="article:author" content="'.seopress_social_accounts_facebook_option().'" />';
					$seopress_social_og_author .= "\n";
					$seopress_social_og_author .= '<meta property="article:publisher" content="'.seopress_social_accounts_facebook_option().'" />';
				}
			}
			if (isset($seopress_social_og_author)) {
				//Hook on post OG author - 'seopress_social_og_author'
				if (has_filter('seopress_social_og_author')) {
					$seopress_social_og_author = apply_filters('seopress_social_og_author', $seopress_social_og_author);
			    }
				echo $seopress_social_og_author."\n";
			}
		}
		if (isset($options['seopress_social_facebook_og'])) {
			$title = '';
			$title = ampforwp_get_seopress_title();
			if ( is_home() && '' != get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_fb_title',true) ){
				$title = get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_fb_title',true);
			}
			if ((is_tax() || is_category() || is_tag()) && '' != get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_title',true) )  {
				$title = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_title',true);
			}
			if ( '' != get_post_meta($post_id,'_seopress_social_fb_title',true) ){
				$title = get_post_meta($post_id,'_seopress_social_fb_title',true);
			}
			if ( '' == $title && '' != get_the_title() ){
				$title = get_the_title();
			}
			$seopress_social_og_title .= '<meta property="og:title" content="'.esc_attr($title).'" />'; 
		 	$seopress_social_og_title .= "\n";
		 	//Hook on post OG title - 'seopress_social_og_title'
			if (has_filter('seopress_social_og_title')) {
				$seopress_social_og_title = apply_filters('seopress_social_og_title', $seopress_social_og_title);
		    }
		    if (isset($seopress_social_og_title) && $seopress_social_og_title !='') {
		    	echo $seopress_social_og_title;
		    }
		}

		if (isset($options['seopress_social_facebook_og'])) {
			$description = ampforwp_generate_meta_desc();
			if ( is_home() && '' != get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_fb_desc',true) ) {
				$description = get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_fb_desc',true);
			}
			if (is_tax() || is_category() || is_tag() && '' != get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_desc',true) ) {
				$description = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_desc',true);
			}
			if ( '' != get_post_meta($post_id,'_seopress_social_fb_desc',true) ) {
				$description = get_post_meta($post_id,'_seopress_social_fb_desc',true);
			}
			$seopress_social_og_desc .= '<meta property="og:description" content="'.$description.'" />';
			$seopress_social_og_desc .= "\n";
			//Hook on post OG description - 'seopress_social_og_desc'
			if (has_filter('seopress_social_og_desc')) {
				$seopress_social_og_desc = apply_filters('seopress_social_og_desc', $seopress_social_og_desc);
		    }
		    if (isset($seopress_social_og_desc) && $seopress_social_og_desc !='') {
		    	echo $seopress_social_og_desc;
			}
		}
		if (isset($options['seopress_social_facebook_og'])) {
			$url = '';
			if ( ampforwp_is_home() && '' != get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_fb_img',true) ){
				$url = get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_fb_img',true);
			}
			if (is_tax() || is_category() || is_tag() && '' != get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_img',true) ) {
				$url = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_img',true);
			}
			if ( '' != get_post_meta(ampforwp_get_the_ID(),'_seopress_social_fb_img',true) ) {
				$url = get_post_meta(ampforwp_get_the_ID(),'_seopress_social_fb_img',true);
			}
			if ( '' == $url && has_post_thumbnail() ) {
				$url = get_the_post_thumbnail_url();
			}
			if (function_exists('attachment_url_to_postid')) {
				$image_id = attachment_url_to_postid( $url );
				if ( !$image_id ){
					return;
				}

				$image_src = wp_get_attachment_image_src( $image_id, 'full' );

				//OG:IMAGE
				$seopress_social_og_img = '';
				$seopress_social_og_img .= '<meta property="og:image" content="'.esc_url($url).'" />';
				$seopress_social_og_img .= "\n";

				//OG:IMAGE:SECURE_URL IF SSL
				if (is_ssl()) {
					$seopress_social_og_img .= '<meta property="og:image:secure_url" content="'.esc_url($url).'" />';
					$seopress_social_og_img .= "\n";
				}

				//OG:IMAGE:WIDTH + OG:IMAGE:HEIGHT
				if (!empty($image_src)) {
					$seopress_social_og_img .= '<meta property="og:image:width" content="'.$image_src[1].'" />';
					$seopress_social_og_img .= "\n";
					$seopress_social_og_img .= '<meta property="og:image:height" content="'.$image_src[2].'" />';
					$seopress_social_og_img .= "\n";
				}

				//OG:IMAGE:ALT
				if (get_post_meta($image_id, '_wp_attachment_image_alt', true) !='') {
					$seopress_social_og_img .= '<meta property="og:image:alt" content="'.esc_attr(get_post_meta($image_id, '_wp_attachment_image_alt', true)).'" />';
					$seopress_social_og_img .= "\n";
				}
				//Hook on post OG thumbnail - 'seopress_social_og_thumb'
				if (has_filter('seopress_social_og_thumb')) {
					$seopress_social_og_img = apply_filters('seopress_social_og_thumb', $seopress_social_og_img);
			    }
			    if (isset($seopress_social_og_img) && $seopress_social_og_img !='') {
		    		echo $seopress_social_og_img;
			    }
			}
		}
		if (isset($options['seopress_social_facebook_og']) && isset($options['seopress_social_facebook_link_ownership_id'])) {
			$seopress_social_link_ownership_id = '<meta property="fb:pages" content="'.$options['seopress_social_facebook_link_ownership_id'].'" />';	
			echo $seopress_social_link_ownership_id."\n";
		}
		if (isset($options['seopress_social_facebook_og']) && isset($options['seopress_social_facebook_link_ownership_id']) ) {
			$seopress_social_admin_id = '<meta property="fb:admins" content="'.$options['seopress_social_facebook_admin_id'].'" />';		
			echo $seopress_social_admin_id."\n";
		}
		if (isset($options['seopress_social_facebook_og']) && isset($options['seopress_social_facebook_link_ownership_id']) ) {
			$seopress_social_app_id = '<meta property="fb:app_id" content="'.$options['seopress_social_facebook_app_id'].'" />';		
			echo $seopress_social_app_id."\n";
		}
		if (isset($options['seopress_social_twitter_card'])) {
			if ($options['seopress_social_twitter_card_img_size'] =='large') {
				$seopress_social_twitter_card_summary = '<meta name="twitter:card" content="summary_large_image">';
			} else {
				$seopress_social_twitter_card_summary = '<meta name="twitter:card" content="summary" />';
			}
			//Hook on post Twitter card summary - 'seopress_social_twitter_card_summary'
			if (has_filter('seopress_social_twitter_card_summary')) {
				$seopress_social_twitter_card_summary = apply_filters('seopress_social_twitter_card_summary', $seopress_social_twitter_card_summary);
		    }
			echo $seopress_social_twitter_card_summary."\n";
		}
		if (isset($options['seopress_social_twitter_card']) && isset($options['seopress_social_accounts_twitter']) ) {
			$seopress_social_twitter_card_site = '<meta name="twitter:site" content="'.$options['seopress_social_accounts_twitter'].'" />';	
			//Hook on post Twitter card site - 'seopress_social_twitter_card_site'
			if (has_filter('seopress_social_twitter_card_site')) {
				$seopress_social_twitter_card_site = apply_filters('seopress_social_twitter_card_site', $seopress_social_twitter_card_site);
		    }
			echo $seopress_social_twitter_card_site."\n";
		}
		if (isset($options['seopress_social_twitter_card'])) {
			//Init
			$seopress_social_twitter_card_creator ='';
			if ($options['seopress_social_twitter_card'] =='1' && get_the_author_meta('twitter') ) {

				$seopress_social_twitter_card_creator .= '<meta name="twitter:creator" content="@'.get_the_author_meta('twitter').'" />';

			} elseif ($options['seopress_social_twitter_card'] =='1' && $options['seopress_social_accounts_twitter'] !='' ) {
				$seopress_social_twitter_card_creator .= '<meta name="twitter:creator" content="'.$options['seopress_social_accounts_twitter'].'" />';
			}
			//Hook on post Twitter card creator - 'seopress_social_twitter_card_creator'
			if (has_filter('seopress_social_twitter_card_creator')) {
				$seopress_social_twitter_card_creator = apply_filters('seopress_social_twitter_card_creator', $seopress_social_twitter_card_creator);
		    }
		    if (isset($seopress_social_twitter_card_creator) && $seopress_social_twitter_card_creator !='') {
		    	echo $seopress_social_twitter_card_creator."\n";
			}
		}
		if (isset($options['seopress_social_twitter_card'])) {
			$title = '';
			$title = ampforwp_get_seopress_title();
			if ( is_home() && '' != get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_twitter_title',true) ){
				$title = get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_twitter_title',true);
			}elseif ( is_home() && '' != get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_fb_title',true) ){
				$title = get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_fb_title',true);
			}
			if ((is_tax() || is_category() || is_tag()) && '' != get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_twitter_title',true) )  {
				$title = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_twitter_title',true);
			}elseif ((is_tax() || is_category() || is_tag()) && '' != get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_title',true) )  {
				$title = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_title',true);
			}
			if ( '' != get_post_meta(ampforwp_get_the_ID(),'_seopress_social_twitter_title',true) ){
				$title = get_post_meta(ampforwp_get_the_ID(),'_seopress_social_twitter_title',true);
			}elseif ( '' != get_post_meta($post_id,'_seopress_social_fb_title',true) ){
				$title = get_post_meta($post_id,'_seopress_social_fb_title',true);
			}
			if ( '' == $title && '' != get_the_title() ){
				$title = get_the_title();
			}
			$seopress_social_twitter_card_title .= '<meta name="twitter:title" content="'.esc_attr($title).'" />';
			//Hook on post Twitter card title - 'seopress_social_twitter_card_title'
			if (has_filter('seopress_social_twitter_card_title')) {
				$seopress_social_twitter_card_title = apply_filters('seopress_social_twitter_card_title', $seopress_social_twitter_card_title);
		    }
		    if (isset($seopress_social_twitter_card_title) && $seopress_social_twitter_card_title !='') {
		    	echo $seopress_social_twitter_card_title."\n";
		    }
		}
		if (isset($options['seopress_social_twitter_card'])) {
			$description = ampforwp_generate_meta_desc();
			if ( is_home() && '' != get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_twitter_desc',true) ) {
				$description = get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_twitter_desc',true);
			}elseif ( is_home() && '' != get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_fb_desc',true) ) {
				$description = get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_fb_desc',true);
			}
			if (is_tax() || is_category() || is_tag() && '' != get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_twitter_desc',true) ) {
				$description = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_twitter_desc',true);
			}elseif (is_tax() || is_category() || is_tag() && '' != get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_desc',true) ) {
				$description = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_desc',true);
			}

			if ( '' != get_post_meta(ampforwp_get_the_ID(),'_seopress_social_twitter_desc',true) ) {
				$description = get_post_meta(ampforwp_get_the_ID(),'_seopress_social_twitter_desc',true);
			}elseif ( '' != get_post_meta(ampforwp_get_the_ID(),'_seopress_social_fb_desc',true) ) {
				$description = get_post_meta(ampforwp_get_the_ID(),'_seopress_social_fb_desc',true);
			}
			$seopress_social_twitter_card_desc .= '<meta name="twitter:description" content="'.$description.'" />';
			//Hook on post Twitter card description - 'seopress_social_twitter_card_desc'
			if (has_filter('seopress_social_twitter_card_desc')) {
				$seopress_social_twitter_card_desc = apply_filters('seopress_social_twitter_card_desc', $seopress_social_twitter_card_desc);
		    }
		    if (isset($seopress_social_twitter_card_desc) && $seopress_social_twitter_card_desc !='') {
		    	echo $seopress_social_twitter_card_desc."\n";
		    }
		}
		if (isset($options['seopress_social_twitter_card'])) {
			$url = '';
			if ( ampforwp_is_home() && '' != get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_twitter_img',true) ){
				$url = get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_twitter_img',true);
			}elseif ( ampforwp_is_home() && '' != get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_fb_img',true) ){
				$url = get_post_meta(get_option( 'page_for_posts' ),'_seopress_social_fb_img',true);
			}
			if (is_tax() || is_category() || is_tag() && '' != get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_twitter_img',true) ) {
				$url = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_twitter_img',true);
			}elseif (is_tax() || is_category() || is_tag() && '' != get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_img',true) ) {
				$url = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_img',true);
			}
			if ( '' != get_post_meta(ampforwp_get_the_ID(),'_seopress_social_twitter_img',true) ) {
				$url = get_post_meta(ampforwp_get_the_ID(),'_seopress_social_twitter_img',true);
			}elseif ( '' != get_post_meta(ampforwp_get_the_ID(),'_seopress_social_fb_img',true) ) {
				$url = get_post_meta(ampforwp_get_the_ID(),'_seopress_social_fb_img',true);
			}
			if ( '' == $url && has_post_thumbnail() ) {
				$url = get_the_post_thumbnail_url();
			}
			if (function_exists('attachment_url_to_postid')) {
				$image_id = attachment_url_to_postid( $url );
				if ( !$image_id ){
					return;
				}

				$image_src = wp_get_attachment_image_src( $image_id, 'full' );

				//OG:IMAGE
				$seopress_twitter_img = '';
				$seopress_twitter_img .= '<meta property="twitter:image" content="'.$url.'" />';
				$seopress_twitter_img .= "\n";

				//OG:IMAGE:SECURE_URL IF SSL
				if (is_ssl()) {
					$seopress_twitter_img .= '<meta property="twitter:image:secure_url" content="'.$url.'" />';
					$seopress_twitter_img .= "\n";
				}

				//OG:IMAGE:WIDTH + OG:IMAGE:HEIGHT
				if (!empty($image_src)) {
					$seopress_twitter_img .= '<meta property="twitter:image:width" content="'.$image_src[1].'" />';
					$seopress_twitter_img .= "\n";
					$seopress_twitter_img .= '<meta property="twitter:image:height" content="'.$image_src[2].'" />';
					$seopress_twitter_img .= "\n";
				}

				//OG:IMAGE:ALT
				if (get_post_meta($image_id, '_wp_attachment_image_alt', true) !='') {
					$seopress_twitter_img .= '<meta property="twitter:image:alt" content="'.esc_attr(get_post_meta($image_id, '_wp_attachment_image_alt', true)).'" />';
					$seopress_twitter_img .= "\n";
				}

				//Hook on post OG thumbnail - 'seopress_social_og_thumb'
				if (has_filter('seopress_social_og_thumb')) {
					$seopress_twitter_img = apply_filters('seopress_social_og_thumb', $seopress_twitter_img);
			    }
			    if (isset($seopress_twitter_img) && $seopress_twitter_img !='') {
			    	echo $seopress_twitter_img;
			    }
			}
		}
	}
}
//Menu css is not loading when directory plus theme is active. #2963
add_filter('ait-theme-configuration', 'ampforwp_directory_theme_menu'); 
function ampforwp_directory_theme_menu($configuration){ 
if( function_exists('ampforwp_is_amp_endpoint') && class_exists('AitTheme')){
		 unset($configuration['ait-theme-support'][1]); 
	} 
	return $configuration; 
}