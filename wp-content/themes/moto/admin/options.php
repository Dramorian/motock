<?php

//////////////////////////////////////////////////////////////
// Theme Custom Options Page
/////////////////////////////////////////////////////////////

$theme_name = "Настройки";
$theme_version = "1.0";

require_once( TEMPLATEPATH . '/admin/admin-setup.php');
require_once( TEMPLATEPATH . '/admin/admin-functions.php');

add_action( 'admin_init', 'theme_register_options' );

function theme_register_options() {
	register_setting( 'theme_options_group', 'theme_options' );
}


function theme_options_page() {
	
global $theme_name, $theme_version
 ?>
<div class="wrap">
	<div id="optionsWrap">			
		<div id="optionsHeader" class="clearfix">
			<a id="themeLogo" href="/">Moto.ck.ua</a>
			<span id="themeVersion">
				<a href="http://primeua.com" target="_blank">Поддержка PrimeUA</a>
			</span>
			<ul id="optionsNav" class="tabs">
            	<li id="tab1"><a href="#option1">Основные настройки</a></li>
			</ul>
			
		</div>		
		
		<form id="optionsForm" method="post" action="options.php">	        
       
		    <?php
			settings_fields( 'theme_options_group' ); 
		    $theme_options = theme_get_option('all');
			?>
		    
		    <div class="optionsContainer clearfix">	
			
				<div id="statusBar" class="clearfix">
					<?php if(isset($_REQUEST['updated']) || isset($_REQUEST['reset'])) echo '<div id="message">'.$theme_name.' '. 'Settings updated'.'</div>'; ?>
					<input type="submit" class="button" value="Сохранить" />
				</div>	
			
			<div id="option1" class="optionContent">
                <div class="subOption">
                    <br><br>
                	<div class="itemRow clearfix">
						<div class="col_label">Курс цены:</div>
						<div class="col_long">
							<div class="logoContainer">
								<div id="status_favicon"></div>

							</div>
							<div class="itemRow clearfix">
								<input name="theme_options[kurs_dollar]" id="kurs_dollar" type="text" size="50" value="<?php if(isset($theme_options['kurs_dollar'])) echo $theme_options['kurs_dollar'];  ?>" />
								<span class="col_desc">Курс грн относительно доллара</span>
							</div>
						</div>
					</div>
					<div class="itemRow clearfix">
						<div class="col_label">Курс цены:</div>
						<div class="col_long">
							<div class="logoContainer">
								<div id="status_favicon"></div>

							</div>
							<div class="itemRow clearfix">
								<input name="theme_options[kurs_evro]" id="kurs_evro" type="text" size="50" value="<?php if(isset($theme_options['kurs_evro'])) echo $theme_options['kurs_evro'];  ?>" />
								<span class="col_desc">Курс грн относительно евро</span>
							</div>
						</div>
					</div>
                    

                </div>
            </div>
            
            

                
		<input type="submit" class="button right" value="Сохранить" />
		
	</div>
</div> 
<?php } ?>