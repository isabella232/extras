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
							<li class="panel-type"
								data-class="<?php print esc_attr($panel_type['class']) ?>"
								data-form="<?php print esc_attr($panel_type['form']) ?>"
								data-title="<?php print esc_attr($panel_type['info']['title']) ?>"
								<?php if(!empty($panel_type['info']['title_field'])) : ?>data-title-field="<?php print esc_attr($panel_type['info']['title_field']) ?>"<?php endif ?>
								>
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
	
	<div id="grid-add-dialog" data-title="<?php esc_attr_e('Create Grid','siteorigin') ?>" class="panels-admin-dialog">
		<?php
		global $so_panel_grids;
		?><ul class="panel-grid-list"><?php
		foreach ($so_panel_grids as $id => $grid){
			$cells = array();
			foreach($grid['cells'] as $cell){
				$cells[] = $cell['weight'];
			}
			?>
			<li class="panel-grid"
				data-cells="<?php print esc_attr(implode('|', $cells)) ?>"
				data-type="<?php echo esc_attr($id) ?>"
				>
				<div class="panel-grid-wrapper">
					<h3><?php print esc_html(isset($grid['title']) ? $grid['title'] : __('Untitled', 'siteorigin')) ?></h3>
					<?php if(!empty($grid['description'])) : ?>
						<small class="description"><?php print esc_html($grid['description']) ?></small>
					<?php endif; ?>
				</div>
			</li>
			<?php
		}
		?>
		</ul>
	</div>
	
	<?php wp_nonce_field('save', '_sopanels_nonce') ?>
</div>