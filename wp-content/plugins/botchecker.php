<?php
/*
Plugin Name: Dschini Bot Checker
Plugin URI: http://manfred.dschini.org/dschini-botchecker-plugin
Description: Check for bots that track your blog
Author: Manfred Weber
Version: 1.0
Author URI: http://manfred.dschini.org
*/

ini_set("soap.wsdl_cache_enabled","0");

function dschini_botchecker_init()
{
	global $wpdb;
	$table_name_1 = $wpdb->prefix . "dschini_botchecker_bots";
	$table_name_2 = $wpdb->prefix . "dschini_botchecker_tracker";
	$dschini_botchecker_options_high = get_option("dschini_botchecker_options_high");
        $dschini_botchecker_options_medium = get_option("dschini_botchecker_options_medium");
        $dschini_botchecker_options_low = get_option("dschini_botchecker_options_low");

	$sql = sprintf("DELETE FROM %s",$table_name_1);
	$wpdb->query( $sql );	

        $bots = array();
	$key = get_option("dschini_botchecker_options_key");
        $client = new SoapClient("http://services.dschini.org/bot.php?WSDL");
	$_botshigh = $dschini_botchecker_options_high=="1"?$client->getHighPopularBots($key):array();
        $_botsmedium = $dschini_botchecker_options_medium=="1"?$client->getMediumPopularBots($key):array();
        $_botslow = $dschini_botchecker_options_low=="1"?$client->getLowPopularBots($key):array();
        $bots = array_merge($bots, $_botshigh);
	$bots = array_merge($bots, $_botsmedium);
	$bots = array_merge($bots, $_botslow);

        foreach($bots AS $bot){
                $sql = sprintf("INSERT INTO %s (`bot_id`,`description`
                                                ,`purpose`,`history`,`license`
                                                ,`exclusionuseragent`,`ownername`,`ownerurl`
                                                , `owneremail`,`popularity`,`name`
                                                ,`detailsurl`)
                                        VALUES (
                                                '%d', '%s',
                                                '%s', '%s', '%s',
                                                '%s', '%s', '%s',
                                                '%s', '%s', '%s',
                                                '%s')"
                                        ,$table_name_1
                                        ,$bot->id,$wpdb->escape($bot->description)
                                        ,$wpdb->escape($bot->purpose),$wpdb->escape($bot->history),$wpdb->escape($bot->license)
                                        ,$wpdb->escape($bot->exclusionuseragent),$wpdb->escape($bot->ownername),$wpdb->escape($bot->ownerurl)
                                        ,$wpdb->escape($bot->owneremail),$wpdb->escape($bot->popularity),$wpdb->escape($bot->name)
                                        ,$wpdb->escape($bot->detailsurl)
                                        );
                $wpdb->query( $sql );

        }

}

function dschini_botchecker_install()
{
	global $wpdb;
	$table_name_1 = $wpdb->prefix . "dschini_botchecker_bots";
	$table_name_2 = $wpdb->prefix . "dschini_botchecker_tracker";
	if($wpdb->get_var("show tables like '$table_name_1'") != $table_name_1) {
      
		$sql = "CREATE TABLE ".$table_name_1." (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
	                bot_id mediumint(9) NOT NULL,
			description VARCHAR(255) NOT NULL,
			purpose VARCHAR(255) NOT NULL,
			history VARCHAR(255) NOT NULL,
			license VARCHAR(255) NOT NULL,
			exclusionuseragent VARCHAR(255) NOT NULL,
			ownername VARCHAR(255) NOT NULL,
			ownerurl VARCHAR(255) NOT NULL,
			owneremail VARCHAR(255) NOT NULL,
			popularity VARCHAR(255) NOT NULL,
			name VARCHAR(255) NOT NULL,
			detailsurl VARCHAR(255) NOT NULL,
			UNIQUE KEY id (id)
		     );";
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($sql);

		$sql = "CREATE TABLE ".$table_name_2." (
	                id mediumint(9) NOT NULL AUTO_INCREMENT,
			bot_id mediumint(9) NOT NULL,
	                url VARCHAR(255) NOT NULL,
	                created DATETIME NOT NULL,
	                UNIQUE KEY id (id)
		);";
		dbDelta($sql);

		$client = new SoapClient("http://services.dschini.org/bot.php?WSDL");
                $key = $client->registerUrl($_SERVER["HTTP_HOST"]);		

		add_option("dschini_botchecker_options_key", $key, "Dschini Botchecker Key", "yes");
		add_option("dschini_botchecker_options_high", "1", "Dschini Botchecker Track High Popular Bots", "yes");
		add_option("dschini_botchecker_options_medium", "1", "Dschini Botchecker Track Medium Popular Bots", "yes");
		add_option("dschini_botchecker_options_low", "0", "Dschini Botchecker Track Low Popular Bots", "yes");
		add_option("dschini_botchecker_options_amountlist", "30", "Dschini Botchecker Management Amount of Tracks to display", "yes");

		dschini_botchecker_init(); 

		update_option('shoutbox_fade_from', "666666");
		update_option('shoutbox_fade_to', "FFFFFF");
		update_option('shoutbox_update_seconds', 4000);
	}
}

