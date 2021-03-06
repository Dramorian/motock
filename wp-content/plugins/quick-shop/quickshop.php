<?php
/*
Plugin Name: QuickShop
Plugin URI: http://www.zackdesign.biz/wp-plugins/34
Description: Quick and easy shopping cart with widget!
Author: Isaac Rowntree
Version: 1.3.12
Author URI: http://zackdesign.biz

	Copyright (c) 2005, 2006 Isaac Rowntree (http://zackdesign.biz)
	QuickShop is released under the GNU General Public
	License (GPL) http://www.gnu.org/licenses/gpl.txt

	This is a WordPress plugin (http://wordpress.org).

*/


session_start();

// Check for WP2.5 installation
define('IS_WP25', version_compare($wp_version, '2.4', '>=') );

// define URL
$myabspath = str_replace("\\","/",ABSPATH);  // required for Windows & XAMPP
define('WINABSPATH', $myabspath);
define('QSFOLDER', dirname(plugin_basename(__FILE__)));
define('QS_ABSPATH', $myabspath.'wp-content/plugins/' . QSFOLDER .'/');
define('QS_URLPATH', get_option('siteurl').'/wp-content/plugins/' . QSFOLDER.'/');


if ($_POST['addcart'])
{
    $count = 1;    
    $products = $_SESSION['qscart'];
    
    if (is_array($products))
    {
        foreach ($products as $key => $item)
        {
            if ($item['name'] == $_POST['product'])
            {
                $count += $item['quantity'];
                $item['quantity']++;
                unset($products[$key]);
                array_push($products, $item);
            }
        }
    }
    else
        $products = array();
        
    if ($count == 1)
    {
        if (!empty($_POST[$_POST['product']]))
            $price = $_POST[$_POST['product']];
        else
            $price = $_POST['price'];
        
        $product = array('name' => stripslashes($_POST['product']), 'price' => $price, 'quantity' => $count, 'shipping' => $_POST['shipping'], 'shipping2' => $_POST['shipping2'], 'qslink' => $_POST['qslink'], 'item_number' => $_POST['item_number']);
        array_push($products, $product);
    }
    
    sort($products);
    $_SESSION['qscart'] = $products;
}
else if ($_POST['cquantity'])
{
    $products = $_SESSION['qscart'];
    foreach ($products as $key => $item)
    {
        if (($item['name'] == $_POST['product']) && $_POST['quantity'])
        {
            $item['quantity'] = $_POST['quantity'];
            unset($products[$key]);
            array_push($products, $item);
        }
        else if (($item['name'] == $_POST['product']) && !$_POST['quantity'])
            unset($products[$key]);
    }
    sort($products);
    $_SESSION['qscart'] = $products;
}
else if ($_POST['delcart'])
{
    $products = $_SESSION['qscart'];
    foreach ($products as $key => $item)
    {
        if ($item['name'] == $_POST['product'])
            unset($products[$key]);
    }
    $_SESSION['qscart'] = $products;
}
else if ($_POST['qstc'])
    $_SESSION['qstc'] = $_POST['qstc'];


