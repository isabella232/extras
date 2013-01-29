<?php

function siteorigin_panels_default_prebuilt_layouts($layouts){
	return $layouts;
}
add_filter('siteorigin_panels_prebuilt_layouts', 'siteorigin_panels_default_prebuilt_layouts');