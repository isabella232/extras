<?php global $post ?>

<div id="slider-builder">
	<?php $images = siteorigin_slider_get_post_images($post->ID); ?>
	<div id="slider-builder-slides-skeleton">
		<div class="slide-title widget-top"><strong><?php _e('New Item', 'siteorigin') ?></strong> <a class="widget-action hide-if-no-js" href="#"></a></div>
		<div class="slide-content">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label><?php _e('Title Text', 'siteorigin') ?></label></th>
						<td><input name="siteorigin_slider[title][]" type="text" class="title-field regular-text" data-field="title"></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label><?php _e('Extra Text', 'siteorigin') ?></label></th>
						<td>
							<textarea rows="2" name="siteorigin_slider[extra][]" class="large-text" data-field="extra"></textarea>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><label><?php _e('Image', 'siteorigin') ?></label></th>
						<td>
							<select class="siteorigin-media" name="siteorigin_slider[image][]" data-field="image">
								<option value="-1"><?php _e('None', 'siteorigin') ?></option>
							</select>
							<div class="loading"></div>
						</td>
					</tr>
				
					<?php do_action('siteorigin_slider_after_builder_form'); ?>
					
				</tbody>
			</table>
			
			<p><a href="#" class="delete" data-confirm="<?php esc_attr_e('Are you sure you want to delete this?', 'siteorigin') ?>">Delete</a></p>
		</div>
	</div>
	
	<ul id="slider-builder-slides" class="ui-sortable">
		<!-- The Slides -->
	</ul>
	
	<?php wp_nonce_field('save_slider', '_siteorigin_slider_nonce') ?>
	
	<a href="#" class="button-secondary" id="slider-builder-add"><?php _e('Add Slide', 'siteorigin') ?></a>
	<a href="<?php echo admin_url('media-upload.php') ?>?post_id=<?php echo intval($post->ID) ?>&TB_iframe=1&width=640&height=542" class="thickbox add_media" id="content-add_media" title="Add Media" onclick="return false;"><?php _e('Upload Media', 'siteorigin') ?></a>
</div>