<?php
/**
 * @category	Solide Webservices
 * @package		Flexslider
 */
?>

<style type="text/css">
<?php 
$model = Mage::getModel('flexslider/group')->getCollection();
foreach ($model as $group) :
?>

	.flexslider-<?php echo $group['code'] ?> .sw-flexslider-direction-nav .sw-flexslider-prev:before, .flexslider-<?php echo $group['code'] ?> .sw-flexslider-direction-nav .sw-flexslider-next:before { color: <?php echo $group['nav_color'] ?>; }
	.flexslider-<?php echo $group['code'] ?> .sw-flexslider-control-paging li a { background-color: <?php echo $group['pagination_color'] ?>; }
	.flexslider-<?php echo $group['code'] ?> .loader-gutter {
		background-color: <?php echo $group['loader_bgcolor'] ?>;
		opacity: <?php echo $group['loader_opacity'] ?>;
	}
	.flexslider-<?php echo $group['code'] ?> .loader { background-color: <?php echo $group['loader_color'] ?>; }
	.flexslider-<?php echo $group['code'] ?> .slides .slider-title {
		background-color: rgba(<?php $this->helper('flexslider')->hex2rgb($group['caption_bgcolor']) ?>, <?php echo $group['caption_opacity'] ?>);
		color: <?php echo $group['caption_textcolor'] ?>;
	}

<?php if($group['theme']=='custom'){
	echo $group['custom_theme'];
} ?>
<?php if($group['type']=='overlay'){ ?>
	.overlay-nav {
		<?php if($group['overlay_position']=='right'){ echo 'right: 0'; } else { echo 'left: 0'; } ?>;
		background-color: rgba(<?php $this->helper('flexslider')->hex2rgb($group['overlay_bgcolor']) ?>, <?php echo $group['overlay_opacity'] ?>);
	}
	.overlay-nav .overlay-element h3, .overlay-nav .overlay-element .overlay-arrow:before { color: <?php echo $group['overlay_textcolor'] ?>; }
	.overlay-nav .overlay-element:hover, .overlay-nav .sw-flexslider-active { background-color: <?php echo $group['overlay_hovercolor'] ?>; }
<?php } ?>


<?php
endforeach;
?>
</style>