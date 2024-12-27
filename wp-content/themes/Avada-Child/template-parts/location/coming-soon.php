<div class="location-cs-section">
	<div class="wrap">
		<div class="cs-bl-1">
			<?php the_field( 'cs_sup_headline' ); ?>
		</div>
		<div class="cs-bl-2">
			<?php the_field( 'cs_headline' ); ?>
		</div>
		<div class="cs-bl-3">
			<?php the_field( 'cs_date' ); ?>
		</div>
		<div class="cs-bl-4">
			<?php the_field( 'cs_special_offer' ); ?>
		</div>
		<div class="cs-bl-5">
			<a href="<?php the_field( 'cs_button_link' ); ?>" class="btn-orange"><?php the_field( 'cs_button_text' ); ?></a>
		</div>
		<div class="cs-bl-6">
			<?php the_field( 'cs_below_button' ); ?>
		</div>
		<div class="cs-bl-7">
			<?php the_field( 'cs_list_title' ); ?>
		</div>
		<div class="location-cs-list">
			<?php foreach ( get_field( 'cs_list' ) as $item ) : ?>
				echo '<div><span>'. $item['item'] .'</span></div>';
			<?php endforeach; ?>
		</div>
	</div>
</div>