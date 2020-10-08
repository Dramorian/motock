<?php
###     (c) MaxSite.org, 2007
### Plugin for http://rss2email.ru/

class maxsite_rss2email_plugin 
{
	var $page_title;
	var $menu_title;
	var $access_level;
	var $add_page_to; 
	var $short_description;
	var $key_options = 'rss2email';

	
	function maxsite_rss2email_plugin()
	{
		$options = get_option($this->key_options);
		
		$add = false;
		
		if ( !$options['button'] ) {
			$options['button'] = 'ta_1.gif';
			$add = true;
		}

		if ( !$options['nomer_rss'] ) {
			$options['nomer_rss'] = 0;
			$add = true;
		}			
		
		if ($add) update_option($this->key_options, $options);
		
	}

	
	function add_admin_menu() {
		if ( $this->add_page_to == 1 )
			add_menu_page($this->page_title, $this->menu_title, $this->access_level, __FILE__, 
				array($this, 'admin_page'));
		elseif ( $this->add_page_to == 2 )
			add_options_page($this->page_title, $this->menu_title, $this->access_level, __FILE__, 
				array($this, 'admin_page'));
		elseif ( $this->add_page_to == 3 )
			add_management_page($this->page_title, $this->menu_title, $this->access_level, __FILE__, 
				array($this, 'admin_page'));
		elseif ( $this->add_page_to == 4 )
			add_theme_page($this->page_title, $this->menu_title, $this->access_level, __FILE__, 
				array($this, 'admin_page'));
	}


	function activate() 
	{ 
		$options = get_option($this->key_options);

		if ( !$options ) {
			$options['button'] = 'ta_1.gif';
			$options['adres_rss'] = get_bloginfo('rss2_url');
			$options['nomer_rss'] = 0;
			$options['form'] = 1;
			add_option($this->key_options, $options, 'rss2email');
		}
	}


	function deactivate() 
	{ 
		delete_option($this->key_options);
	}


	function update($item) 
	{
		$options = $newoptions = get_option($this->key_options);
		$newoptions['button'] = $item;

		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option($this->key_options, $options);
			$upd = true;
		}
		else $upd = false;
		
