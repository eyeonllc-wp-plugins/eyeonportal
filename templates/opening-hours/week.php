<div class="mcd_oh_week">
	<table>
		<tbody>
			<?php foreach( $this->mcd_settings['opening_hours_week'] as $day ) :
				$day_value = eyeon_format_time($day['open_time']).' - '.eyeon_format_time($day['close_time']);
				if( $day['status'] == 'closed' ) {
					$day_value = $day['title'];
				} elseif( $day['status'] == 'holiday' ) {
					$day_value = $day['title'].' — Closed';
				} elseif( $day['status'] == 'irregular' ) {
					$day_value = $day['title'].'<br>'.$day_value;
				}
				?>
				<tr>
					<td><?= strtoupper($day['day']) ?></td>
					<td><?= $day_value ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
