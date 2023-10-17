<?php
$mcd_center_id = isset( $this->mcd_settings['center_id'] ) ? $this->mcd_settings['center_id'] : 0;

$shortcode_atts = $this->mcd_settings['events_shortcode_atts'];

$holiday = '';
if( isset($shortcode_atts['holiday']) ) {
	$holiday = $shortcode_atts['holiday'];
}

$type = '';
if( isset($shortcode_atts['type']) ) {
	$type = $shortcode_atts['type'];
}

?>

<div ng-app="MyCenterPortalApp" ng-controller="EventsCtrl" data-url="<?= MCD_API_EVENTS.'?limit=100&page=1' ?>" data-center-id="<?= $mcd_center_id ?>" data-event-url="<?= mcd_single_page_url('mycenterevent') ?>">
	<div class="mycenterdeals-wrapper mycenterevents" ng-class="{loading: busy}">
		<div id="mcd-error-msg" ng-show="data.error" ng-cloak>
			<div class="mcd-alert">{{ data.error }}</div>
		</div>
	
		<?php if( empty($type) || $type == 'calendar' ) : ?>
		<div id="mcd-calendar" <?= (empty($type)?'class="mcd-magic-hide" ng-show="selectedType == \'calendar\'"':'') ?> ng-cloak>
			<div id="calendar"></div>
		</div>
		<?php endif; ?>

		<?php if( empty($type) || $type == 'grid' ) : ?>
		<div id="mcd-events-grid" ng-show="selectedType == 'grid'" ng-cloak>
			<a class="mcd-event-item" ng-repeat="event in data.items" href="{{ event.event_url }}" title="{{ event.title }}">
				<span class="mcd-event-image">
					<img ng-src="{{ event.media.url }}" />
				</span>
				<?php if( $this->mcd_settings['events_listing_grid_title'] ) : ?>
					<div class="mcd-event-title">{{ event.title }}</div>
				<?php endif; ?>
        <div class="metadata">
          <div class="date" ng-if="event.start_date!==event.end_date">
            <i class="far fa-calendar"></i>
            <span>{{ event.start_date }} - {{ event.end_date }}</span>
          </div>
          <div class="date" ng-if="event.start_date===event.end_date">
            <i class="far fa-calendar"></i>
            <span>{{ event.start_date }}</span>
          </div>
          <div class="time">
            <i class="far fa-clock"></i>
            <span>{{ event.start_time_formatted }} - {{ event.end_time_formatted }}</span>
          </div>
        </div>
			</a>
		</div>
		<?php endif; ?>
	</div>
</div>

