<?php
$mycenterdeal = $this->mcd_settings['mycenterdeal'];

$deal_url = mcd_single_page_url('mycenterdeal');
$prev_url = '';
$next_url = '';

if( isset($mycenterdeal['prev']) ) {
	$prev_url = $deal_url.$mycenterdeal['prev']['slug'];
}
if( isset($mycenterdeal['next']) ) {
	$next_url = $deal_url.$mycenterdeal['next']['slug'];
}
?>

<?php if( is_array($mycenterdeal) ) : ?>

<div id="eyeondeal-single" class="mycenterdeals-wrapper">
	<?php if( isset( $mycenterdeal['error'] ) ) : ?>
		<?php if( isset( $mycenterdeal['error']['description'] ) ) : ?>
      <div class="mcd-alert"><?= $mycenterdeal['error']['description'] ?></div>
    <?php else: ?>
      <div class="mcd-alert"><?= $mycenterdeal['error'] ?></div>
    <?php endif; ?>
	<?php else: ?>
		<div class="eyeon-deal clearfix">
			<div class="mcd-prev-next-nav">
				<?php if( !empty($this->mcd_settings['deals_listing_page']) ) : ?>
					<a href="<?= get_permalink($this->mcd_settings['deals_listing_page']) ?>" class="item back">Back to Deals</a>
				<?php endif; ?>
				<a <?= (!empty($prev_url)?'href="'.$prev_url.'"':'') ?> class="item prev hide <?= (empty($prev_url)?'disabled':'') ?>"><i class="fas fa-chevron-left"></i><span>Prev</span></a>
				<a <?= (!empty($next_url)?'href="'.$next_url.'"':'') ?> class="item next hide <?= (empty($next_url)?'disabled':'') ?>"><span>Next</span><i class="fas fa-chevron-right"></i></a>
			</div>

			<div class="mcd-deal-cols">
				<div class="mcd-deal-image-col">
					<div class="mcd-deal-image">
						<img src="<?= $mycenterdeal['media']['url'] ?>" />
					</div>
          <div class="mcd-retailer-logo">
            <img src="<?= $mycenterdeal['retailer']['media']['url'] ?>" />
          </div>
				</div>

				<div class="eyeon-deal-details">
          <div class="mcd-deal-title"><?= $mycenterdeal['title'] ?></div>
          <div class="mcd-retailer-name"><?= $mycenterdeal['retailer']['name'] ?></div>
          <div class="mcd-deal-until"><span class="mcd-label">Valid Until:</span> <?= eyeon_format_date($mycenterdeal['end_date']) ?></div>
          <div class="mcd-deal-message editor_output"><?= get_editor_output($mycenterdeal['description']) ?></div>

          <?php
          $retailer_phone = @$mycenterdeal['retailer']['phone'];
          $retailer_location = get_retailer_location($mycenterdeal['retailer']['location']);
          ?>

          <?php if( !empty($retailer_phone) || !empty($retailer_location) ) : ?>
            <div class="eyeon-retailer-phone-location">
              <?php if( !empty($retailer_phone) ) : ?>
                <div class="mcd-retailer-phone"><span class="mcd-label">Retailer Phone:</span> <?= eyeon_format_phone($retailer_phone) ?></div>
              <?php endif; ?>
              <?php if( !empty($retailer_location) ) : ?>
                <div class="mcd-retailer-location"><span class="mcd-label">Retailer Location:</span> <?= $retailer_location ?></div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
          
          <?php if( $this->mcd_settings['deals_single_social_share'] ) : ?>
          <div class="mcd-deal-share clearfix">
            <span class="mcd-share-title mcd-label">Share</span>
            <ul class="mcd-social-icons">
              <li class="twitter"><a href="http://twitter.com/share?text=<?= urlencode($mycenterdeal['title']) ?>&url=<?= get_current_url() ?>" target="_blank">Twitter</a></li>
              <li class="facebook"><a href="http://www.facebook.com/sharer.php?u=<?= get_current_url() ?>&quote=<?= urlencode($mycenterdeal['title']) ?>" target="_blank">Facebook</a></li>
              <li class="email"><a href="mailto:?subject=<?= $mycenterdeal['retailer']['name'] ?> - <?= $mycenterdeal['title'] ?>&body=Hi,%0D%0A%0D%0ACheckout this Deal! - <?= urlencode(get_current_url()) ?>%0D%0A%0D%0A<?= $mycenterdeal['title'] ?>%0D%0A%0D%0A<?= strip_tags($mycenterdeal['short_description']) ?>%0D%0A%0D%0AValid Until: <?= $mycenterdeal['end_date'] ?>%0D%0A%0D%0AStore: <?= $mycenterdeal['retailer']['name'].', '.$mycenterdeal['center']['name'] ?>%0D%0APhone: <?= eyeon_format_phone($retailer_phone) ?>%0D%0A%0D%0A">Email</a></li>
            </ul>
          </div>
          <?php endif; ?>
				</div>
			</div>
		</div>

	<?php endif; ?>	
</div>

<?php endif; ?>

