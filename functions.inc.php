<?php
/**
 * This file is used to hold general functions for the frontend
 */

/**
 * This function registrates javascripts for plugin usage
 */
function addScripts()
{
    global $translate;

    wp_enqueue_script('blockUI', plugins_url('js/jquery.blockUI.js' , __FILE__ ));
    wp_enqueue_script('creditsNumeric', plugins_url('js/jquery.numeric.js' , __FILE__ ));

    $params = array(
        'errorMessage'=>$translate->wooTranslate('insufficient', get_bloginfo('language')),
        'areYouSureMessage'=>$translate->wooTranslate('areYouSure', get_bloginfo('language')),
        'yes'=>$translate->wooTranslate('yes', get_bloginfo('language')),
        'no'=>$translate->wooTranslate('no', get_bloginfo('language')),
        'homeurl'=>get_home_url()
    );

    wp_register_script('woocommerceCredit', plugins_url('js/woocommerceCredit.js' , __FILE__ ));
    wp_localize_script('woocommerceCredit', 'params', $params); //pass any php settings to javascript
    wp_enqueue_script('woocommerceCredit'); //load the JavaScript file
}

/**
 * If user buys the bundles, this function is called to add credits
 */
 function check_thankyou($order_id)
 {
        global $post;
        global $wpdb;
        $alreadyRewarded = $wpdb->get_row("SELECT order_id FROM `" . $wpdb->prefix . "woocredit_orders` WHERE order_id=" . $order_id);
        $order = new WC_Order($order_id);
        $items = $order->get_items();
        if(count($items)>0 && !$alreadyRewarded) {
            $getUserCredit = 0;
            $getUserCredit = getUserCredit($order->user_id);
            $credits = 0;
            foreach($items as $item) {
                $credits += get_post_meta($item['product_id'],'_credits_amount',true)*$item['qty'];
            }
            $credits = $getUserCredit + $credits;
            if($getUserCredit==NULL) {
                $wpdb->query("INSERT INTO `".$wpdb->prefix."woocredit_users` (`user_id`, `credit`) VALUES ('".$order->user_id."', '".$credits."')");
            }
            if($getUserCredit!=NULL) {
                $wpdb->query("UPDATE `".$wpdb->prefix."woocredit_users` SET `credit` ='".$credits."' WHERE user_id=".$order->user_id);
            }
            $wpdb->query("INSERT INTO `".$wpdb->prefix."woocredit_orders` (`user_id`, `order_id`) VALUES ('".$order->user_id."', '".$order_id."')");
        }
 }

/**
 * Get current user balance
 */
function userCreditBalance()
{
    global $translate;
    if (is_user_logged_in()) {
        global $current_user;
        get_currentuserinfo();
        $getUserCredit = getUserCredit($current_user->ID);
        if(!$getUserCredit)
            $getUserCredit = 0;
        return $getUserCredit;
    } else {
        return $translate->wooTranslate('loginPlease', get_bloginfo('language'));
    }
}

/**
 * This function retrieves credits amount by user id
 * @param int $user_id
 * @return credits amount
 */
function getUserCredit($user_id)
{
    global $wpdb;
    $sql = $wpdb->get_row("SELECT credit FROM `".$wpdb->prefix."woocredit_users` where user_id=".$user_id);
    $res = "credit";
    if($sql->$res!=""):
    return $sql->$res;
    else:
    return null;
    endif;
}

/**
 * This function retrieves credits amount for all users
 * @return array of users and their credits:
 */
function getUserallCredit()
{
    global $wpdb;
    $sql = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."woocredit_users` ");
    if (!empty($sql)):
    return $sql;
    else:
    return array();
    endif;
}

/**
 * Get bought product statistics for x days
 * @param int $days
 * @return array products with data
 */
function getBoughtProducts($days=null)
{
    global $wpdb;
    $format = get_option('date_format');
    if($days>0) {
        $where = ' WHERE cp.date BETWEEN NOW() - INTERVAL '.$days.' DAY AND NOW() ';
    } else {
        $where = ' WHERE DATE(cp.date)=DATE(SUBDATE(NOW(),1))';
    }
    $sql = 'SELECT COUNT(cp.product_id) as count, SUM(cp.price) as price, cp.product_id as id, DATE(cp.date) as date, p.post_title as title
    FROM `'.$wpdb->prefix.'woocredit_products` as cp LEFT JOIN '.$wpdb->prefix.'posts as p ON cp.product_id=p.ID '.$where.'
    GROUP BY DATE(cp.date) ORDER BY date ASC';
    $result = $wpdb->get_results($sql);
    if(!empty($result)) {
        return $result;
    } else {
        return array();
    }
}

/**
 * This function is used to display the credit bundles
 */
function creditwoocommerceshortcode()
{
    global $translate;

    $args = array(
        'post_type' => 'product',
        'product_cat' => 'credit'
    );

    $output = "<table class='creditsTable'>";
    $output .="<tr>";
    $output .= "<th class='creditshead'>Credits</th>";
    $output  .= "<th class='creditshead'>".$translate->wooTranslate('Amount', get_bloginfo('language'))."</th>";
    $output  .= "<th>&nbsp;</th>";
    $output .="</tr>";
    $counter =1;
    $loop = new WP_Query( $args );

    while ( $loop->have_posts() )
    {
        $loop->the_post();
        global $product;

        $output .="<tr>";
        $output .="<td>".$loop->post->post_title."</td>";
        $output .="<td>". $product->get_price_html()." </td>";
        if (is_user_logged_in()) {
            $output .="<td><input class='btn btn-success' type='button' value='".$translate->wooTranslate('Buy Credits', get_bloginfo('language'))."' onclick='document.location.href = \"".do_shortcode('[add_to_cart_url id="'.$loop->post->ID.'"]')."\"'>";
            $output .= " </td>";
        } else {
            $loginMessage = $translate->wooTranslate('withoutloginMessage', get_bloginfo('language'));
            $output .='<td>'.$loginMessage.'</td>';
        }
        $output .="</tr>";
        $counter++;
    }

    wp_reset_postdata();

    echo $output .="</table>";
}

