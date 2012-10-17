<?php

$panel_widgets = array();
global $wp_widget_factory;

$i = 0;
foreach($wp_widget_factory->widgets as $class => $info){
	
	$widget = new $class();
	$widget->id = 'temp';
	$widget->number = $i++;
	
	ob_start();
	$widget->form(array());
	$form = ob_get_clean();
	
	// Conver the widget field naming into ones that panels uses
	$exp = preg_quote($widget->get_field_name('____'));
	$exp = str_replace('____', '(.*?)', $exp);
	$form = preg_replace('/'.$exp.'/', 'widgets[{$id}][$1]', $form);
	
	// Add all the extra fields
	$form .= '<input type="hidden" data-info-field="order" name="panel_order[]" value="{$id}" />';
	$form .= '<input type="hidden" data-info-field="class" name="widgets[{$id}][info][class]" value="'.$class.'" />';
	$form .= '<input type="hidden" data-info-field="id" name="widgets[{$id}][info][id]" value="{$id}" />';
	$form .= '<input type="hidden" data-info-field="grid" name="widgets[{$id}][info][grid]" value="" />';
	$form .= '<input type="hidden" data-info-field="cell" name="widgets[{$id}][info][cell]" value="" />';
	
	$widget->form = $form;
	
	$panel_widgets[] = $widget;
}

?>

<div id="panels">
	<div id="panels-container">
	</div>
	
	<div id="add-to-panels">
		<button class="panels-add" data-tooltip="<?php esc_attr_e('Add Widget','siteorigin') ?>"><?php _e('Add Widget', 'siteorigin') ?></button>
		<button class="grid-add" data-tooltip="<?php esc_attr_e('Add Columns','siteorigin') ?>"><?php _e('Add Columns', 'siteorigin') ?></button>
	</div>
	
	<!-- The dialogs -->
	
	<div id="panels-dialog" data-title="<?php esc_attr_e('Add Widget','siteorigin') ?>" class="panels-admin-dialog">
		<div id="panels-dialog-tabs">
			
			<div class="panels-text-filter">
				<input type="search" class="widefat" placeholder="Filter" id="panels-text-filter-input" />
			</div>
			
			<ul class="panel-type-list">
				<?php $i = 0; foreach($panel_widgets as $widget) : $i++; ?>
					<li class="panel-type"
						data-class="<?php echo esc_attr(get_class($widget)) ?>"
						data-form="<?php echo esc_attr($widget->form) ?>"
						data-title="<?php echo esc_attr($widget->name) ?>"
						>
						<div class="panel-type-wrapper">
							<h3><?php echo esc_html($widget->name) ?></h3>
							<?php if(!empty($widget->widget_options['description'])) : ?>
								<small class="description"><?php echo esc_html($widget->widget_options['description']) ?></small>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
				
				<div class="clear"></div>
			</ul>
			
			<?php if(!defined('SITEORIGIN_IS_PREMIUM')) : ?>
				<p><?php printf(__('Additional widgets are available in <a href="%s">%s Premium</a>'), admin_url('themes.php?page=premium_upgrade'), ucfirst(get_option('stylesheet'))) ?></p>
			<?php endif; ?>
		</div>
		
	</div>
	
	<div id="grid-add-dialog" data-title="<?php esc_attr_e('Add Columns','siteorigin') ?>" class="panels-admin-dialog">
		<p><label><strong><?php _e('Columns', 'siteorigin') ?></strong></label></p>
		<p><input type="text" id="grid-add-dialog-input" name="column_count" class="small-text" value="3" /></p>
	</div>
	
	<?php wp_nonce_field('save', '_sopanels_nonce') ?>
</div>