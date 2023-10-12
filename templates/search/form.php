<?php 
$unique_id = uniqid();
$form_style = 'light';
if( isset($this->mcd_settings['search_shortcode_atts']['style']) && $this->mcd_settings['search_shortcode_atts']['style'] == 'dark' ) {
	$form_style = 'dark';
}
?>

<style type="text/css">
#mcd_search_form_<?= $unique_id ?> {
	border: 1px solid rgba(0,0,0,0.4);
	outline: none;
	overflow: hidden;
	position: relative;
	margin-bottom: 15px;
	width: 100%;
	-webkit-border-radius: 6px;
	border-radius: 6px;
}
#mcd_search_form_<?= $unique_id ?>.dark {
	border-color: rgba(255,255,255,.5);
}

.mycenterdeals-wrapper.mycentersearch #mcd_search_form_<?= $unique_id ?> {
	max-width: 240px;
}


#mcd_search_form_<?= $unique_id ?> .mcd_search_field {
	padding: 0 12px;
	margin: 0;
	height: 40px;
	line-height: 38px;
	border: none;
	background-color: rgba(0,0,0,0.1);
	display: block;
	width: 100%;
	color: #666;
	font-size: 16px;
}
#mcd_search_form_<?= $unique_id ?>.dark .mcd_search_field {
	background-color: rgba(255,255,255,0.3);
	color: #DDD;
}

#mcd_search_form_<?= $unique_id ?> .mcd_search_field::placeholder {
	color: rgba(0,0,0,0.8);
}
#mcd_search_form_<?= $unique_id ?>.dark .mcd_search_field::placeholder {
	color: rgba(255,255,255,0.8);
}


#mcd_search_form_<?= $unique_id ?> .mcd_search_submit {
	margin: 0;
	padding: 0;
	background: no-repeat;
	color: rgba(0,0,0,.5);
	position: absolute;
	top: 0;
	right: 0;
	height: 40px;
	width: 40px;
	line-height: 38px;
	min-height: auto;
	font-size: 18px;
	border: none;
}
#mcd_search_form_<?= $unique_id ?>.dark .mcd_search_submit {
	color: rgba(255,255,255,.6);
}

#mcd_search_form_<?= $unique_id ?> .mcd_search_submit:hover {
	color: rgba(0,0,0,.7);
}
#mcd_search_form_<?= $unique_id ?>.dark .mcd_search_submit:hover {
	color: rgba(255,255,255,.8);
}
</style>

<form role="search" method="get" id="mcd_search_form_<?= $unique_id ?>" class="<?= $form_style ?>" action="<?= esc_url( home_url( '/' ) ) ?>">
	<input type="search" name="mycentersearch" class="mcd_search_field" placeholder="Search &hellip;" value="<?= stripslashes(urldecode(get_query_var('mycentersearch'))) ?>" autocomplete="off" />
	<button type="submit" class="mcd_search_submit">
		<i class="fa fa-search"></i>
	</button>
</form>

