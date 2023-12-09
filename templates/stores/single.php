<?php
$mycenterstore = $this->mcd_settings['mycenterstore'];
$map_config = $this->mcd_settings['map_config'];

$social_links = array('affiliate_url', 'website', 'facebook', 'instagram', 'pinterest', 'twitter', 'youtube');

$store_url = mcd_single_page_url('mycenterstore');
$prev_url = '';
$next_url = '';

if( isset($mycenterstore['prev']) ) {
	$prev_url = $store_url.$mycenterstore['prev']['slug'];
}
if( isset($mycenterstore['next']) ) {
	$next_url = $store_url.$mycenterstore['next']['slug'];
}
?>

<?php if( is_array($mycenterstore) ) : ?>

<div id="eyeonstore-single" class="mycenterdeals-wrapper">
	<?php if( isset( $mycenterstore['error'] ) ) : ?>
		<div class="mcd-alert"><?= $mycenterstore['error'] ?></div>
	<?php else: ?>
		<div class="eyeon-store clearfix">
			<div class="mcd-prev-next-nav">
				<?php if( !empty($this->mcd_settings['stores_listing_page']) ) : ?>
					<a href="<?= get_permalink($this->mcd_settings['stores_listing_page']) ?>" class="item back">Back to Stores</a>
				<?php endif; ?>
				<a <?= (!empty($prev_url)?'href="'.$prev_url.'"':'') ?> class="item prev hide <?= (empty($prev_url)?'disabled':'') ?>"><i class="fas fa-chevron-left"></i><span>Prev</span></a>
				<a <?= (!empty($next_url)?'href="'.$next_url.'"':'') ?> class="item next hide <?= (empty($next_url)?'disabled':'') ?>"><span>Next</span><i class="fas fa-chevron-right"></i></a>
			</div>

			<div class="mcd-store-cols">
				<div class="mcd-retailer-image-col">
					<div class="mcd-retailer-image">
						<img src="<?= $mycenterstore['media']['url'] ?>" />
					</div>
				</div>

				<div class="mcd-retailer-details">
					<div class="mcd-retailer-name"><?= $mycenterstore['name'] ?></div>
          <?php if( !empty(trim($mycenterstore['location'])) && trim($mycenterstore['location']) !== '-' ) : ?>
            <div class="mcd-retailer-location"><span class="mcd-label">Location:</span> <?= $mycenterstore['location'] ?></div>
          <?php endif; ?>
					<?php if( !empty($mycenterstore['retailer_phone']) ) : ?>
						<div class="mcd-retailer-phone"><span class="mcd-label">Phone:</span> <?= $mycenterstore['retailer_phone'] ?></div>
					<?php endif; ?>
					<div class="mcd-retailer-description editor_output">
            <?= trim($mycenterstore['global_retailer']['description']) ?>
            <?= !empty(trim($mycenterstore['description']))?'<br><br>'.trim($mycenterstore['description']):'' ?>
          </div>
					
					<?php if( $this->mcd_settings['stores_single_social_links'] ) : ?>
						<?php
						$social_links_html = '';
							foreach ($social_links as $link) {
								if( !empty($mycenterstore['global_retailer']['links'][$link]) ) {
									$social_links_html .= '<li class="'.$link.'"><a href="'.$mycenterstore['global_retailer']['links'][$link].'" target="_blank">'.$link.'</a></li>';
								}
							}
						?>
						<?php if( !empty($social_links_html) ) : ?>
						<div class="mcd-social-links">
							<ul class="mcd-social-icons"><?= $social_links_html ?></ul>
						</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if( !empty($this->mcd_settings['map_page']) || !empty($map_config['map_url']) ) :
						$new_tab = false;
						$map_url = get_permalink($this->mcd_settings['map_page']).$mycenterstore['slug'];
						if( !empty($map_config['map_url']) ) {
							$new_tab = true;
							$map_url = $map_config['map_url'];
						}
						?>
						<a href="<?= $map_url ?>" <?= ($new_tab?'target="blank"':'') ?> class="mcd_mapit_link">Find IT</a>
					<?php endif; ?>
				</div>

        <?php if( $mycenterstore['opening_hours'] ) : ?>
        <div class="mcd-retailer-opening-hours">
          <h4>Opening Hours:</h4>
          <div class="hours-sets">
            <?php foreach( weekdays() as $key=>$day ) : ?>
            <div class="hours-set">
              <div class="day"><?= $day ?></div>
              <div class="time">
                <?= $mycenterstore['opening_hours'][$key]['startTime'] ?> - <?= $mycenterstore['opening_hours'][$key]['endTime'] ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
			</div>
		</div>

		<?php include(MCD_PLUGIN_PATH.'templates/stores/single/deals.php') ?>
	<?php endif; ?>
</div>

<?php endif; ?>

