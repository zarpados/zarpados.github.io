<div class="flexslider">
	<ul class="slides">
    	<?php
		$image = 'image_870x490';

		$post_num = 0;
		$args = array( 
			'post_type' => 'attachment', 
			'numberposts' => -1, 
			'post_status' => null, 
			'post_parent' => $post->ID,
			'order' => 'ASC',
			'orderby' => 'menu_order',
		); 
		$attachments = get_posts( $args );

		foreach($attachments as $key => $photo)
		{
			if(!empty($photo->guid))
			{
				$post_num++;
				$large_image_url = wp_get_attachment_image_src( $photo->ID, $image );
				$image_caption = $photo->post_excerpt;
			}
		?>
	    <li>
	    	<img src="<?php echo $large_image_url[0]; ?>">
            <?php if ($image_caption != '') {
            	echo '<p class="flex-caption">' . $image_caption . '</p>'; 
            } ?>
	    </li>
	<?php }	?>
    </ul>
</div>