function widget_quickshop($args)
{
    extract($args);
	
	if (cart_has_items())
    {
    $defaultSymbol = get_option('quickshop_symbol');
    $defaultDecimal = get_option('quickshop_decimal');
    $defaultChURL = get_option('quickshop_churl');
    if (empty($defaultChURL))
        $defaultChURL = '/contact';
    if (!empty($defaultCurrency))
        $currency = $defaultCurrency;
    else
        $currency = 'USD';
    if (!empty($defaultSymbol))
        $symbol = $defaultSymbol;
    else
        $symbol = '$';
    if (!empty($defaultDecimal))
        $decimal = $defaultDecimal;
    else
        $decimal = '.';
        
    $freeshipv = get_option('quickshop_freeshipv');
    if (empty($freeshipv)) $freeshipv = '0';
    $freeshipt = get_option('quickshop_freeshipt');
    if (empty($freeshipt)) $freeshipt = 'Shipping is free for orders of $200 and upwards.';

    $title = get_option('quickshop_title');
	  if (empty($title)) $title = 'Ваш заказ';
	
	  
    
    echo $before_widget;
    echo '<div class="quickshopcart" style="padding: 5px;">';
    echo $before_title . $title . $after_title;
    
    echo '<br /><span id="info" style="display: none; font-weight: bold; color: red;">Нажмите Enter или Tab, чтобы пересчитать сумму заказа.</span><form method="post" action=""><table style="width: 100%;">';
    
    $count = 1;
    $total = 0;
    if ($_SESSION['qscart'] && is_array($_SESSION['qscart']))
    {
    
                echo '
        <tr>
        <th>Наим-е</th><th>Кол-во</th><th>Цена</th>
        </tr>';
    
    foreach ($_SESSION['qscart'] as $item)
    {
        echo "     
        
        <tr><td style='overflow: hidden;'><a href='".$item['qslink']."'>".$item['name']."</a></td>
        <td style='text-align: center'><form method=\"post\" name='cquantity'  action=\"\" style='display: inline'>
        <input type='hidden' name='product' value='".$item['name']."' />
        
        <input type='hidden' name='cquantity' value='1' /><input type='text' name='quantity' value='".$item['quantity']."' size='1' onchange='document.cquantity.submit();' onkeypress='document.getElementById(\"info\").style.display = \"\";' /></form></td>
        <td style='text-align: center'>".output_currency(($item['price'] * $item['quantity']),$symbol, $decimal)."</td>
        <td><form method=\"post\"  action=\"\">
        <input type='hidden' name='product' value='".$item['name']."' />
        <input type='hidden' name='delcart' value='1' />
        <input type='image' src='".get_bloginfo('wpurl')."/wp-content/plugins/quick-shop/images/cart_remove.png' value='Remove' title='Remove' /></form></td></tr>
        ";
        $total += $item['price'] * $item['quantity'];
        $count += $item['quantity'];
        
        if ($item['shipping2'])
            $postage += (($item['quantity'] - 1) * $item['shipping2']) + $item['shipping'];
        else
            $postage += $item['quantity'] * $item['shipping'];
    }
    }
    
    $count--;
    
       if ($count)
       {
              echo '
       <tr><td></td><td></td><td></td></tr>';
       
       if (!get_option('quickshop_total')) {
            
            if ($freeshipv && ($freeshipv <= $total))
                $postage = 0;
            
            echo"
<!--       <tr><td colspan='2' style='font-weight: bold; text-align: right;'>Заказ: </td><td style='text-align: center'>".output_currency($total, $symbol, $decimal)."</td><td></td></tr>
       <tr><td colspan='2' style='font-weight: bold; text-align: right;'>Доставка: </td><td style='text-align: center'>".output_currency($postage, $symbol, $decimal)."</td><td></td></tr> -->";
       }
       echo "<tr><td colspan='2' style='font-weight: bold; text-align: right;'>Всего: </td><td style='text-align: center'>".output_currency($total + $postage, $symbol, $decimal)."</td><td></td></tr>
       <tr><td colspan='4'>
       
       ";
          
          $terms = get_option('quickshop_tc');
          if (($_SESSION['qstc'] != true) && ($terms != '') && $terms)
          {
              $text = get_option('quickshop_tct');
              $st = get_option('quickshop_tcst');
              if (empty($text))
                  $text = 'Вы согласны с нашими ценами и условиями?';
              if (empty($st))
                  $st = 'Да';
              echo '<a href="'.$terms.'" target="_blank">'.$text.'</a>
              <form method="post" action=""><input type="hidden" value="true" name="qstc"><input type="submit" value="'.$st.'"></form>
              ';
          }
          else 
              echo "<p  style='font-weight: bold'><a href='".get_bloginfo('url').'/'.$defaultChURL."'>Перейти к оформлению <img src='".get_bloginfo('wpurl')."/wp-content/plugins/quick-shop/images/cart_go.png' style='border: 0px;' title='Оформление заказа' alt='Оформление заказа' /></a></p>";
       }
       
       echo "
       
       </td></tr>
    </table><input type='hidden' value='$currency' name='currency'> <!-- This is here for your use with a custom form -->
    ";
    
    if ($freeshipv)
        echo $freeshipt;
    echo '</div>';
    
    echo $after_widget;
    
    }
	else
	{
	    $display  = get_option('quickshop_display');
		if ($display)
		{
		    $displayt = get_option('quickshop_displayt');
			$title = get_option('quickshop_title');
			if (empty($title)) $title = 'Ваш заказ';
		    if (empty($displayt)) $displayt = 'Пока вы ничего не заказали';
		    echo $before_widget.'<div class="quickshopcart" style="padding: 5px;">'.$before_title . $title . $after_title.'<p>'.$displayt.'</p></div>'.$after_widget;
		}
	}
}

