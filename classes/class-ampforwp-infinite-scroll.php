<?php
/**
* Class: To enable Infinite Scroll in AMP
* Note: For performance reasons the component will render a maximum of three documents (total) on screen at one time. This limit may be changed or removed in the future.
* Read more about it here: https://www.ampproject.org/docs/reference/components/amp-next-page 
*/
if( ! class_exists('AMPforWP_Infinite_Scroll') ) {

	class AMPforWP_Infinite_Scroll
	{
		private $paged;
		private $is_single = false;
		private $is_loop = false;
		function __construct()
		{
			$this->is_single = true == $this->is_single() ? $this->is_single() : $this->is_single;
			$this->is_loop = true == $this->is_loop() ? $this->is_loop() : $this->is_loop;
			$this->paged = $this->paged();
			if ( $this->is_single ){
				// amp-next-page experiment meta tag
				add_action('amp_experiment_meta', array( $this, 'amp_experiment_meta') );
				// amp-next-page script
				add_filter('amp_post_template_data', array( $this , 'amp_infinite_scroll_script') );
				// amp-next-page tag
				if ( 4 != ampforwp_get_setting('amp-design-selector') ) 
					add_action('ampforwp_above_related_post', array( $this , 'amp_next_page') );
				else 
					add_action('ampforwp_single_design_type_handle', array( $this , 'amp_next_page') );
			}
			if ( $this->is_loop ) {
				// amp-next-page experiment meta tag
				add_action('amp_experiment_meta', array( $this, 'amp_experiment_meta') );
				// amp-next-page script
				add_filter('amp_post_template_data', array( $this , 'amp_infinite_scroll_script') );				
				// amp-next-page tag
				add_action('ampforwp_loop_before_pagination', array( $this , 'amp_next_page') );
				// Next Posts Link
				add_filter('ampforwp_next_posts_link', array( $this , 'next_posts_link') , 10 , 2 );
			}
		}
		public function is_single() {
			if ( is_single() && true == ampforwp_get_setting('ampforwp-infinite-scroll-single') ) {
				return true;
			}
			return false;
		}
		public function is_loop() {
			if ( (is_home() || is_archive()) && true == ampforwp_get_setting('ampforwp-infinite-scroll-home') ) {
				return true;
			}
			return false;
		} 
		public function paged() {
			if ( get_query_var( 'paged' ) ) {
			    return get_query_var('paged');
			} elseif ( get_query_var( 'page' ) ) {
			    return get_query_var('page');
			} else {
			    return 1;
			}
		}
		public function amp_experiment_meta() {
			echo '<meta name="amp-experiments-opt-in" content="amp-next-page">';
		}

		public function amp_infinite_scroll_script( $data ) {
			if ( empty( $data['amp_component_scripts']['amp-next-page'] ) ) {
				$data['amp_component_scripts']['amp-next-page'] = 'https://cdn.ampproject.org/v0/amp-next-page-0.1.js';
			}
			return $data;
		}

		public function amp_next_page() { 
			$loop_link = $first_url = $first_title = $first_image = $second_url = $second_image = $second_title ='';
			$single_links = $single_titles = $single_images = $classes = array();
			if ( $this->is_loop ) {
				$loop_link = $this->loop_link();
				$first_url = $loop_link.($this->paged+1);
				$second_url = $loop_link.($this->paged+2);
			}
			if ( $this->is_single ) {
				// Urls				
				$single_links 	= $this->single_post();				
				$first_url = $single_links[0];
				$second_url = $single_links[1];
				// Titles
				$single_titles 	= $this->single_post('title');				
				$first_title = $single_titles[0];
				$second_title = $single_titles[1];
				// Images
				$single_images 	= $this->single_post('image');
				$first_image = $single_images[0];
				$second_image = $single_images[1];
			}
			$classes = $this->hide();
			?>
			<amp-next-page>
			  	<script type="application/json">
			    {
			      	"pages": [{
				          "title": 	"<?=$first_title?>",
				          "image": 	"<?=$first_image?>",
				          "ampUrl": "<?=$first_url?>"
				        },
				        {
				          "title": 	"<?=$second_title?>",
				          "image": 	"<?=$second_image?>",
				          "ampUrl": "<?=$second_url?>"
				        }
				    ],
				    "hideSelectors": <?=$classes?>
		    	}
			  	</script>
			</amp-next-page>
		<?php }
		public function single_post($type = 'url') {

			global $post;
			$urls = array();
			$titles = array();
			$images = array();
			$exclude_ids = get_option('ampforwp_exclude_post');
			$exclude_ids[] = $post->ID;
			$query_args =  array(
				'post_type'           => get_post_type(),
				'orderby'             => 'date',
				'ignore_sticky_posts' => 1,
				'paged'               => esc_attr($this->paged),
				'post__not_in' 		  => $exclude_ids,
				'has_password' => false ,
				'post_status'=> 'publish',
				'posts_per_page' => 2
			  );
			$query = new WP_Query( $query_args );
			while ($query->have_posts()) {
				$query->the_post();
				$urls[] = ampforwp_url_controller( get_permalink() );
				$titles[] = get_the_title();
				$images[] = ampforwp_get_post_thumbnail('url', 'full');
			}
			wp_reset_postdata();
			switch ($type) {
				case 'url':
					return $urls;
				case 'title':
					return $titles;
				case 'image':
					return $images;
			}
			return $urls;
		}

		public function loop_link() {
			global $wp;
			$amp_url = trailingslashit(home_url($wp->request));
			if( $this->paged < 2 ) {
				$amp_url = trailingslashit($amp_url.'page');
			}
			else
				$amp_url = str_replace('/'.$this->paged, '', $amp_url);
			return $amp_url;	
		}
		public function hide() {
			$classes = array();
			$design = ampforwp_get_setting('amp-design-selector');
			if ( 1 == $design ) {
				$classes = array("#pagination",".related_posts", ".amp-wp-footer",".amp-wp-header");
			}
			if ( 2 == $design ) {
				$classes = array("#headerwrap","#pagination","#footer",".nav_container",".related_posts");
			}
			if ( 3 == $design ) {
				$classes = array(".relatedpost",".footer_wrapper",".pagination-holder");
			}
			if ( 4 == $design ) {
				$classes = array(".p-m-fl",".loop-pagination",".footer",".r-pf",".srp ul",".srp h3","#pagination");
			}

			return json_encode($classes);
		}
		public function next_posts_link( $next_link , $paged ) {
			// Change the next link to paged+3
			// reason: amp-next-page will show the results for 3 pages
			$next_link = preg_replace('/'.($paged+1).'/', ($paged+3), $next_link);
			return $next_link;
		}
	}
	// Initiate the Class
	new AMPforWP_Infinite_Scroll();
}