		return !$upd;
	}

	
	function update_form($item) 
	{
		
		if ( ($item < 1) || ($item >4) ) return '<div class="error">Балуемся, да?</div>';
		
		$options = $newoptions = get_option($this->key_options);
		$newoptions['form'] = $item;

		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option($this->key_options, $options);
			$upd = '<div class="updated">Отлично!</div>';
		}
		else $upd = '<div class="error">Настройки не изменились</div>';
		
		return $upd;
	}	
	

	function get_nomer($item) 
	{
	
		if ( !$item ) $item = get_bloginfo('rss2_url');
		
		// адрес загрузки
		$url = 'http://www.rss2email.ru/misc/rss_info_txt.asp?rss='. urlencode ( stripslashes($item) );
		$info = @file($url); // массив
		
		if (!$info) echo '<div class="error">Ошибка соединения с сервером rss2email.ru</div>';
		
		$info = (array) $info;
		
		$info = trim( implode(' ', $info) ); // в строку
		$info = explode(' ', $info); // в массив - первый элемент номер

		if ($info) $info = (int) $info[0];
			else $info = 0;
			
		$options = $newoptions = get_option($this->key_options);
		$newoptions['adres_rss'] = $item;
		$newoptions['nomer_rss'] = $info;
		
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option($this->key_options, $options);
			
			if ($info == 0) 
				$upd = '<div class="error">Ошибка получения номера для RSS ленты <b>' . wp_specialchars($item) . '</b></div>';
			else
				$upd = '<div class="updated">Обновление выполено! Получен номер ленты: ' . $info . '</div>';
		}
		else 
		$upd = '<div class="error">Настройки не изменились</div>';
		
		return $upd;
	}


	function admin_page() 
	{ 
		$path = get_bloginfo('siteurl') . '/wp-content/plugins/rss2email/images/';
		
		echo <<<EOF
		<div class="wrap" style="padding: 15px;"> 
		
		<a href="http://www.rss2email.ru/" target="_blank"><img style="float: right;" src="{$path}logo.gif" /></a>
		<span style="font-weight: bold; color: #F37423; font-size: 24pt;">{$this->page_title}</span>		
		
		<br clear="all" />
		<p>{$this->short_description}</p>
EOF;

		if (isset($_POST['UPDATE'])) { 
	
			if (isset($_POST['button'])) {
				foreach ($_POST['button'] as $item) {
					$add = $this->update($item);
				}
			}
			
			if ( !$add ) 
				echo '<div class="updated">Отличный выбор!</div>';
		}
		
		if (isset($_POST['GETNOMER'])) { 
			if (isset($_POST['adres_rss'])) {
				echo $this->get_nomer($_POST['adres_rss']);
			}
		}		
		
		if (isset($_POST['SELFORM'])) { 
			if (isset($_POST['rss2email_form'])) {
				echo $this->update_form($_POST['rss2email_form']);
			}
		}
		
		
		$this->view_options_page();
		echo '<p style="text-align: right;">&copy; <a href="http://maxsite.org/">MaxSite.org</a>, 2007, 2008</p>';
		echo '</div>';
	}


	function make_input($arr, $big = false) 
	{ 
		$path = get_bloginfo('siteurl') . '/wp-content/plugins/rss2email/images/';
		$out = '';

		$options = get_option($this->key_options);
		$cur = $options['button'];
		if (!$cur) $cur = 'ta_1.gif';
		
		$nomer_rss = (int) $options['nomer_rss'];
		
	
		foreach ($arr as $item) {
			if ( $cur == $item) {
				$select = ' checked="checked" ';
				$trstyle = 'background: #E7FF90;';
			}
			else 
			{
				$trstyle = '';
				$select = '';
			}
			
			if ( !stristr($item, 'rss2email') ) { // нет  rss2email_
				// меняем путь на внешний - только для счетчиков
				$r = str_replace('ta_', 'typeA/' . $nomer_rss . '_', $item);  // typeA/1234_2.gif
				$r = str_replace('tb_', 'typeB/' . $nomer_rss . '_', $r);
				$r = str_replace('tc_', 'typeC/' . $nomer_rss . '_', $r);
				$r = str_replace('td_', 'typeD/' . $nomer_rss . '_', $r);
				$r = str_replace('te_', 'typeE/' . $nomer_rss . '_', $r);
				$path_src = 'http://www.rss2email.ru/counter/'. $r;
			}
			else
			{
			$path_src = $path . $item;
			}
			
			$out .= '<div style="float: left; vertical-align: middle; width: 130px; margin: 0 10px 5px 0; padding: 3px;' . $trstyle . '"><input style="border: none; background: transparent;" name="button[]" ' . $select . 'value="' . $item . '" type="radio"> <img style="margin-left: 5px; vertical-align: middle;" src="'. $path_src . '" /></div>' . "\n\n";
		}
		
		echo '<br clear="all" />';
		echo $out;
	}
	
	
	function view_options_page() 
	{ 
		$options = get_option($this->key_options);

		$blog_rss = $options['blog_rss'] ? 'checked="checked"' : '';
		$adres_rss = attribute_escape ($options['adres_rss']);
		$nomer_rss = (int) $options['nomer_rss'];
		
		if ( !$adres_rss ) $adres_rss = get_bloginfo('rss2_url');
		
		$message_nomer_rss = $nomer_rss ? 
				'Лента подключена'
				: 
				'<font color="red">Вам нужно подключить ленту к сервису rss2email.ru. Нажмите кнопку «Подключить»</font>';


		
		$form_zag_blog = get_bloginfo('name');
		$form = (int) $options['form'];
		if ($form == 0) $form = 1;
		$adres_site = get_bloginfo('siteurl');
		
		$form1 = $this->form1($form_zag_blog, $form, $adres_rss, $adres_site, $nomer_rss);
		$form2 = $this->form2($form_zag_blog, $form, $adres_rss, $adres_site, $nomer_rss);
		$form3 = $this->form3($form_zag_blog, $form, $adres_rss, $adres_site, $nomer_rss);
		$form4 = $this->form4($form_zag_blog, $form, $adres_rss, $adres_site, $nomer_rss);		
		
		
		echo <<<EOF
		<div style="padding: 0px 10px 0px 10px;">
		<form action="" method="POST">
		<br clear="all" />
		Адрес RSS: <input id="adres_rss" name="adres_rss" type="text" size="80" value="{$adres_rss}" /> 
		<input type="submit" name="GETNOMER" value="Подключить">
		<br clear="all" />
		<span style="padding-left:75px;">{$message_nomer_rss}</span>
		<br clear="all" /><br clear="all" />
		

		<hr />
		<h3 style="margin-bottom: 0;">Выберите форму подписки</h3>
		<br clear="all" />
		<table width="100%" cellpadding="10"><tr>
		<td width="25%" valign="top">{$form1}</td>
		<td width="25%" valign="top">{$form2}</td>
		<td width="25%" valign="top">{$form3}</td>
		<td width="25%" valign="top">{$form4}</td>
		</tr></table>
		<br clear="all" />
		<input type="submit" name="SELFORM" value="Выбрать форму" style="float: right;">
		<br clear="all" />
		
		
		
		<hr />
		<h3 style="margin-bottom: 0;">Выберите счетчик</h3>
		<br clear="all" /><input type="submit" name="UPDATE" value="Обновить" style="float: right;">
EOF;

		$this->make_input( array('ta_1.gif', 'ta_2.gif', 'ta_3.gif', 'ta_4.gif') );
		$this->make_input( array('tb_1.gif', 'tb_2.gif', 'tb_3.gif', 'tb_4.gif') );	
		$this->make_input( array('tc_1.gif', 'td_1.gif') );	
		$this->make_input( array('te_1.gif', 'te_2.gif', 'te_3.gif', 'te_4.gif', 'te_5.gif') );	
		$this->make_input( array('te_6.gif', 'te_7.gif', 'te_8.gif', 'te_9.gif') );


		echo '<br clear="all" /><h3 style="margin-bottom: 0;">или кнопку</h3>';
		
		$this->make_input( array('rss2email_33x31_a.gif',  'rss2email_33x31_b.gif') );
		$this->make_input( array('rss2email_42x17_a.gif',  'rss2email_42x17_b.gif', 'rss2email_42x17_c.gif') );
		$this->make_input( array('rss2email_88x15_a.gif',  'rss2email_88x15_b.gif', 'rss2email_88x15_c.gif',
			'rss2email_88x15_d.gif', 'rss2email_88x15_e.gif', 'rss2email_88x15_f.gif') );
		$this->make_input( array('rss2email_88x15_g.gif',  'rss2email_88x15_h.gif', 'rss2email_88x15_i.gif',
			'rss2email_88x15_j.gif', 'rss2email_88x15_k.gif', 'rss2email_88x15_l.gif') );
		$this->make_input( array('rss2email_88x15_m.gif',  'rss2email_88x15_n.gif') );
		$this->make_input( array('rss2email_88x31_a.gif',  'rss2email_88x31_b.gif') );
		$this->make_input( array('rss2email_91x17_a.gif',  'rss2email_91x17_b.gif', 'rss2email_91x17_c.gif',
			'rss2email_91x17_d.gif') );
		$this->make_input( array('rss2email_91x17_e.gif',  'rss2email_91x17_f.gif', 'rss2email_91x17_g.gif',
			'rss2email_91x17_h.gif') );

		echo <<<EOF
		<br clear="all" /><br clear="all" /><input type="submit" name="UPDATE" value="Обновить" style="float: right;">
		</form><br /><br clear="all" /><br clear="all" />
		</div>
EOF;

	}
	
	
	function form1($form_zag_blog, $form, $adres_rss, $adres_site, $nomer_rss, $radio = true) 
	{
		if ( $form == 1 ) $select = 'checked="checked" ';
			else $select = '';
		
		if ( $radio ) {
			$radio = '<input style="border: none; background: transparent;" name="rss2email_form" ' .
					$select . 'value="1" type="radio" /> Выбрать<br /><br />';
			$f1 = $f2 = '';
			$s = 'button';
		}
		else {
			$radio = '';
			$f1 = '<form action="http://www.rss2email.ru/ready.asp" method="get">';
			$f2 = '</form>';
			$s = 'submit';
		}
		
		
		$out = <<<EOF
		{$radio}
<table width="100%" border="0" cellspacing="0" cellpadding="0">
{$f1}
<tr>
<td bgcolor="#DDDDDD">
<table border="0" width="100%">
<tr>
<td bgcolor="#EFEFEF" align="center" style="font: 12px tahoma, verdana, arial">Подписаться через <a href="http://www.rss2email.ru"><font color="#FF6300">RSS2Email</font></a></td></tr>
<tr><td align="center" bgcolor="#FFFFFF" style="line-height: 10px;">
<input type="hidden" name="rss" value="{$adres_rss}" />
<input type="hidden" name="link"  value="" />
<input type="hidden" name="logo" value="" /><br />
<b>{$form_zag_blog}</b><br /><br />
<input type="text" size="15" name="email" value="Ваш E-mail" onfocus="if (this.value=='Ваш E-mail') this.value='';" /> 
<input type="{$s}" value=" Ok " />
<br /><br /></td></tr></table></td></tr>{$f2}</table>
EOF;

	return $out;
	}

	
	function form2($form_zag_blog, $form, $adres_rss, $adres_site, $nomer_rss, $radio = true) 
	{
		
		if ( $form == 2 ) $select = 'checked="checked" ';
			else $select = '';
		
		if ( $radio ) {
			$radio = '<input style="border: none; background: transparent;" name="rss2email_form" ' .
					$select . 'value="2" type="radio" /> Выбрать<br /><br />';
			$f1 = $f2 = '';
			$s = 'button';
		}
		else {
			$radio = '';
			$f1 = '<form action="http://www.rss2email.ru/ready.asp" method="get">';
			$f2 = '</form>';
			$s = 'submit';
		}
			
		$out = <<<EOF
		{$radio}		
<table width="100%" border="0" cellspacing="0" cellpadding="0">{$f1}<tr><td bgcolor="#DDDDDD"><table border="0" width="100%"><tr><td bgcolor="#EFEFEF" align="center" style="font: 12px tahoma, verdana, arial">Подписаться через <a href="http://www.rss2email.ru"><font color="#FF6300">RSS2Email</font></a></td></tr><td align="center" bgcolor="#FFFFFF" style="line-height: 10px;"><input type="hidden" name="rss" value="{$adres_rss}" /><input type="hidden" name="link"  value="" /><input type="hidden" name="logo" value="" /><br /><b>{$form_zag_blog}</b><br /><br /><input type="text" size="15" name="email" value="Ваш E-mail" onfocus="if (this.value=='Ваш E-mail') this.value='';" /> <input type="{$s}" value=" Ok " /><br /><br /><img src="http://www.rss2email.ru/counter/typeF/{$nomer_rss}_1.gif" /><br /></td></tr></table></td></tr>{$f2}</table>
EOF;
		return $out;
	}

	
	function form3($form_zag_blog, $form, $adres_rss, $adres_site, $nomer_rss, $radio = true) 
	{

		if ( $form == 3 ) $select = 'checked="checked" ';
			else $select = '';

		if ( $radio ) {
			$radio = '<input style="border: none; background: transparent;" name="rss2email_form" ' .
					$select . 'value="3" type="radio" /> Выбрать<br /><br />';
			$f1 = $f2 = '';
			$s = 'button';
		}
		else {
			$radio = '';
			$f1 = '<form action="http://www.rss2email.ru/ready.asp" method="get">';
			$f2 = '</form>';
			$s = 'submit';
		}
			
		$out = <<<EOF
		{$radio}
{$f1}
<input type="hidden" name="rss" value="{$adres_rss}" />
<input type="hidden" name="link"  value="" />
<input type="hidden" name="logo" value="" />
<table><tr><td>Подписка на блог<br />
<input type="text" size="20" name="email" value="Ваш E-mail" onfocus="if (this.value=='Ваш E-mail') this.value='';" /></td></tr>
<tr><td><input type="{$s}" value=" Подписаться " /><br /><img src="http://www.rss2email.ru/counter/typeF/{$nomer_rss}_1.gif" /><br /><a href="http://www.rss2email.ru"><small><small>rss2email</small></small></a></td></tr></table>{$f2}
EOF;
		return $out;
	}

	
	function form4($form_zag_blog, $form, $adres_rss, $adres_site, $nomer_rss = 1, $radio = true) 
	{
		if ( $form == 4 ) $select = 'checked="checked" ';
			else $select = '';
		
		if ( $radio ) {
			$radio = '<input style="border: none; background: transparent;" name="rss2email_form" ' .
					$select . 'value="4" type="radio" /> Выбрать<br /><br />';
			$f1 = $f2 = '';
			$s = 'button';
		}
		else {
			$radio = '';
			$f1 = '<form action="http://www.rss2email.ru/ready.asp" method="get">';
			$f2 = '</form>';
			$s = 'submit';
		}
			
		$out = <<<EOF
		{$radio}
{$f1}
<input type="hidden" name="rss" value="{$adres_rss}" />
<input type="hidden" name="link"  value="" />
<input type="hidden" name="logo" value="" />
<table><tr><td>Подписка на блог<br />
<input type="text" size="20" name="email" value="Ваш E-mail" onfocus="if (this.value=='Ваш E-mail') this.value='';" /></td></tr>
<tr><td><input type="{$s}" value=" Подписаться " /><br /><a href="http://www.rss2email.ru"><small><small>rss2email</small></small></a></td></tr></table>{$f2}
EOF;
		return $out;
	}
	
	
	function widget() 
	{ 
		$options = get_option($this->key_options);

		$form_zag_blog = get_bloginfo('name');
		$form = (int) $options['form'];
		if ( $form  == 0 ) $form  = 1;
		$adres_rss = attribute_escape ($options['adres_rss']);
		$adres_site = get_bloginfo('siteurl');		
		$nomer_rss = (int) $options['nomer_rss'];

		if ( $form  == 1 ) echo $this->form1($form_zag_blog, $form, $adres_rss, $adres_site, $nomer_rss, false);
		elseif ( $form  == 2 ) echo $this->form2($form_zag_blog, $form, $adres_rss, $adres_site, $nomer_rss, false); 
		elseif ( $form  == 3 ) echo $this->form3($form_zag_blog, $form, $adres_rss, $adres_site, $nomer_rss, false); 
		elseif ( $form  == 4 ) echo $this->form4($form_zag_blog, $form, $adres_rss, $adres_site, $nomer_rss, false); 
	}
	
	
	function widget_options($args = array()) 
	{ 
		echo 'Настройки формы для подписки через rss2email.ru осуществляются на страничке плагина: "Настройки - RSS2Email.RU"';
	}	
	
	
	function button() 
	{ 
		$options = get_option($this->key_options);
		$button = $options['button'];
		$nomer_rss = (int) $options['nomer_rss'];
		$adres_rss = $options['adres_rss'];
		
		$options = get_option('widget_maxsite_rss2email');
		$do = $options['do'] ? $options['do'] : '';
		$posle = $options['posle'] ? $options['posle'] : '';
		
		if (!$adres_rss) $adres_rss = get_bloginfo('rss2_url'); // адрес по-умолчанию
		
		if (!$button) $button = 'rss2email_91x17_c.gif'; // нулевая кнопка
		
		// если имя содержит rss2email, значит это кнопка
		// иначе счетчик - нужно добавлять номер ленты
		if ( stristr($button, 'rss2email') ) { // есть 
			$out = '<a href="http://www.rss2email.ru?rss=' . $adres_rss . 
				'" title="Получать RSS-ленту на почту"><img src="' . get_bloginfo('siteurl') .
				'/wp-content/plugins/rss2email/images/' . $button . '" border="0" /></a>';
		}
		else {
			$r = str_replace('ta_', 'typeA/' . $nomer_rss . '_', $button);  // typeA/1234_2.gif
			$r = str_replace('tb_', 'typeB/' . $nomer_rss . '_', $r);
			$r = str_replace('tc_', 'typeC/' . $nomer_rss . '_', $r);
			$r = str_replace('td_', 'typeD/' . $nomer_rss . '_', $r);
			$r = str_replace('te_', 'typeE/' . $nomer_rss . '_', $r);
			
			$out = $do . '<a href="http://www.rss2email.ru?rss=' . $adres_rss . 
				'" title="Получать RSS-ленту на почту"><img src="http://www.rss2email.ru/counter/' . 
				$r . '" border="0" alt="" /></a>' . $posle;
		}
		
		echo $out;
	}


	function button_options($args = array()) 
	{ 
		$options = $newoptions = get_option('widget_maxsite_rss2email');
		
		if ( $_POST['widget_maxsite_rss2email_submit'] ) {
			$newoptions['do'] = $_POST['widget_maxsite_rss2email_do'];
			$newoptions['posle'] = $_POST['widget_maxsite_rss2email_posle'];
		}
	
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_maxsite_rss2email', $options);
		}

		$do = $options['do'];
		$posle = $options['posle'];
		
		echo <<<EOF
		Текст перед кнопкой<br />
		<input id="widget_maxsite_rss2email_do" name="widget_maxsite_rss2email_do" type="text" value="{$do}" />
		<br /><br />Текст после кнопки<br />
		<input id="widget_maxsite_rss2email_posle" name="widget_maxsite_rss2email_posle" type="text" value="{$posle}" />
		<br /><br /><em>Можно использовать HTML-тэги.</em>
		<br /><br />Выбрать кнопку или счетчик rss2email.ru можно на страничке плагина: "Настройки - RSS2Email.RU"
		<input type="hidden" id="widget_maxsite_rss2email_submit" name="widget_maxsite_rss2email_submit" value="1" />
EOF;
	}

}

?>