function widget_quickshoppaypal($args)
{
    extract($args);
	
	if (cart_has_items())
    {
    
    $email = get_bloginfo('admin_email');
       
    $defaultCurrency = get_option('quickshop_currency');
    $defaultSymbol = get_option('quickshop_symbol');
    $defaultDecimal = get_option('quickshop_decimal');
    $defaultEmail = get_option('quickshop_pemail');
    if (!empty($defaultCurrency))
        $paypal_currency = $defaultCurrency;
    else
        $paypal_currency = 'USD';
    if (!empty($defaultSymbol))
        $paypal_symbol = $defaultSymbol;
    else
        $paypal_symbol = '$';
    if (!empty($defaultDecimal))
        $decimal = $defaultDecimal;
    else
        $decimal = '.';
    if (!empty($defaultEmail))
        $email = $defaultEmail;
    
    $freeshipv = get_option('quickshop_freeshipv');
    if (empty($freeshipv)) $freeshipv = '0';
    $freeshipt = get_option('quickshop_freeshipt');
    if (empty($freeshipt)) $freeshipt = 'Shipping is free for orders of $200 and upwards.';
	  
	      $urls = '';
        
        $notify = get_option('quickshop_pnurl');
        $return = get_option('quickshop_prurl');
        
        if (!empty($notify))
            $urls .= '<input type="hidden" name="notify_url" value="'.$notify.'" />';
            
        if (!empty($return))
            $urls .= '<input type="hidden" name="return" value="'.$return.'" />';
	  
	$title = get_option('quickshop_title');
	if (empty($title)) $title = 'Ваш заказ';
    
    echo $before_widget;
    echo '<div class="quickshopcart" style=" padding: 5px;">';
    echo $before_title . $title . $after_title;
    
    echo '<br /><span id="pinfo" style="display: none; font-weight: bold; color: red;">Нажмите Enter, чтобы пересчитать заказ.</span><table style="width: 100%;">';
    
    $count = 1;
    $total_items = 0;
    $total = 0;
    $form = '';
    if ($_SESSION['qscart'] && is_array($_SESSION['qscart']))
    {
    
        
                echo '
        <tr>
        <th>Наим-е</th><th>Кол-во</th><th>Цена</th>
        </tr>';
    
    foreach ($_SESSION['qscart'] as $item)
    {
        $total += $item['price'] * $item['quantity'];
        
        $total_items +=  $item['quantity'];
        if ($item['shipping2'])
            $postage += (($item['quantity'] - 1) * $item['shipping2']) + $item['shipping'];
        else
            $postage += $item['quantity'] * $item['shipping'];
    }
    
    foreach ($_SESSION['qscart'] as $item)
    {
        echo "  
       
        
        <tr><td style='overflow: hidden;'><a href='".$item['qslink']."'>".$item['name']."</a></td>
        <td style='text-align: center'><form method=\"post\"  action=\"\" name='pcquantity' style='display: inline'>
        <input type='hidden' name='product' value='".$item['name']."' />
        
        <input type='hidden' name='cquantity' value='1' /><input type='text' name='quantity' value='".$item['quantity']."' size='1' onchange='document.pcquantity.submit();' onkeypress='document.getElementById(\"pinfo\").style.display = \"\";' /></form></td>
        <td style='text-align: center'>".output_currency(($item['price'] * $item['quantity']), $paypal_symbol, $decimal)."</td>
        <td><form method=\"post\"  action=\"\">
        <input type='hidden' name='product' value='".$item['name']."' />
        <input type='hidden' name='delcart' value='1' />
        <input type='image' src='".get_bloginfo('wpurl')."/wp-content/plugins/quick-shop/images/cart_remove.png' value='Убрать' title='Убрать' /></form></td></tr>
        
        ";
        
        $form .= "
            <input type=\"hidden\" name=\"item_name_$count\" value=\"".$item['name']."\" />
            <input type=\"hidden\" name=\"amount_$count\" value='".$item['price']."' />
            <input type=\"hidden\" name=\"quantity_$count\" value=\"".$item['quantity']."\" />
            <input type='hidden' name='item_number' value='".$item['item_number']."' />
        ";
        if ($freeshipv && ($freeshipv <= $total))
            $form .= "<input type=\"hidden\" name=\"shipping_$count\" value=\"0\" />";
        else if (!empty($item['shipping2']))
            $form .= "<input type=\"hidden\" name=\"shipping_$count\" value=\"".$item['shipping']."\" /><input type=\"hidden\" name=\"shipping2_$count\" value=\"".$item['shipping2']."\" />";
        else
            $form .= "<input type=\"hidden\" name=\"shipping_$count\" value=\"".($item['shipping'] * $item['quantity'])."\" />";
        $count++;
    }
    }
    
        $count--;
    
       if ($count)
       {
           echo '<tr><td></td><td></td><td></td></tr>';
       
          if (!get_option('quickshop_total')) {
          if ($freeshipv && ($freeshipv <= $total))
                $postage = 0;
              echo "
   <!--    <tr><td colspan='2' style='font-weight: bold; text-align: right;'>Заказ: </td><td style='text-align: center'>".output_currency($total, $paypal_symbol, $decimal)."</td><td></td></tr>
       <tr><td colspan='2' style='font-weight: bold; text-align: right;'>Доставка: </td><td style='text-align: center'>".output_currency($postage, $paypal_symbol, $decimal)."</td><td></td></tr> -->";
       }
       echo "
       <tr><td colspan='2' style='font-weight: bold; text-align: right;'>Всего: </td><td style='text-align: center'>".output_currency(($total + $postage), $paypal_symbol, $decimal)."</td><td></td></tr>
       <tr><td colspan='4'>";
       
          $terms = get_option('quickshop_tc');
          if (($_SESSION['qstc'] != true) && ($terms != '') && $terms)
          {
              $text = get_option('quickshop_tct');
              $st = get_option('quickshop_tcst');
              if (empty($text))
                  $text = 'Вы согласны с ценами и условиями?';
              if (empty($st))
                  $st = 'Да';
              echo '<a href="'.$terms.'" target="_blank">'.$text.'</a>
              <form method="post" action=""><input type="hidden" value="true" name="qstc"><input type="submit" value="'.$st.'"></form>
              ';
          }
          else
          {
              echo "<form action=\"https://www.paypal.com/us/cgi-bin/webscr\" method=\"post\">$form";
    if ($count)
            echo '<input type="image" src="'.get_bloginfo('wpurl').'/wp-content/plugins/quick-shop/images/btn_xpressCheckout.gif" name="submit" alt="Make payments with PayPal - it\'s fast, free and secure!" />';
    
    if ($freeshipv)
        echo $freeshipt;
    
    echo $urls.'
    <input type="hidden" name="business" value="'.$email.'" />
    <input type="hidden" name="currency_code" value="'.$paypal_currency.'" />
    <input type="hidden" name="cmd" value="_cart" />
    <input type="hidden" name="upload" value="1" />
    </form>';
          }
          
       }
       
       echo "
       
       </td></tr>
    </table></div>
    ";
    
    echo $after_widget;

    }
	else
	{
	    $display  = get_option('quickshop_display');
		if ($display)
		{
		    $displayt = get_option('quickshop_displayt');
			$title = get_option('quickshop_title');
			if (empty($title)) $title = 'Ваш заказ';
		    if (empty($displayt) ) $displayt = 'Пока вы ничего не заказали';
		    echo $before_widget.'<div class="quickshopcart" style="padding: 5px;">'.$before_title . $title . $after_title.'<p>'.$displayt.'</p></div>'.$after_widget;
		}
	}
}

