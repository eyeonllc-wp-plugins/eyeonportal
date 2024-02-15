<?php
$mycenterevent = $this->mcd_settings['mycenterevent'];
$mycenterevent['start_time'] = eyeon_format_time($mycenterevent['start_time']);
$mycenterevent['end_time'] = eyeon_format_time($mycenterevent['end_time']);

$rdate = '';
if( isset($mycenterevent['rrdate']) ) {
	$rdate = $mycenterevent['rrdate'];
}
if( isset($_GET['rdate']) ) {
	$rdate = date('M jS, Y', $_GET['rdate']);
}

$event_dates = eyeon_format_date($mycenterevent['start_date']);
if( $mycenterevent['end_date'] ) {
  $event_dates .= ' - '.eyeon_format_date($mycenterevent['end_date']);
}
if( $mycenterevent['start_date'] === $mycenterevent['end_date'] ) {
	$event_dates = eyeon_format_date($mycenterevent['start_date']);
}

$event_url = mcd_single_page_url('mycenterevent');
$prev_url = '';
$next_url = '';

if( isset($mycenterevent['prev']) ) {
	$prev_url = $event_url.$mycenterevent['prev']['slug'];
}
if( isset($mycenterevent['next']) ) {
	$next_url = $event_url.$mycenterevent['next']['slug'];
}
?>

<?php if( is_array($mycenterevent) ) : ?>

<div id="eyeonevent-single" class="mycenterdeals-wrapper">
	<?php if( isset( $mycenterevent['error'] ) ) : ?>
		<div class="mcd-alert"><?= $mycenterevent['error'] ?></div>
	<?php else: ?>
		<div class="eyeon-event clearfix">
			<div class="mcd-event-cols">
				<div class="mcd-event-image-col">
					<div class="mcd-event-image">
						<img src="<?= $mycenterevent['media']['url'] ?>" />
					</div>
				</div>

				<div class="mcd-event-details-col">
					<div class="mcd-event-name"><?= $mycenterevent['title'] ?></div>

          <?php if( !$mycenterevent['ongoing_event'] ) : ?>
            <div class="mcd-event-date-time">
              <div class="mcd-event-dates">
                <i class="far fa-calendar-alt"></i>&nbsp;
                <?= (!empty($rdate) ? $rdate : $event_dates) ?>
              </div>
              <?php if( !empty($mycenterevent['start_time']) && !$mycenterevent['is_all_day_event'] ) : ?>
                <div class="mcd-event-times">
                  <i class="far fa-clock"></i>&nbsp;
                  <?= $mycenterevent['start_time'] ?> - <?= $mycenterevent['end_time'] ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
					
          <div class="mcd-event-description editor_output"><?= get_editor_output($mycenterevent['description']) ?></div>

					<?php if( $this->mcd_settings['events_single_add_to_calendar'] ) : ?>
					<div class="mcd-event-add-to-calendar">
						<div title="Add to Calendar" class="addeventatc">
							<span>Add to Calendar</span>
							<span class="date_format">DD/MM/YYYY</span>
							<span class="start"><?= date('d/m/Y', strtotime($mycenterevent['start_date'])) ?> <?= (!empty($mycenterevent['start_time'])?$mycenterevent['start_time']:'12:00 am') ?></span>
							<span class="end"><?= date('d/m/Y', strtotime($mycenterevent['end_date'])) ?> <?= (!empty($mycenterevent['end_time'])?$mycenterevent['end_time']:'11:59 pm') ?></span>
							<?php if( $mycenterevent['is_all_day_event'] ) : ?>
                <span class="all_day_event">true</span>
							<?php endif; ?>
							<?php if( $mycenterevent['is_repeat_event'] ) : ?>
                <span class="recurring"><?= $mycenterevent['repeat_rrule'] ?></span>
							<?php endif; ?>
							<span class="title"><?= $mycenterevent['title'] ?></span>
							<span class="description"><?= $mycenterevent['short_description'] ?></span>
							<span class="location"><?= $mycenterevent['center']['name'] ?></span>
						</div>
					</div>
					<?php endif; ?>

					<?php if( $this->mcd_settings['events_single_social_share'] ) : ?>
					<div class="mcd-event-share clearfix">
            <span class="mcd-share-title mcd-label">Share</span>
						<ul class="mcd-social-icons">
							<li class="twitter"><a href="http://twitter.com/share?text=<?= urlencode($mycenterevent['title']) ?>&url=<?= get_current_url() ?>" target="_blank">Twitter</a></li>
							<li class="facebook"><a href="http://www.facebook.com/sharer.php?u=<?= get_current_url() ?>&quote=<?= urlencode($mycenterevent['title']) ?>" target="_blank">Facebook</a></li>
							<li class="email"><a href="mailto:?subject=<?= $mycenterevent['title'] ?>&body=Hi,%0D%0A%0D%0AEvent Details - <?= urlencode(get_current_url()) ?>%0D%0A%0D%0A<?= $mycenterevent['title'] ?>%0D%0A%0D%0A<?= urlencode($mycenterevent['description']) ?>%0D%0A%0D%0A<?= $event_dates ?>%0D%0A<?= $mycenterevent['start_time'] ?> - <?= $mycenterevent['end_time'] ?>%0D%0A%0D%0ACenter Location: <?= $mycenterevent['center']['name'] ?>%0D%0A%0D%0A">Email</a></li>
						</ul>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

	<?php endif; ?>	
</div>

<?php endif; ?>

<script type="text/javascript">
window.addeventasync = function(){
    addeventatc.settings({
        appleical  : {show:true, text:"Apple Calendar"},
        google     : {show:true, text:"Google Calendar"},
        outlook    : {show:false, text:"Outlook"},
        outlookcom : {show:false, text:"Outlook.com <em>(online)</em>"},
        yahoo      : {show:false, text:"Yahoo <em>(online)</em>"}
    });
};
</script>