/**
 * Redirect to cart when user buys credits
 */
add_filter( 'woocommerce_add_to_cart_redirect', 'custom_add_to_cart_redirect' );
function custom_add_to_cart_redirect()
{
    $product_id = absint( $_REQUEST['add-to-cart'] );
    $product_cat_slug = '';

    $terms = get_the_terms( $product_id, 'product_cat' );
    $creditProduct = false;
        if($terms) {
        foreach ( $terms as $term ) {
            if(strcmp($term->slug, "credit")===0)
                $creditProduct = true;
            break;
        }
        }
    if($creditProduct)
        return get_permalink( get_option('woocommerce_cart_page_id') );
    else
        return remove_query_arg( 'add-to-cart' , get_permalink( $post->ID ));
}

/**
 * This function shows the buy for credit button on products page
 * @param int $attr
 */
function creditsbuybutton($attr)
{
    global $translate;
    global $product;

    extract(shortcode_atts(array(
        'class' => 'class',
        'title' => 'title',
    ), $attr));

    if($class!=='class')
        $class = 'class="'.$class.'"';
    if($title==='title')
        $title = $product->price.' '.$translate->wooTranslate('Credits', get_bloginfo('language'));
    return '<a '.$class.' href="javascript:void(0);" onclick="creditdeduct('.$product->id.','.$product->price.')" >'.$title.'</a>';
}

/**
 * This function shows the buy for credit button on products page
 * @param int $attr
 */
function single_product_buy_button($attr)
{
    global $translate;
    global $product;

    $class = '';

    extract(shortcode_atts(array(
        'class' => 'class',
        'title' => 'title',
    ), $attr));

    if($class!=='class') {
        $class = 'class="'.$class.'"';
    } else {
        $class = 'class="creditsBuyButton button"';
    }

    if($product->product_type=='variable') {
        $available_variations = $product->get_available_variations();
        if(!empty($available_variations)) {
            foreach($available_variations as $variation) {
                $variation = new WC_Product_Variation( $variation['variation_id'] );
                $title = $variation->get_price().' '.$translate->wooTranslate('Credits', get_bloginfo('language')).' - '.implode(', ',$variation->get_variation_attributes());
                echo '<a '.$class.' href="javascript:void(0);" onclick="creditdeduct('.$product->id.','.$variation->get_price().','.$variation->variation_id.')" >'.$title.'</a><br/>';
            }
        }
    } else {
        if($title==='title') {
            $title = $product->price.' '.$translate->wooTranslate('Credits', get_bloginfo('language'));
        }
        echo '<a '.$class.' href="javascript:void(0);" onclick="creditdeduct('.$product->id.','.$product->price.')" >'.$title.'</a>';
    }
}

/**
 * Return custom price override
 */
function return_custom_price() {
    return;
}

/**
 * Return product title override
 */
function product_title($cart_object) {
    global $product;
    echo $cart_object;
}

/**
 * Show bought products shortcode
 */
function show_bought_products() {
    global $wpdb;
    $user = get_current_user_id();
    if(isset($_POST["productSearchString"])) {
        $search = mysql_real_escape_string($_POST["productSearchString"]);
        $where = " AND LOWER(p.post_title) LIKE LOWER('%$search%')";
    }
    $sql = 'SELECT cp.user_id as user, DATE(date) as date, cp.product_id as id, p.post_title as title, cp.price as price
    FROM `'.$wpdb->prefix.'woocredit_products` as cp LEFT JOIN '.$wpdb->prefix.'posts  as p ON cp.product_id=p.ID
    WHERE cp.user_id='.$user.' '.$where.' ORDER BY date DESC LIMIT 20';
    $result = $wpdb->get_results($sql);
    ob_start();
    include_once('frontend/bought_products.php');
    return ob_get_clean();
}


/**
 * This function is used not to show the credit bundles among products
 * @param int $q
 */
function custom_pre_get_posts_query( $q )
{
    if(is_shop() && !is_admin()) {
        $q->set( 'tax_query', array(array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => array( 'credit' ), // Don't display products in the knives category on the shop page
            'operator' => 'NOT IN'
        )));
        remove_action( 'pre_get_posts', 'custom_pre_get_posts_query' );
    }
}

/**
 * Add fields to product to save credits price
 */
function woo_add_custom_general_fields()
{
    global $woocommerce, $post;
    $terms = wp_get_post_terms( $post->ID, 'product_cat' );
    foreach ( $terms as $term ) $categories[] = $term->slug;
    if(!empty($categories) && in_array('credit',$categories)) {
        echo '<div class="options_group">';
        woocommerce_wp_text_input(
            array(
                'id'          => '_credits_amount',
                'label'       => __( 'Credits Amount', 'woocommerce' ),
                'placeholder' => '0',
                'desc_tip'    => 'true',
                'description' => __( 'Enter here the amount of credits for this bundle.', 'woocommerce' ),
                'type'              => 'number',
                'custom_attributes' => array(
                    'step'     => 'any',
                    'min'    => '0'
                )
            )
        );
        echo '</div>';
    }
}


/**
 * Save custom product fields
 * @param unknown_type $post_id
 */
function woo_add_custom_general_fields_save( $post_id )
{
    $woocommerce_credits_amount = $_POST['_credits_amount'];
    if(!empty( $woocommerce_credits_amount ) )
        update_post_meta( $post_id, '_credits_amount', esc_attr( $woocommerce_credits_amount ) );
}
?>
