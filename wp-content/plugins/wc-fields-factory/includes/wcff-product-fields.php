<?php 

if (!defined('ABSPATH')) { exit; }

/**
 * 
 * One of the core module, which is responsible for the registering necessary hooks for the lifecycle of<br><br>
 * 1. Injecting Fields on Single Product Page<br>
 * 2. Add To Cart handler<br>
 * 3. Rendering Fields on Cart & Checkout Page<br>
 * 4. Edit fields on Cart Page<br>
 * 5. Pricing & Fee handler<br>
 * 6. Order Meta Handler
 *
 * @author 	    : Saravana Kumar K
 * @copyright   : Sarkware Pvt Ltd
 *
 */
class Wcff_ProductFields {
	
	public function __construct() {		
		add_action("init", array($this, 'registerHooks'));
	}
	
	public function registerHooks() {
		$wccpf_options = wcff()->option->get_options();
		$fields_cloning = isset($wccpf_options["fields_cloning"]) ? $wccpf_options["fields_cloning"] : "no";
		$show_custom_data = isset( $wccpf_options["show_custom_data"] ) ? $wccpf_options["show_custom_data"] : "yes";
		$group_fields_on_cart = isset( $wccpf_options["group_fields_on_cart"] ) ? $wccpf_options["group_fields_on_cart"] : "no";
		$cart_editable = isset($wccpf_options["edit_field_value_cart_page"]) ? $wccpf_options["edit_field_value_cart_page"] : "no";
		$fields_location = isset($wccpf_options["field_location"]) ? $wccpf_options["field_location"] : "woocommerce_before_add_to_cart_button";
		
		/** STEP 1 **/
		
		/* Register handler for injecting custom fields into the single product page */
		if ($fields_location != "woocommerce_single_product_tab") {
			add_action($fields_location, array($this, 'fields_injector'));
		} else {
			/* If admin wants to inject the custom fields on a seperate tab ( on the single product page ) */
			add_filter('woocommerce_product_tabs', array($this, 'inject_fields_tab'));
		}
		
		/** STEP 2 **/
		
		/* Regsiter validation handler for add to cart action */
		add_filter('woocommerce_add_to_cart_validation', array( $this, 'fields_validator' ), 99, 2);
		
		/** STEP 3 **/
		
		/* Register handler for handling add to cart action, this is where all the custom fields
		 * that is being submitted by the users will be persisted */
		add_filter('woocommerce_add_cart_item_data', array( $this, 'fields_persister' ), 10, 2);
		
		/** STEP 4 **/
		
		/* Register handler for rendering custom field on cart page
		 * Before that make sure admin wants to display the data on Cart & Checkout */
		if ($show_custom_data == "yes") {
			if ($fields_cloning == "yes" || $cart_editable == "yes") {
				/* If this is the case then we are responsible for rendering custom field data
				 * into the cart and checkout */
				add_filter('woocommerce_cart_item_name', array($this, 'fields_cloning_cart_handler'), 999, 3);
				add_filter('woocommerce_checkout_cart_item_quantity', array($this, 'fields_cloning_checkout_handler'), 999, 3);
			} else {
				/* Here we are using woocommerce default line item attribute renser method
				 * Just have supply the field's key value, rest will be handled by the woocommerec itself */
				add_filter('woocommerce_get_item_data', array($this, 'cart_data_handler'), 999, 2);
			}
		}
		
		/** STEP 5 **/
		
		/* Register handler for Pricing rules */
		add_action('woocommerce_before_calculate_totals', array($this, 'pricing_rules_handler'), 999);
		
		/* Register handler for Fee rules */
		add_action('woocommerce_cart_calculate_fees', array($this, 'fee_rules_handler'), 999);
		
		/** STEP 6 **/
		
		/* WC 3.0.6 update */
		if (version_compare(WC()->version, '3.0.0', '<')) {
			add_action('woocommerce_add_order_item_meta', array($this, 'fields_order_meta_handler'), 99, 3);
		} else {
			add_action('woocommerce_new_order_item', array($this, 'fields_order_meta_handler'), 99, 3);
		}
	}
	
	
	public function fields_injector() {	
	    /* Inject the custom fields into the single product page */
	    wcff()->injector->inject();
	}
	
	public function inject_fields_tab($_tabs=array()) {
	    $wccpf_options = wcff()->option->get_options();
	    $_tabs['wccpf_fields_tab'] = array(
	        'title' => $wccpf_options["product_tab_title"],
	        'priority' => $wccpf_options["product_tab_priority"],
	        'callback' => array($this, 'fields_injector')
	    );
	    return $_tabs;
	}
	
	/**
	 * 
	 * Call the validation module to perform validation on Product as well as Admin Fields
	 * 
	 * @param boolean $_passed
	 * @param integer $_pid
	 * @return boolean
	 * 
	 */
	public function fields_validator($_passed, $_pid = null) {
		$is_ok = true;
	    /* Delegate the task to Validation module */
		if(!wcff()->validator->validate($_pid, $_passed)) {
			$is_ok = false;
			WC()->session->set("wcff_validation_failed", true);
		}
		return $is_ok;		
	}
	
	public function fields_persister($_cart_item_data, $_product_id) {
	    /* Delegate the task to Persister module */
	    return wcff()->persister->persist($_cart_item_data, $_product_id);
	}
	
	public function pricing_rules_handler($_cart = null) {
		wcff()->negotiator->handle_custom_pricing($_cart);
	}
	
	public function fee_rules_handler($_cart = null) {
		wcff()->negotiator->handle_custom_fee($_cart);
	}
	
	public function cart_data_handler($_cart_data, $_cart_item = null) {
	    return wcff()->renderer->render_fields_data($_cart_data, $_cart_item);
	}
	
	public function fields_cloning_cart_handler($_title = null, $_cart_item = null, $_cart_item_key = null) {
		if (is_cart()) {
			return wcff()->editor->render_fields_data($_title, $_cart_item, $_cart_item_key, false);
		}
	    return $_title;
	}
	
	public function fields_cloning_checkout_handler($_quantity = null, $_cart_item = null, $_cart_item_key = null) {
	    return wcff()->editor->render_fields_data($_quantity, $_cart_item, $_cart_item_key, true);
	}
	
	public function fields_order_meta_handler($_item_id, $_values, $_cart_item_key) {
	    wcff()->order->insert($_item_id, $_values, $_cart_item_key);
	}
}

new Wcff_ProductFields();

?>