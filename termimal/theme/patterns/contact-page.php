<?php
/**
 * Title: Contact TERMIMAL
 *Slug: termimal/contact-page
 * Categories: termimal
 * Description: Contact hero plus contact form shortcode.
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|6","left":"var:preset|spacing|6","right":"var:preset|spacing|6"}}},"backgroundColor":"gray-950","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-gray-950-background-color has-background" style="padding-top:var(--wp--preset--spacing--10);padding-right:var(--wp--preset--spacing--6);padding-bottom:var(--wp--preset--spacing--6);padding-left:var(--wp--preset--spacing--6)">
	<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"4xl","fontFamily":"display"} -->
	<h1 class="wp-block-heading has-text-align-center has-display-font-family has-4-xl-font-size">Contact</h1>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"align":"center","textColor":"gray-400","fontSize":"lg"} -->
	<p class="has-text-align-center has-gray-400-color has-text-color has-lg-font-size">Questions about our products, partnerships, or demos — send a message.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|6","bottom":"var:preset|spacing|12","left":"var:preset|spacing|6","right":"var:preset|spacing|6"}}},"layout":{"type":"constrained","contentSize":"560px"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--6);padding-right:var(--wp--preset--spacing--6);padding-bottom:var(--wp--preset--spacing--12);padding-left:var(--wp--preset--spacing--6)">
	<!-- wp:shortcode -->
	[termimal_contact]
	<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