function quickshop_widgets()
{

    register_sidebar_widget('QuickShop', 'widget_quickshop');
    
    register_sidebar_widget('QuickShop Paypal', 'widget_quickshoppaypal');

}

function quickshop_button_post($content)
{
           
    if (!get_option('quickshop_logged') || is_user_logged_in())
    {    
    
        $addcart = get_option('quickshop_addcart');
    
        if (!$addcart || ($addcart == '') )
            $addcart = 'Add to Cart';


        /*** format ***/
        $pattern_format = '#:format:.+:end#';
        preg_match_all ($pattern_format, $content, $matches_format);
        //print_r($matches_format);

        $format_price = array();
        foreach ($matches_format[0] as $list){
            $pattern_format = ':format:';
            $format_price_buf = str_replace ($pattern_format, '', $list);
            $pattern_format = ':end';
            $format_price[] = str_replace ($pattern_format, '', $format_price_buf);
        }
        //print_r($format_price);
        //echo $format_price;
        /**** end format ****/

        /*** price ***/
        $price_value = array();
        $kurs_dollar = 1;
        $kurs_evro = 1;
        foreach($format_price as $key_index => $list_price){
            if ($list_price == 'usd') {
                $pattern_format = '#:price:.+:format:#';
                preg_match_all ($pattern_format, $content, $matches_format);
                //print_r($matches_format);

                $pattern_format = ':price:';
                $price_value_buf = str_replace ($pattern_format, '', $matches_format[0][$key_index]);
                $pattern_format = ':format:';
                $price_value[$key_index] = str_replace ($pattern_format, '', $price_value_buf);
                //echo $price_value;
                $kurs_dollar = theme_get_option('kurs_dollar');

            }
            if ($list_price == 'evro') {
                $pattern_format = '#:price:.+:format:#';
                preg_match_all ($pattern_format, $content, $matches_format);
                //print_r($matches_format);

                $pattern_format = ':price:';
                $price_value_buf = str_replace ($pattern_format, '', $matches_format[0][$key_index]);
                $pattern_format = ':format:';
                $price_value[$key_index] = str_replace ($pattern_format, '', $price_value_buf);
                //echo $price_value;
                $kurs_evro = theme_get_option('kurs_evro');

            }
        }

        /**** end price ****/

        $pattern = '#\[quickshop:.+:price:#';
        preg_match_all ($pattern, $content, $matches);
        //print_r($matches);
        foreach ($matches[0] as $key_index => $match)
        {
            //print_r($match);
            //echo $price_value;
            //exit();
            $pattern = '[quickshop:';
            $m = str_replace ($pattern, '', $match);
            $pattern = ':price:';
            $m = str_replace ($pattern, '', $m);

            /*$pattern = ':format:';
            $m = str_replace ($pattern, '', $m);
*/
            $pieces = explode('|',$m);         
            //print_r($pieces);

            if (sizeof($pieces) == 1)
            {

                if ($format_price[$key_index] == 'usd') {

                    $price_convert = (float) ($price_value[$key_index] * $kurs_dollar);

                    $content = str_replace ($match.$price_value[$key_index], 'Цена : <span class="page_price">'.sprintf("%.2f",$price_convert).' грн.</span> '.$match.$price_value[$key_index], $content);

                    $replacement = '<object><form method="post"  action=""  style="display:inline">
                    <input type="submit" value="'.$addcart.'" />
                    <input type="hidden" name="product" value="'.$pieces['0'].
                        '" /><input type="hidden" name="price" value="'.sprintf("%.2f",$price_convert);

                    $content = str_replace ($match.$price_value[$key_index], $replacement, $content);

                } else {

                    if ($format_price[$key_index] == 'evro') {

                        $price_convert = (float) ($price_value[$key_index] * $kurs_evro);

                        $content = str_replace ($match.$price_value[$key_index], 'Цена : <span class="page_price">'.sprintf("%.2f",$price_convert).' грн.</span> '.$match.$price_value[$key_index], $content);

                        $replacement = '<object><form method="post"  action=""  style="display:inline">
                    <input type="submit" value="'.$addcart.'" />
                    <input type="hidden" name="product" value="'.$pieces['0'].
                            '" /><input type="hidden" name="price" value="'.sprintf("%.2f",$price_convert);

                        $content = str_replace ($match.$price_value[$key_index], $replacement, $content);

                    } else {
                            $replacement = '<object><form method="post"  action=""  style="display:inline">
                        <input type="submit" value="' . $addcart . '" />
                        <input type="hidden" name="product" value="' . $pieces['0'] .
                                '" /><input type="hidden" name="price" value="';

                            $content = str_replace($match, $replacement, $content);
                    }
                }
            }
            else
            {
                //echo '1';
                // Checking for multiple prices
                $pattern = '#:price:.+:shipping:#';
                preg_match_all ($pattern, $content, $matches);
                
                $price = '';
                
                foreach ($matches[0] as $key_index => $match2)
                {

                    $pattern = ':price:';
                    $m = str_replace ($pattern, '', $match2);
                    $pattern = ':shipping:';
                    $m = str_replace ($pattern, '', $m);
                
                    $pieces2 = explode('|',$m);
                    
                    $count = 0;
                    foreach ($pieces2 as $option2)
                    {

                        $price .= '<input type="hidden" name="'.$pieces[$count].'" value="'.$option2.'" />';
                        $count++;
                    }
                }

                if (empty ($price))
                    $pname = 'price';
                else
                    $pname = 'multiple';
                
                $options = '';
                foreach ($pieces as $option)
                {
                    $options .= "<option>$option</option>";
                }

                if ($format_price[$key_index] == 'usd' && $pname == 'price') {

                    $price_convert = (float) ($price_value[$key_index] * $kurs_dollar);

                    $content = str_replace ($match.$price_value[$key_index], 'Цена : <span class="page_price">'.sprintf("%.2f",$price_convert).' грн.</span> '.$match.$price_value[$key_index], $content);

                    $replacement = '<object><form method="post"  action=""  style="display:inline">
                    <select name="product">'.$options.'</select>
                    <input type="submit" value="'.$addcart.'" />'.$price.'
                    <input type="hidden" name="'.$pname.'" value="'.sprintf("%.2f",$price_convert);

                    $content = str_replace ($match.$price_value[$key_index], $replacement, $content);

                } else {


                    if ($format_price[$key_index] == 'evro' && $pname == 'price') {

                        $price_convert = (float) ($price_value[$key_index] * $kurs_evro);

                        $content = str_replace ($match.$price_value[$key_index], 'Цена : <span class="page_price">'.sprintf("%.2f",$price_convert).' грн.</span> '.$match.$price_value[$key_index], $content);

                        $replacement = '<object><form method="post"  action=""  style="display:inline">
                    <select name="product">'.$options.'</select>
                    <input type="submit" value="'.$addcart.'" />'.$price.'
                    <input type="hidden" name="'.$pname.'" value="'.sprintf("%.2f",$price_convert);

                        $content = str_replace ($match.$price_value[$key_index], $replacement, $content);

                    } else {

                            $replacement = '<object><form method="post"  action=""  style="display:inline">
                        <select name="product">' . $options . '</select>
                        <input type="submit" value="' . $addcart . '" />' . $price . '
                        <input type="hidden" name="' . $pname . '" value="';

                            $content = str_replace($match, $replacement, $content);
                    }
                }
            }        
        }
    
        $forms = str_replace(':shipping:',
    
        '" /><input type="hidden" name="shipping" value="',
    
        $content);


        $forms = str_replace(':format:',

            '" /><input type="hidden" name="format" value="',

            $forms);
        
        
        $forms = str_replace(':shipping2:',
    
        '" /><input type="hidden" name="shipping2" value="',
    
        $forms);
        
        $forms = str_replace(':item_num:',
    
        '" /><input type="hidden" name="item_number" value="',
    
        $forms);
        global $post;
        
        $forms = str_replace(':end]',
    
        '" /><input type="hidden" name="addcart" value="1" /><input type="hidden" name="qslink" value="'.qscurPageURL().'" />
        </form></object>',
    
        $forms);
    }
    else 
    {
    
        $forms = str_replace('[quickshop:',
    
        '<form>
        <input type="hidden" name="product" value="',
    
        $content);
        
        $forms = str_replace(':end]',
    
        '" /></form>',
    
        $forms);
    
    }

    
    if (empty($forms))
        $forms = $content;
    
    $forms = str_replace('[quickdonate:',
    
    '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_donations"><input type="hidden" name="business" value="',
    
    $forms);
    
    $forms = str_replace(':d_name:',
    
    '" /><input type="hidden" name="item_name" value="',
    
    $forms);
    
    $forms = str_replace(':d_id:',
    
    '" /><input type="hidden" name="item_number" value="',
    
    $forms);
    
    $forms = str_replace(':d_amount:',
    
    '" /><input type="hidden" name="amount" value="',
    
    $forms);
    
    $forms = str_replace(':d_currency:',
    
    '" /><input type="hidden" name="no_shipping" value="0">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="currency_code" value="',
    
    $forms);
    
    $forms = str_replace(':d_locale:',
    
    '" /><input type="hidden" name="tax" value="0">
<input type="hidden" name="lc" value="',
    
    $forms);
    
    $forms = str_replace(':d_end]',
    
    '" /><input type="hidden" name="bn" value="PP-DonationsBF">
<input type="image" src="https://www.paypal.com/en_AU/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_AU/i/scr/pixel.gif" width="1" height="1">
</form>',
    
    $forms);
    
    if (empty($forms))
        $forms = $content;
    
    $forms = str_replace('[quicksubscribe:',
    
    '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="image" src="https://www.paypal.com/en_AU/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_AU/i/scr/pixel.gif" width="1" height="1">
<input type="hidden" name="cmd" value="_xclick-subscriptions">
<input type="hidden" name="business" value="',
    
    $forms);
    
    $forms = str_replace(':s_name:',
    
    '" /><input type="hidden" name="item_name" value="',
    
    $forms);
    
    $forms = str_replace(':s_id:',
    
    '" /><input type="hidden" name="item_number" value="',
    
    $forms);
    
    $forms = str_replace(':s_amount:',
    
    '" /><input type="hidden" name="a3" value="',
    
    $forms);
    
    $forms = str_replace(':s_currency:',
    
    '" /><input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="sra" value="1">
<input type="hidden" name="bn" value="PP-SubscriptionsBF">
<input type="hidden" name="currency_code" value="',
    
    $forms);
    
    $forms = str_replace(':s_locale:',
    
    '" /><input type="hidden" name="lc" value="',
    
    $forms);
    
    $forms = str_replace(':sc_value:',
    
    '" /><input type="hidden" name="p3" value="',
    
    $forms);
    
    $forms = str_replace(':sc_time:',
    
    '" /><input type="hidden" name="t3" value="',
    
    $forms);
    
    $forms = str_replace(':s_end]',
    
    '" /></form>',
    
    $forms);
    
    return $forms;
}


