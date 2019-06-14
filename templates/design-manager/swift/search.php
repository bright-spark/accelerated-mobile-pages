<?php global $redux_builder_amp;
amp_header() ?>
<div class="cntr archive">
	<div class="arch-tlt">
		<?php amp_archive_title(); ?>
	</div>
	<div class="arch-dsgn">
		<div class="arch-psts">
			<?php amp_loop_template(); ?>
			<?php 
			if (empty(amp_loop_template())){ ?>
				<div class="cntn-wrp srch ">
					<p><?php
						$message = '';
						if(! empty(ampforwp_get_setting('amp-translator-search-no-found') ) ){
							$message = ampforwp_translation( ampforwp_get_setting('amp-translator-search-no-found'), 'It seems we can\'t find what you\'re looking for.');
						}
	 					echo esc_html($message); ?>
	 				</p>
			    </div>
 			<?php } ?>
			<?php amp_pagination(); ?>
		</div>
		<?php if(isset($redux_builder_amp['gbl-sidebar']) && $redux_builder_amp['gbl-sidebar'] == '1'){ ?>
		<div class="sdbr-right">
			<?php 
				$sanitized_sidebar = ampforwp_sidebar_content_sanitizer('swift-sidebar');
			if ( $sanitized_sidebar) {
				$sidebar_output = $sanitized_sidebar->get_amp_content();
				$sidebar_output = apply_filters('ampforwp_modify_sidebars_content',$sidebar_output);
            	echo $sidebar_output; // amphtml content, no kses
			}
			?>
		</div>
		<?php } ?>
	</div>
</div>
<?php amp_footer()?>