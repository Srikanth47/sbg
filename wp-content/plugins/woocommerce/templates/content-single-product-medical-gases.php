<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
    <?php
	/**
	 * woocommerce_before_single_product hook.
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>
        <?php
	$classes = array(
		'tab-pane',
		'class3',
	);
?>
<div class="tab-content">
	<div class="process">
		<div class="process-row nav nav-tabs">
			<span class="order_tlt">ORDER FORM</span>
			<div class="process-step">
				<a href="<?php echo home_url(); ?>">
					<button type="button" class="btn btn-default" data-toggle="tab" href="#compliancy-menu1">GET STARTED</button>
				</a>
			</div>
			<div class="process-step">
				<button type="button" class="btn btn-info" data-toggle="tab" href="#compliancy-menu2">TESTS</button>
			</div>
			<div class="process-step">
				<button type="button" class="btn btn-default" data-toggle="tab" href="#compliancy-menu3">
					CANISTERS </button>
			</div>
			<div class="process-step">
				<button type="button" class="btn btn-default" data-toggle="tab" href="#compliancy-menu4">SUMMARY</button>
			</div>
			<div class="process-step">
				<button type="button" class="btn btn-default" data-toggle="tab" href="#compliancy-menu5">SHIPPING</button>
			</div>
			<div class="process-step">
				<button type="button" class="btn btn-default" data-toggle="tab" href="#compliancy-menu6">PAYMENT</button>
			</div>
		</div>
	</div>
	<!--get started open here-->

	<!--get started close here-->

	<!--tests open here-->

	<div id="compliancy-menu2" class="tab-pane fade active in">
		<h2>Your require the following compliancy testing, please confirm</h2>
		<div class="inner_text">
			<p class="inner_main_head">standard compliancy testing is require every six month, etc..</p>

			<div class="row">
				<div class="col-md-2 cbg-tests">
					<h3>you are in</h3>
					<i class="fa fa-stack-exchange" aria-hidden="true"></i>
					<?php
						$args = array(
								'delimiter' => '<br/><br/>',
								'before' => '',
								'after'   => '',
								'home'    => null
									);
						?>

						<p class="item-heading">
							<?php woocommerce_breadcrumb( $args ); ?>
						</p>
				</div>

				<div class="col-md-10">
					<?php the_title( '<h1 class="product_title entry-title">', '</h1>' ); ?>
						<?php global $post;
							if ( ! $post->post_excerpt ) {
							return;
							}
						?>
				<div class="woocommerce-product-details__short-description">
					<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
				</div>
			<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

					<?php
					/**
					* Loop Add to Cart -- with quantity and AJAX
					* requires associated JavaScript file qty-add-to-cart.js
					*
					* @link http://snippets.webaware.com.au/snippets/woocommerce-add-to-cart-with-quantity-and-ajax/
					* @link https://gist.github.com/mikejolley/2793710/
					*/
					// add this file to folder "woocommerce/loop" inside theme
					global $product;
					if( $product->get_price() === '' && $product->product_type != 'external' ) return;
					// script for add-to-cart with qty
					wp_enqueue_script('qty-add-to-cart', get_stylesheet_directory_uri() . '/js/qty-add-to-cart.js', array('jquery'), '1.0.1', true);
					?>
					<?php if ( ! $product->is_in_stock() ) : ?>

					<a href="<?php echo get_permalink($product->id); ?>" class="button">
						<?php echo apply_filters('out_of_stock_add_to_cart_text', __('Read More', 'woocommerce')); ?>
					</a>

					<?php else : ?>
						<?php
						switch ( $product->product_type ) {
							case "variable" :
								$link   = get_permalink($product->id);
								$label  = apply_filters('variable_add_to_cart_text', __('Select options', 'woocommerce'));
							break;
							case "grouped" :
								$link   = get_permalink($product->id);
								$label  = apply_filters('grouped_add_to_cart_text', __('View options', 'woocommerce'));
							break;
							case "external" :
								$link   = get_permalink($product->id);
								$label  = apply_filters('external_add_to_cart_text', __('Read More', 'woocommerce'));
							break;
							default :
								$link   = esc_url( $product->add_to_cart_url() );
								$label  = apply_filters('add_to_cart_text', __('Add to cart', 'woocommerce'));
							break;
						}
						//printf('<a href="%s" rel="nofollow" data-product_id="%s" class="button add_to_cart_button product_type_%s">%s</a>', $link, $product->id, $product->product_type, $label);
						if ( $product->product_type == 'simple' ) {
						?>
							<form action="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="cart" method="post" enctype='multipart/form-data'>
											<?php woocommerce_quantity_input(); ?>

												<button type="submit" data-quantity="1" data-product_id="<?php echo $product->id; ?>" class="button alt ajax_add_to_cart add_to_cart_button product_type_simple">
													<?php echo $label; ?>
												</button>

							</form>
								<?php
								} else {
									printf('<a href="%s" rel="nofollow" data-product_id="%s" class="button add_to_cart_button product_type_%s">%s</a>', $link, $product->id, $product->product_type, $label);
								}
								?>

				<?php endif; ?>

        <button type="button" class="btn btn-order next-step">Next<i class="fa fa-angle-right" aria-hidden="true"></i></button>
            </div>
			</div>
        </div>
    </div>

                <!--tests close here-->

                <!--consisters open here

		<div id="compliancy-menu3" class="tab-pane fade">
			<h2>Choose the type of cylinder you need</h2>
			<?php //echo do_shortcode('[woocommerce_product_upsells]'); ?>
			<button type="button" class="btn btn-order next-step" disabled>Next<i class="fa fa-angle-right" aria-hidden="true"></i></button>
		</div>-->

                <!--consisters close here-->

                <!--summery open here-->

		<div id="compliancy-menu4" class="tab-pane fade">
			<h2>Please confirm your order</h2>
			<?php echo do_shortcode('[woocommerce_cart]'); ?>
				<button type="button" class="btn btn-order next-step checkout pull-right">checkout<i class="fa fa-angle-right" aria-hidden="true"></i></button>
		</div>

                <!--summery close here-->

                <!--shipping open here-->

		<div id="compliancy-menu5" class="tab-pane fade">

			<h2>Please enter your contact info and shipping address</h2>
			<?php echo do_shortcode('[woocommerce_checkout]'); ?>
				<button type="button" class="btn btn-order next-step pull-right">Next<i class="fa fa-angle-right" aria-hidden="true"></i></button>
		</div>

                <!--shipping close here-->

                <!--payment open here-->

		<div id="compliancy-menu6" class="tab-pane fade">

			<h2>Please enter your method of payment</h2>

		</div>

                <!--payment close here-->

</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>