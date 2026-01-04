<?php
/*
 * Plugin Name: WooCommerce Mobile Money
 * Description: Recevez simplement des paiements via Mobile Money.
 * Author: Nehemie KOFFI
 * Author URI: https://nehemiekoffi.wordpress.com
 * Version: 1.1.0
 *
 */

add_filter( 'woocommerce_payment_gateways', 'mobilemoney_payment' );
function mobilemoney_payment( $gateways ) {
	$gateways[] = 'WC_MobileMoney_Payment_Gateway';
	return $gateways;
}

/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'mmpayment_display_admin_order_meta', 10, 1 );

function mmpayment_display_admin_order_meta($order){
    echo '<p><strong>'.__('Opérateur Mobile Money').':</strong> ' . get_post_meta( $order->get_id(), 'Operateur Mobile Money', true ) . '</p>';
    echo '<p><strong>'.__('Numéro Mobile Money').':</strong> ' . get_post_meta( $order->get_id(), 'Numero Mobile Money', true ) . '</p>';
    echo '<p><strong>'.__('ID transaction Mobile Money').':</strong> ' . get_post_meta( $order->get_id(), 'ID transaction Mobile Money', true ) . '</p>';
}

add_action( 'plugins_loaded', 'init_mobilemoney_payment' );
function init_mobilemoney_payment() {
 
	class WC_MobileMoney_Payment_Gateway extends WC_Payment_Gateway {
 
 		public function __construct() {

            $this->id = 'wc_mmpayment';
            $this->icon = plugins_url( 'mmoney-icons.png', __FILE__ );
            $this->has_fields = true;
            $this->method_title = 'Mobile Money Payment';
            $this->method_description = 'Payez à partir de votre compte mobile money';
         
            $this->supports = array(
                'products'
            );

            $this->init_form_fields();
            
            $this->init_settings();
            $this->title = $this->get_option( 'title' );
            $this->icon = $this->get_option( 'icon_url' ) != "" ? $this->get_option( 'icon_url' ) : $this->icon;
            $this->description = $this->get_option( 'description' );
            $this->enabled = $this->get_option( 'enabled' );
        
	        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );

 		}
 
		public function init_form_fields(){

            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Enable/Disable',
                    'label'       => 'Enable Mobile Money Payment',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default' => 'Mobile Money',
                    'desc_tip'    => true,
                ),
                'icon_url' => array(
                    'title'       => 'Icon URL',
                    'type'        => 'text',
                    'description' => "Lien de l'icone que l'utilisateur verra",
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default'     => 'Payez à partir de votre compte mobile money',
                ),
                'operator_1_name' => array(
                    'title'       => 'Operator #1 Name',
                    'type'        => 'text',
                    'description' => 'Enter the name of the first mobile money operator',
                    'default'     => 'Wave',
                    'desc_tip'    => true,
                ),
                'operator_1_phone' => array(
                    'title'       => 'Operator #1 Phone Number',
                    'type'        => 'text',
                    'description' => 'Enter the phone number for the first operator',
                    'default'     => '05000000',
                    'desc_tip'    => true,
                ),
                'operator_1_instruction' => array(
                    'title'       => 'Operator #1 Payment Instruction',
                    'type'        => 'text',
                    'description' => 'Enter the payment instruction for the first operator',
                    'default'     => 'Faites un transfert à partir de l\'application',
                    'desc_tip'    => true,
                ),
                'operator_2_name' => array(
                    'title'       => 'Operator #2 Name',
                    'type'        => 'text',
                    'description' => 'Enter the name of the second mobile money operator',
                    'default'     => 'MTN Money',
                    'desc_tip'    => true,
                ),
                'operator_2_phone' => array(
                    'title'       => 'Operator #2 Phone Number',
                    'type'        => 'text',
                    'description' => 'Enter the phone number for the second operator',
                    'default'     => '05000000',
                    'desc_tip'    => true,
                ),
                'operator_2_instruction' => array(
                    'title'       => 'Operator #2 Payment Instruction',
                    'type'        => 'text',
                    'description' => 'Enter the payment instruction for the second operator',
                    'default'     => '*133#',
                    'desc_tip'    => true,
                ),
                'operator_3_name' => array(
                    'title'       => 'Operator #3 Name',
                    'type'        => 'text',
                    'description' => 'Enter the name of the third mobile money operator',
                    'default'     => 'Orange Money',
                    'desc_tip'    => true,
                ),
                'operator_3_phone' => array(
                    'title'       => 'Operator #3 Phone Number',
                    'type'        => 'text',
                    'description' => 'Enter the phone number for the third operator',
                    'default'     => '07000000',
                    'desc_tip'    => true,
                ),
                'operator_3_instruction' => array(
                    'title'       => 'Operator #3 Payment Instruction',
                    'type'        => 'text',
                    'description' => 'Enter the payment instruction for the third operator',
                    'default'     => '#144#',
                    'desc_tip'    => true,
                ),
                'operator_4_name' => array(
                    'title'       => 'Operator #4 Name',
                    'type'        => 'text',
                    'description' => 'Enter the name of the fourth mobile money operator',
                    'default'     => 'YAS',
                    'desc_tip'    => true,
                ),
                'operator_4_phone' => array(
                    'title'       => 'Operator #4 Phone Number',
                    'type'        => 'text',
                    'description' => 'Enter the phone number for the fourth operator',
                    'default'     => '92000000',
                    'desc_tip'    => true,
                ),
                'operator_4_instruction' => array(
                    'title'       => 'Operator #4 Payment Instruction',
                    'type'        => 'text',
                    'description' => 'Enter the USSD code or payment instruction for the fourth operator',
                    'default'     => '*145#',
                    'desc_tip'    => true,
                ),
                'operator_5_name' => array(
                    'title'       => 'Operator #5 Name',
                    'type'        => 'text',
                    'description' => 'Enter the name of the fifth mobile money operator (leave empty if not needed)',
                    'default'     => '',
                    'desc_tip'    => true,
                ),
                'operator_5_phone' => array(
                    'title'       => 'Operator #5 Phone Number',
                    'type'        => 'text',
                    'description' => 'Enter the phone number for the fifth operator',
                    'default'     => '',
                    'desc_tip'    => true,
                ),
                'operator_5_instruction' => array(
                    'title'       => 'Operator #5 Payment Instruction',
                    'type'        => 'text',
                    'description' => 'Enter the USSD code or payment instruction for the fifth operator',
                    'default'     => '',
                    'desc_tip'    => true,
                )
            );
 
         }
         
         private function get_active_operators() {
             $operators = array();
             
             for ($i = 1; $i <= 5; $i++) {
                 $name = $this->get_option("operator_{$i}_name");
                 $phone = $this->get_option("operator_{$i}_phone");
                 $instruction = $this->get_option("operator_{$i}_instruction");
                 
                 if (!empty($name)) {
                     $operators[] = array(
                         'name' => $name,
                         'phone' => $phone,
                         'instruction' => $instruction
                     );
                 }
             }
             
             return $operators;
         }
 
		public function payment_fields() {

            global $woocommerce;
            $active_operators = $this->get_active_operators();

            echo 
            '<fieldset class="mm-payment-fieldset">
            <div class="mm-payment-header">
                <p class="mm-payment-amount">Montant à payer : <strong>'.$woocommerce->cart->get_cart_total().'</strong></p>
            </div>
            <div id="mm_operator_field" class="form-row form-row-wide mm-operator-field">
                <label>Sélectionnez votre opérateur <span class="required">*</span></label> 
                <select name="mm_operator" class="mm-operator-select">
                ';

                foreach ($active_operators as $operator) {
                    echo '<option value="' . esc_attr($operator['name']) . '">' . esc_html($operator['name']) . ' (' . esc_html($operator['phone']) . ')</option>';
                }
                
            echo '
                </select>
                <div id="mm_instruction" class="mm-instruction"></div>
            </div>
            <div class="form-row form-row-wide validate-required mm-input-field">
                <label>Numéro Mobile Money <abbr class="required" title="obligatoire">*</abbr></label>
                <input type="text" class="input-text mm-input" name="mm_sender_msisdn" placeholder="Ex: 90123456" value="">
            </div>
            <div class="form-row form-row-wide validate-required mm-input-field">
                <label>ID de la transaction <abbr class="required" title="obligatoire">*</abbr></label>
                <input type="text" autocomplete="off" class="input-text mm-input" name="mm_transaction_id" placeholder="Code de confirmation SMS" value="">
            </div>
            </fieldset>'; 
 
		}
 
	 	public function payment_scripts() {

            wp_enqueue_style('mmpayment_style', plugins_url( 'mobilemoney-payment.css', __FILE__ ));
            wp_register_script('mmpayment_js', plugins_url( 'mobilemoney-payment.js', __FILE__ ), array("jquery"), '1.1.0', true);
            wp_enqueue_script( 'mmpayment_js' );

            $active_operators = $this->get_active_operators();
            $operators_data = array();
            
            foreach ($active_operators as $operator) {
                $operators_data[$operator['name']] = $operator['instruction'];
            }

            wp_localize_script( 'mmpayment_js', 'mmpayment_data', array(
                'operators' => $operators_data
            ));

	 	}
 
		public function validate_fields() {
 
                if( empty( $_POST[ 'mm_sender_msisdn' ]) ) {
                    wc_add_notice(  'Le numéro de téléphone est obligatoire !', 'error' );
                    return false;
                }

                if( empty( $_POST[ 'mm_transaction_id' ]) ) {
                    wc_add_notice(  "Veuillez préciser l'ID de la transaction !", 'error' );
                    return false;
                }

                return true;
 
		}
 
		public function process_payment( $order_id ) {
            global $woocommerce;
            $order = new WC_Order( $order_id );

            $order->update_meta_data( 'Operateur Mobile Money', sanitize_text_field( $_POST['mm_operator'] ) );
            $order->update_meta_data( 'Numero Mobile Money', sanitize_text_field( $_POST['mm_sender_msisdn'] ) );
            $order->update_meta_data( 'ID transaction Mobile Money', sanitize_text_field( $_POST['mm_transaction_id'] ) );
        
            $order->update_status('on-hold', __( 'En attente de confirmation.', 'woocommerce' ));
            $woocommerce->cart->empty_cart();
        
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url( $order )
            );
	 	}

 	}
}