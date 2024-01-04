<?php
$mcd_today = $this->mcd_settings['opening_hours_today'];
$atts = $this->mcd_settings['opening_hours_today_shortcode_atts'];
?>
<div class="mcd_oh_today <?= $mcd_today['type'] ?>">
	<?php if( $mcd_today['type'] == 'holiday' ) : ?>
		<div class="mcd_open_status <?= $mcd_today['status'] ?>"><?= $atts['closed_text'] ?></div>
		<div class="mcd_holiday_title"><?= $mcd_today['title'] ?> — Closed</div>
	<?php else : ?>
		<div class="mcd_open_status <?= $mcd_today['status'] ?>"><?= ($mcd_today['status']=='open'?$atts['open_text']:$atts['closed_text']) ?></div>
		<?php if( $mcd_today['type'] == 'irregular' ) : ?>
			<div class="mcd_holiday_title"><?= $mcd_today['title'] ?></div>
		<?php endif; ?>
		<?php if( isset($mcd_today['open_time']) && isset($mcd_today['close_time']) ) : ?>
			<div class="mcd_open_timings"><?= eyeon_format_time($mcd_today['open_time']).' - '.eyeon_format_time($mcd_today['close_time']) ?></div>
		<?php endif; ?>
	<?php endif; ?>
</div>
