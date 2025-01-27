<?php
$mycentercareer = $this->mcd_settings['mycentercareer'];

$career_url = mcd_single_page_url('mycentercareer');
$prev_url = '';
$next_url = '';

if( isset($mycentercareer['prev']) ) {
	$prev_url = $career_url.$mycentercareer['prev']['slug'];
}
if( isset($mycentercareer['next']) ) {
	$next_url = $career_url.$mycentercareer['next']['slug'];
}
?>

<?php if( is_array($mycentercareer) ) : ?>

<div id="eyeoncareer-single" class="mycenterdeals-wrapper">
	<?php if( isset( $mycentercareer['error'] ) ) : ?>
		<?php if( isset( $mycentercareer['error']['description'] ) ) : ?>
      <div class="mcd-alert"><?= $mycentercareer['error']['description'] ?></div>
    <?php else: ?>
      <div class="mcd-alert"><?= $mycentercareer['error'] ?></div>
    <?php endif; ?>
	<?php else: ?>
		<div class="eyeon-career clearfix">
			<div class="mcd-prev-next-nav">
				<?php if( !empty($this->mcd_settings['careers_listing_page']) ) : ?>
					<a href="<?= get_permalink($this->mcd_settings['careers_listing_page']) ?>" class="item back">Back to Careers</a>
				<?php endif; ?>
				<a <?= (!empty($prev_url)?'href="'.$prev_url.'"':'') ?> class="item prev hide <?= (empty($prev_url)?'disabled':'') ?>"><i class="fas fa-chevron-left"></i><span>Prev</span></a>
				<a <?= (!empty($next_url)?'href="'.$next_url.'"':'') ?> class="item next hide <?= (empty($next_url)?'disabled':'') ?>"><span>Next</span><i class="fas fa-chevron-right"></i></a>
			</div>

			<div class="mcd-career-cols">
				<div class="mcd-career-image-col">
					<div class="mcd-retailer-image">
						<img src="<?= $mycentercareer['retailer']['media']['url'] ?>" />
					</div>
					<div class="mcd-retailer-name eyeon-hide"><?= $mycentercareer['retailer']['name'] ?></div>
				</div>

				<div class="mcd-career-details">
					<div class="mcd-career-title"><?= $mycentercareer['title'] ?></div>
					<div class="mcd-career-description editor_output"><?= get_editor_output($mycentercareer['description']) ?></div>

					<?php if( !empty($mycentercareer['apply_link']) ) : ?>
					<div class="mcd-apply-link"><a href="<?= $mycentercareer['apply_link'] ?>" class="mcp_btn rounded" target="_blank">Apply Now</a></div>
					<?php endif; ?>

					<?php if(
            (isset($mycentercareer['contact_person']['name']) && !empty($mycentercareer['contact_person']['name']))
            || (isset($mycentercareer['contact_person']['email']) && !empty($mycentercareer['contact_person']['email']))
            || (isset($mycentercareer['contact_person']['cell_phone']) && !empty($mycentercareer['contact_person']['cell_phone']))
          ) : ?>
					<div class="mcd-contact-details">
						<span class="mcd-label">Contact Details:</span>
						<?php if( !empty($mycentercareer['contact_person']['name']) ) : ?>
							<div class="mcd-contact-detail"><?= $mycentercareer['contact_person']['name'] ?></div>
						<?php endif; ?>
						<?php if( !empty($mycentercareer['contact_person']['email']) ) : ?>
							<div class="mcd-contact-detail"><?= $mycentercareer['contact_person']['email'] ?></div>
						<?php endif; ?>
						<?php if( !empty($mycentercareer['contact_person']['cell_phone']) ) : ?>
							<div class="mcd-contact-detail"><?= eyeon_format_phone($mycentercareer['contact_person']['cell_phone']) ?></div>
						<?php endif; ?>
					</div>
					<?php endif; ?>

					<?php if( !empty($mycentercareer['retailer']['phone']) ) :?>
            <div class="mcd-retailer-phone"><span class="mcd-label">Retailer Phone:</span> <?= eyeon_format_phone($mycentercareer['retailer']['phone']) ?></div>
					<?php endif; ?>

					<?php if( $this->mcd_settings['careers_single_social_share'] ) : ?>
					<div class="mcd-career-share clearfix">
						<ul class="mcd-social-icons">
							<li class="twitter"><a href="http://twitter.com/share?text=<?= urlencode($mycentercareer['title']) ?>&url=<?= get_current_url() ?>" target="_blank">Twitter</a></li>
							<li class="facebook"><a href="http://www.facebook.com/sharer.php?u=<?= get_current_url() ?>&quote=<?= urlencode($mycentercareer['title']) ?>" target="_blank">Facebook</a></li>
							<!-- <li class="pinterest"><a href="http://pinterest.com/pin/create/button/?url=<?= get_current_url() ?>&media=<?= $mycentercareer['retailer']['media']['url'] ?>&description=<?= $mycentercareer['career'] ?>" target="_blank">Pinterest</a></li> -->
							<li class="email"><a href="mailto:?subject=<?= $mycentercareer['retailer']['name'] ?> - <?= $mycentercareer['title'] ?>&body=Hi,%0D%0A%0D%0ACheckout this Job! - <?= urlencode(get_current_url()) ?>%0D%0A%0D%0A<?= $mycentercareer['title'] ?>%0D%0A%0D%0A<?= strip_tags($mycentercareer['description']) ?>%0D%0A%0D%0AStore: <?= $mycentercareer['retailer']['name'] ?>%0D%0ACenter Location: <?= $mycentercareer['center']['name'] ?>%0D%0APhone: <?= $mycentercareer['contact_person']['cell_phone'] ?>%0D%0A%0D%0A">Email</a></li>
						</ul>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

	<?php endif; ?>
</div>

<?php endif; ?>

