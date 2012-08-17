<?php

global $so_panel_types, $so_panel_groups;
$panels = array();

if(!empty($so_panel_types)){
	foreach($so_panel_types as $group => $types){
		$panels[$group] = array();
		foreach($types as $class){
			$panel = new $class;
			ob_start();
			$panel->form();
			$form = ob_get_clean();
			
			if(empty($form)) $form = '<p>'.__('No Panel Settings', 'siteorigin').'</p>';
			
			// Add all the extra fields
			$form .= '<input type="hidden" data-info-field="order" name="panel_order[]" value="{%id}" />';
			$form .= '<input type="hidden" data-info-field="class" name="'.SO_Panel::input_name('info', 'class').'" value="'.$class.'" />';
			$form .= '<input type="hidden" data-info-field="id" name="'.SO_Panel::input_name('info', 'id').'" value="{%id}" />';
			$form .= '<input type="hidden" data-info-field="container" name="'.SO_Panel::input_name('info', 'grid').'" value="" />';
			$form .= '<input type="hidden" data-info-field="container" name="'.SO_Panel::input_name('info', 'cell').'" value="" />';
	
			$panels[$group][] = array(
				'info' => $panel->get_info(),
				'class' => $class,
				'form' => $form,
			);
		}
	}
}

?>

<div id="panels">
	<div id="panels-container">
	</div>
	
	<div id="add-to-panels">
		<button class="panels-add"><?php _e('Add Panel', 'siteorigin') ?></button>
		<button class="grid-add"><?php _e('Add Grid', 'siteorigin') ?></button>
	</div>
	
	<!-- The dialogs -->
	
	<div id="panels-dialog" data-title="<?php esc_attr_e('Add New Item','siteorigin') ?>" class="panels-admin-dialog">
		<div id="panels-dialog-tabs">
			<ul>
				<?php foreach($so_panel_groups as $id => $info) : ?>
					<li><a href="#panels-dialog-tabs-<?php print esc_attr($id) ?>"><?php print ($info['name']) ?></a></li>
				<?php endforeach; ?>
			</ul>
			
			<?php foreach($so_panel_groups as $group_id => $info) : $i = 0; ?>
				<div id="panels-dialog-tabs-<?php print esc_attr($group_id) ?>">
					<ul class="panel-type-list">
						<?php foreach($panels[$group_id] as $panel_type) : $i++; ?>
							<li class="panel-type" data-class="<?php print esc_attr($panel_type['class']) ?>" data-form="<?php print esc_attr($panel_type['form']) ?>">
								<div class="panel-type-wrapper">
									<h3><?php print $panel_type['info']['title'] ?></h3>
									<?php if(!empty($panel_type['info']['description'])) : ?>
										<small class="description"><?php print $panel_type['info']['description'] ?></small>
									<?php endif; ?>
								</div>
							</li>
							<?php if($i % 4 == 0) : ?><div class="clear"></div><?php endif; ?>
						<?php endforeach; ?>
						<?php if($i % 4 != 0) : ?><div class="clear"></div><?php endif; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
		
	</div>
	
	<div id="grid-setting-dialog" data-title="<?php esc_attr_e('Grid Settings','siteorigin') ?>" class="panels-admin-dialog">
		<div id="grid-setting-tabs">
			<ul>
				<li><a href="#grid-setting-desktop"><?php _e('General', 'siteorigin') ?></a></li>
				<li><a href="#grid-setting-desktop"><?php _e('Desktop', 'siteorigin') ?></a></li>
				<li><a href="#grid-setting-tablet"><?php _e('Tablet', 'siteorigin') ?></a></li>
				<li><a href="#grid-setting-mobile"><?php _e('Mobile', 'siteorigin') ?></a></li>
			</ul>
			
			<div id="grid-setting-desktop">
				This is foo
			</div>

			<div id="grid-setting-tablet">
				This is bar
			</div>

			<div id="grid-setting-mobile">
				This is the final one
			</div>
		</div>
	</div>

	<div id="grid-add-dialog" data-title="<?php esc_attr_e('Create Grid','siteorigin') ?>" class="panels-admin-dialog">
		<label><?php _e('Column Count', 'siteorigin') ?></label>
		<input type="text" class="small-text" value="3" />
	</div>
	
	<?php wp_nonce_field('save', '_sopanels_nonce') ?>
</div>