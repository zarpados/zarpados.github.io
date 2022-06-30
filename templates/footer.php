<?php global $ss_framework, $ss_settings; ?>
<?php if ( is_footer_cta_active() ) { ?>
	<div class="footer-cta">
		<?php echo $ss_framework->open_container( 'div' ); ?>
			<? echo $ss_settings['footer_cta_content']; ?>
		<?php echo $ss_framework->close_container( 'div' ); ?>
	</div>
<?php } ?>
<footer id="page-footer" class="content-info" role="contentinfo">
	<?php echo $ss_framework->open_container( 'div' ); ?>
		<?php shoestrap_footer_content(); ?>
	<?php echo $ss_framework->close_container( 'div' ); ?>
</footer>