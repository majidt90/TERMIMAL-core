<?php
/**
 * Title: Product Hero
 * Slug: termimal/product-hero
 * Categories: termimal
 * Description: Hero section for a product with title, excerpt and CTA.
 * Block Types: core/post-content
 * Post Types: termimal_product
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10","left":"var:preset|spacing|6","right":"var:preset|spacing|6"}}},"backgroundColor":"gray-950","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-gray-950-background-color has-background" style="padding-top:var(--wp--preset--spacing--10);padding-right:var(--wp--preset--spacing--6);padding-bottom:var(--wp--preset--spacing--10);padding-left:var(--wp--preset--spacing--6)">

	<!-- wp:post-title {"textAlign":"center","level":1,"fontSize":"5xl","fontFamily":"display"} /-->

	<!-- wp:post-excerpt {"textAlign":"center","moreText":"","excerptLength":30,"textColor":"gray-400","fontSize":"lg"} /-->

	<!-- wp:spacer {"height":"24px"} -->
	<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"backgroundColor":"primary","textColor":"black","style":{"border":{"radius":"8px"}}} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-black-color has-primary-background-color has-text-color has-background wp-element-button" style="border-radius:8px">View details</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->

</div>
<!-- /wp:group -->