function quickshop_options()
{

     echo '<div class="wrap"><h2>QuickShop Options</h2>';
     print_quickshop_form();
     echo '</div>';

}

function print_quickshop_form () {
    $defaultCurrency = get_option('quickshop_currency');
    if (empty($defaultCurrency)) $defaultCurrency = 'AUD';
    $defaultSymbol = get_option('quickshop_symbol');
    if (empty($defaultSymbol)) $defaultSymbol = '$';
    $defaultDecimal = get_option('quickshop_decimal');
    if (empty($defaultDecimal)) $defaultDecimal = '.';
    $defaultEmail = get_option('quickshop_pemail');
    if (empty($defaultEmail)) $defaultEmail = get_bloginfo('admin_email');
    $notify_url =  get_option('quickshop_pnurl');
    $return_url =  get_option('quickshop_prurl');
    $defaultChURL = get_option('quickshop_churl');
    if (empty($defaultChURL)) $defaultChURL = 'contact';
    $addcart = get_option('quickshop_addcart');
    if (empty($addcart)) $addcart = 'Add to Cart';
    
    if (get_option('quickshop_display'))
        $checked = 'checked="checked"';
    else
        $checked = '';
        
    if (get_option('quickshop_total'))
        $tchecked = 'checked="checked"';
    else
        $tchecked = '';
    
    if (get_option('quickshop_location') == 'before')
        $before = 'checked="checked"';
    else
        $after = 'checked="checked"';
    
    $freeshipv = get_option('quickshop_freeshipv');
    if (empty($freeshipv)) $freeshipv = '0';
    $freeshipt = get_option('quickshop_freeshipt');
    if (empty($freeshipt)) $freeshipt = 'Shipping is free for orders of $200 and upwards.';
	  $displayt = get_option('quickshop_displayt');
	  if (empty($displayt)) $displayt = 'Your cart is empty.';
	
	  $title = get_option('quickshop_title');
	  if (empty($title)) $title = 'Your Shopping Cart';
    
    $displaytc = get_option('quickshop_tc');
    $displaytct = get_option('quickshop_tct');
    $displaytcst = get_option('quickshop_tcst');
    
    if (get_option('quickshop_logged'))
        $logged = 'checked="checked"';
    else
        $logged = '';    

      echo '
 <form method="post" action="options.php">';
 wp_nonce_field('update-options');
 echo '
 <table class="form-table">
 <tr valign="top">
<th scope="row">Shopping Cart title</th>
<td><input type="text" name="quickshop_title" value="'.$title.'"  /></td>
</tr>
<tr valign="top">
<th scope="row">Currency</th>
<td><input type="text" name="quickshop_currency" value="'.$defaultCurrency.'" size="6" /> (e.g. USD, AUD)</td>
</tr>
<tr valign="top">
<th scope="row">Currency Sybmol</th>
<td><input type="text" name="quickshop_symbol" value="'.$defaultSymbol.'" size="2" style="width: 1.5em;" /> (e.g. $, &#163;) - 
<label>before <input type="radio" name="quickshop_location" value="before" '.$before.'></label> or <label>after <input type="radio" name="quickshop_location" value="after" '.$after.'> the number?</label>
</td>
</tr>
<tr valign="top">
<th scope="row">Decimal Sign</th>
<td><input type="text" name="quickshop_decimal" value="'.$defaultDecimal.'" size="2" style="width: 1.5em;" /> (e.g. a period or a comma)</td>
</tr>
<tr valign="top">
<th scope="row">Display Shopping Cart when Empty?</th>
<td><input type="checkbox" name="quickshop_display" value="1" '.$checked.' /> - Text to display when empty: <input type="text" name="quickshop_displayt" value="'.$displayt.'" size="70" /></td>
</tr>
<tr valign="top">
<th scope="row">Display Total only?</th>
<td><input type="checkbox" name="quickshop_total" value="1" '.$tchecked.' /> (e.g. when you don\'t use postage)</td>
</tr>
<tr valign="top">
<th scope="row">Add to Cart button text</th>
<td><input type="text" name="quickshop_addcart" value="'.$addcart.'" /></td>
</tr>
</table>

<h3>PayPal Widget</h3>

<table class="form-table">
<tr valign="top">
<th scope="row">Paypal Email</th>
<td><input type="text" name="quickshop_pemail" value="'.$defaultEmail.'" /></td>
</tr>
<tr valign="top">
<th scope="row">Notify URL</th>
<td><input type="text" name="quickshop_pnurl" value="'.$notify_url.'" size="70" /></td>
</tr>
<tr valign="top">
<th scope="row">Return URL</th>
<td><input type="text" name="quickshop_prurl" value="'.$return_url.'" size="70" /></td>
</tr>
</table>

<h3>Custom Checkout Widget</h3>

<table class="form-table">
<tr valign="top">
<th scope="row">Relative URL to custom checkout</th>
<td>'.get_bloginfo('url').'/<input type="text" name="quickshop_churl" value="'.$defaultChURL.'" /></td>
</tr>
</table>

<h3>Free Shipping</h3>

<table class="form-table">
<tr valign="top">
<th scope="row">Purchase Threshold</th>
<td>'.$defaultSymbol.'<input type="text" name="quickshop_freeshipv" value="'.$freeshipv.'" /> - a value of 0 means that Free Shipping is off.</td>
</tr>
<tr valign="top">
<th scope="row">Notification text</th>
<td><input type="text" name="quickshop_freeshipt" value="'.$freeshipt.'" size="100" /></td>
</tr>
</table>

<h3>Terms and Conditions</h3>

<table class="form-table">
<tr valign="top">
<th scope="row">URL</th>
<td><input type="text" name="quickshop_tc" value="'.$displaytc.'" size="70" /> Must begin with "http://"</td>
</tr>
<tr valign="top">
<th scope="row">Text</th>
<td><input type="text" name="quickshop_tct" value="'.$displaytct.'" size="20" /> Optional wording (e.g. foreign language).</td>
</tr>
<tr valign="top">
<th scope="row">Submit Text</th>
<td><input type="text" name="quickshop_tcst" value="'.$displaytcst.'" size="20" /> Optional wording (e.g. foreign language).</td>
</tr>
</table>

<h3>Security</h3>

<table class="form-table">
<tr valign="top">
<th scope="row">Buy Button for Logged-in Users only?</th>
<td><input type="checkbox" name="quickshop_logged" value="1" '.$logged.' /></td>
</tr>
</table>

<p class="submit">
<input type="submit" name="Submit" value="Update Options &raquo;" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="quickshop_currency,quickshop_churl,quickshop_symbol,quickshop_decimal,quickshop_pemail,quickshop_addcart,quickshop_total,quickshop_display,quickshop_location,quickshop_freeshipv,quickshop_freeshipt,quickshop_displayt,quickshop_title,quickshop_tc,quickshop_tct,quickshop_tcst,quickshop_logged,quickshop_prurl,quickshop_pnurl" />
</p>

      
<h3>Do you like QuickShop?</h3>
<h4>Websites</h4>
<p>Zack Design has completed many websites, themes, and plugins. This one is free for you to use and has been perfected over countless hours. Please either <a href="http://zackdesign.biz/wp-plugins/34">donate</a> or <a href="http://zackdesign.biz/">consider using Zack Design for your next project!</a></p>
<h4>VirtualBox, Synergy, more than 2 Desktop Monitors, Networks, Linux and more!</h4>
<p>Zack Design knows all the tricks when it comes to setting up your office with multiple monitors, seamlessly integrating multiple PCs with different operating systems, virtual machines like VirtualBox for emulating Windows or Linux on your desktop, and can setup and configure wireless and wired networks as well as network servers easily. <a href="http://zackdesign.biz/contact">Contact us today to increase your productivity!</a></p>

 </form>
 ';
}

function quickshop_options_page () {
     add_options_page(
                      'QuickShop',         //Title
                      'QuickShop',         //Sub-menu title
                      'manage_options', //Security
                      __FILE__,         //File to open
                      'quickshop_options'  //Function to call
                     );  
}

add_action('plugins_loaded', 'quickshop_widgets');

add_action('admin_menu','quickshop_options_page');

add_filter('the_content', 'quickshop_button_post');


function cart_has_items()
{
        $count = 0;
        if (isset($_SESSION['qscart']) && is_array($_SESSION['qscart']))
        {
            foreach ($_SESSION['qscart'] as $item)
                $count++;
            return $count;
        }
        else
            return 0;
}

function output_currency($price, $symbol, $decimal)
{
    if (get_option('quickshop_location') == 'before')
        return $symbol.number_format($price, 2, $decimal, ',');
    else
        return number_format($price, 2, $decimal, ',').$symbol;
}

function qscurPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

// Load tinymce button 
if (IS_WP25)
	include_once (dirname (__FILE__)."/tinymce3/tinymce.php");
else
	include_once (dirname (__FILE__)."/tinymce/tinymce.php");


?>