function dschini_botchecker_track()
{
	global $wpdb;
        $table_name_1 = $wpdb->prefix . "dschini_botchecker_bots";
        $table_name_2 = $wpdb->prefix . "dschini_botchecker_tracker";
        if($wpdb->get_var("show tables like '$table_name_1'") == $table_name_1) {
		$sql = sprintf("SELECT bot_id,exclusionuseragent FROM %s",$table_name_1);
		$bots = $wpdb->get_results( $sql );
		$bot_id = "";
		$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
		foreach($bots as $bot){
			if($bot->exclusionuseragent!=""){
				if(stristr($useragent,$bot->exclusionuseragent)){
	                	        $bot_id = $bot->bot_id;
	                	        break;
	                	}
			}
		}
		if($bot_id!=""){
			$sql = sprintf("INSERT INTO `%s` ( `bot_id` , `url` , `created` )
	                                VALUES (
	                                '%d', '%s', %s
	                                )"
					,$table_name_2
	                                ,$bot_id
	                                ,$_SERVER["REQUEST_URI"]
	                                ,"now()");
			$wpdb->query( $sql );
		}
	}
}

function dschini_botchecker_management_subpanel()
{
	global $wpdb;
	$table_name_1 = $wpdb->prefix . "dschini_botchecker_bots";
	$table_name_2 = $wpdb->prefix . "dschini_botchecker_tracker";
	?>
	<div class="wrap">
	<h2>Dschini Bot Checker</h2>

	<?php
	$sql = sprintf("SELECT COUNT(DISTINCT(bot_id)) FROM %s",$table_name_1);
	$ubots = $wpdb->get_var( $sql );
	$sql = sprintf("SELECT COUNT(DISTINCT(bot_id)) FROM %s",$table_name_2);
        $utbots = $wpdb->get_var( $sql );
	$sql = sprintf("SELECT COUNT(bot_id) FROM %s",$table_name_2);
	$maxTrack = $wpdb->get_var( $sql );

	$sql = sprintf("SELECT a.bot_id, count(*) as c, b.name, b.detailsurl, b.description, b.history FROM %s a, %s b 
				WHERE a.bot_id=b.bot_id 
				GROUP BY a.bot_id 
				ORDER BY c DESC 
				LIMIT 0,4",$table_name_2,$table_name_1);
        $highBots = $wpdb->get_results( $sql );

	$sql = sprintf("SELECT url, count(*) as c FROM %s
                                GROUP BY url
                                ORDER BY c DESC
                                LIMIT 0,4",$table_name_2);
        $highUrls = $wpdb->get_results( $sql );
	?>
	
	<div style="padding-bottom:22px">
	<p>
	<?php 
	_e('There are','dschini_botchecker_status_part_1');
	echo ' <b>'.$maxTrack.'</b> ';
	_e('<b>entries</b> tracked!','dschini_botchecker_status_part_2'); 
	echo ' <b>'.$utbots.'</b> ';
	_e('<b>different</b> from','dschini_botchecker_status_part_3');
	echo '<b>'.$ubots.'</b> ';
	_e('<b>available</b> bots are tracking your blog.','dschini_botchecker_status_part_4');
	?>
	</p>
	<p>
	<?php
	_e('<b>Most active bots:</b>','dschini_botchecker_status_mostactivebots');
	for($i=0; $i<count($highBots); $i++){
		echo ' <a title="'.$highBots[$i]->description.' '.$highBots[$i]->history.'" href="'.$highBots[$i]->detailsurl.'">'.$highBots[$i]->name.'</a>&nbsp;';
	}
	?>
	</p>
	<p>
        <?php
        _e('<b>Most tracked URL\'s:</b>','dschini_botchecker_status_mosttrackedurls');
        for($i=0; $i<count($highUrls); $i++){
                echo ' <a title="'.$highUrls[$i]->c.' Times" href="'.$highUrls[$i]->url.'">'.$highUrls[$i]->url.'</a>&nbsp;';
        }
        ?>
        </p>
	</div>

	<table id="the-list-x" width="100%" cellpadding="3" cellspacing="3">
	<tr>

	<th scope="col">ID</th>
	<th scope="col">Bot</th>
	<th scope="col">Owner</th>
	<th scope="col">Target</th>
	<th scope="col"></th>
	</tr>
	<?php
	$_limitTo = ((int)get_option('dschini_botchecker_options_amountlist')>0)?(int)get_option('dschini_botchecker_options_amountlist'):30;
	$sql = sprintf("SELECT 
				b.id AS id,
				a.bot_id AS bot_id,
				a.description AS description,
				a.purpose AS purpose,
				a.history AS history,
				a.license AS license,
				a.exclusionuseragent AS exclusionuseragent,
				a.ownername AS ownername,
				a.ownerurl AS ownerurl,
				a.owneremail AS owneremail,
				a.popularity AS popularity,
				a.name AS name,
				a.detailsurl AS detailsurl,
				b.url AS url,
				b.created AS created
			FROM %s a,%s b  
			WHERE a.bot_id=b.bot_id ORDER by b.id DESC LIMIT 0,%d",$table_name_1,$table_name_2,$_limitTo);
	$tracks = $wpdb->get_results( $sql );
	ob_start();
	_e('Today', 'dschini_botchecker_today');
	$today = ob_get_clean();
	$_i = 0;
	foreach($tracks AS $track){
		$_i++;
		echo '<tr id="'.$track->id.'" class="'.($_i%2==1?'alternate':'').'">';
	    	echo '<th scope="row">'.$track->id.'</th>';
	    	echo '<td><a title="'.$track->description.' / '.$track->history.'" href="'.$track->detailsurl.'">'.$track->name.'</a></td>';
	    	echo '<td>[<a href="mailto:'.$track->owneremail.'">EMail</a>] <a href="'.$track->ownerurl.'">'.$track->ownername.'</a></td>';
		echo '<td><a href="'.$track->url.'">'.$track->url.'</a></td>';
		$time = strtotime($track->created);
		$formatTime = (date("j")==date("j",$time))
				?($today." ".date("g:i a", $time))
				:date("F j, Y g:i a", $time);
                echo '<td>'.$formatTime.'</td>';
	    	echo '</tr>';
	}
	?>
	</table>
	<?php
}

function dschini_botchecker_options_subpanel()
{
	if (isset($_POST['info_update'])) {
	isset($_POST["dschini_botchecker_options_high"])
		?update_option("dschini_botchecker_options_high", "1")
		:update_option("dschini_botchecker_options_high", "0");
        isset($_POST["dschini_botchecker_options_medium"])
                ?update_option("dschini_botchecker_options_medium", "1")
                :update_option("dschini_botchecker_options_medium", "0");
        isset($_POST["dschini_botchecker_options_low"])
                ?update_option("dschini_botchecker_options_low", "1")
                :update_option("dschini_botchecker_options_low", "0");
        update_option("dschini_botchecker_options_amountlist", (int)$_POST["dschini_botchecker_options_amountlist"]);
	dschini_botchecker_init();
	?>
	<div class="updated"><p><strong>
	<?php 
	_e('Options are updated and local bot database synchronized with the <a href="http://services.dschini.org/bot.php" target="_blank">Dschini Bot Service</a>','dschini_botchecker_options_updated'); 
	?>
	</strong></p></div>
	<?php } ?>

	<div class=wrap>
	<form method="post">
	<h2><?php _e('Dschini Bot Checker Options', 'dschini_botchecker_options_title') ?></h2>
	<?php _e('Customize your Bot Checker Options here! The bots are seperated into High Popular, Medium Popular and Low Popular. These seperations are done by the <a href="http://services.dschini.org/bot.php" target="_blank">Dschini Bot Service</a> which tries to collect bot information in a public database.<br/>', 'dschini_botchecker_options_subtitle') ?>
	<p>
	<?php _e('When updating your options Dschini Bot Checker will synchronize your local bot database from the <a href="http://services.dschini.org/bot.php" target="_blank">Dschini Bot Service</a>.', 'dschini_botchecker_options_updatenote') ?>

	<table class="optiontable">

	<tr valign="top">
        <th scope="row"><?php _e('Key:', 'dschini_botchecker_options_key') ?></th>
        <td><label for="dschini_botchecker_options_key"><?php echo get_option("dschini_botchecker_options_key") ?></label></td>
	</tr>

	<tr valign="top">
	<th scope="row"><?php _e('Track:', 'dschini_botchecker_options_track') ?></th>
	<td><label for="dschini_botchecker_options_high">
	<input name="dschini_botchecker_options_high" type="checkbox" <?php
	echo (get_option("dschini_botchecker_options_high")=="1")?'checked="checked"':'' ?> id="dschini_botchecker_options_high" value="1"  />
	 <?php _e('<b>High Popular Bots</b> [GoogleBot, MSNBot, ...]', 'dschini_botchecker_options_high') ?></label><br />
	<label for="dschini_botchecker_options_medium">
	<input name="dschini_botchecker_options_medium" type="checkbox" <?php
        echo (get_option("dschini_botchecker_options_medium")=="1")?'checked="checked"':'' ?> id="dschini_botchecker_options_medium" value="1"  />
	 <?php _e('<b>Medium Popular Bots</b> [FAST Crawler, Speedy Spider, ...]', 'dschini_botchecker_options_medium') ?></label><br/>
	<label for="dschini_botchecker_options_low">
	<input name="dschini_botchecker_options_low" type="checkbox" <?php
        echo (get_option("dschini_botchecker_options_low")=="1")?'checked="checked"':'' ?> id="dschini_botchecker_options_low" value="1"  />
	 <?php _e('<b>Low Popular Bots</b> [ASpider, BBot, ...]', 'dschini_botchecker_options_low') ?></label><br/>
	</td>
	</tr>

	<tr valign="top">
	<th scope="row"><?php _e('Display:','dschini_botchecker_options_display') ?></th>
        <td><select name="dschini_botchecker_options_amountlist" id="dschini_botchecker_options_amountlist">
	<?php
	$_displayAmountList = array(5,10,20,30,50,100,200);
	foreach($_displayAmountList AS $item){
		echo '<option '.(get_option('dschini_botchecker_options_amountlist')==$item?'selected="selected" ':' ').'value="'.$item.'" >Last '.$item.' Tracks</option>';
	}
	?>
	</select>

        <tr valign="top">
	<th></th>
        <td><label for="dschini_botchecker_notifyemail">
	<div class="submit">
	<input type="submit" name="info_update" value="<?php
		_e('Update options', 'dschini_botchecker_options_update')
		?> »" /></div>
	</div>
        </td>
        </tr>
	</table>
	</form>
	</div>
<?php
}

/* The Page Controller */
function dschini_botchecker_controller(){
	add_options_page('Dschini Bot Checker', 'Dschini Bot Checker', 8, __FILE__, 'dschini_botchecker_options_subpanel');
	add_management_page('Dschini Bot Checker', 'Dschini Bot Checker', 8, __FILE__, 'dschini_botchecker_management_subpanel');
}

/* The Hooks */
add_action('activate_dschini/botchecker.php','dschini_botchecker_install');
add_action('admin_menu', 'dschini_botchecker_controller');

/* Track */
dschini_botchecker_track();
?>
