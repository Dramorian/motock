<?php
/*
Plugin Name: DCaptcha
Plugin URI: http://dimoning.ru
Description: Smart captcha for wordpress comments.
Author: DimoninG
Version: 0.1
Author URI: http://dimoning.ru
*/

add_action('comment_post', "comment_post");
add_action('comment_form', "dcaptcha_draw");

function comment_post ($id){
	global $user_ID;

	if ($user_ID){
		return $id;
	}

	if ($_POST['dcaptcha_sess'] != '1'){
		wp_set_comment_status($id, 'delete');
		echo "Вы забыли поставить галочку в красном квадратике, Я не робот, вернитесь и повторите попытку.";
		exit;
	}
}

function dcaptcha_draw ($id){
	global $user_ID;

	if ($user_ID){
		return $id;
	}

	?>
	<style>
	.dcaptcha_red{
		display: inline-block;
		font-family: arial;
		font-size: 12px;
		color: #AA0000;
		padding: 5px;
		background: #AA0000;
	}

	.dcaptcha_yellow{
		display: inline-block;
		font-family: arial;
		font-size: 12px;
		color: #AA0000;
		padding: 5px;
		background: #FFFFFF;
	}
	</style>

	<script language="javascript">
	function dcaptcha_change(){
		if (document.getElementById('dcaptcha_captcha1').className == "dcaptcha_yellow"){
			document.getElementById('dcaptcha_captcha1').className = "dcaptcha_red";
			document.getElementById('dcaptcha_sess').value = 0;
		}
		else{
			document.getElementById('dcaptcha_captcha1').className = "dcaptcha_yellow";
			document.getElementById('dcaptcha_sess').value = 1;
		}
	}

	</script>

	<div id="dcaptcha"><p>

		<input type="checkbox" class="dcaptcha_red" id="dcaptcha_captcha1" onclick="dcaptcha_change();" value="Я - человек!">
		Я не робот.<br>
	</div>
	<input type="hidden" name="dcaptcha_sess" id="dcaptcha_sess" value="0">

	<script>
	var commentField = document.getElementById("url");
    var submitp = commentField.parentNode;
    var answerDiv = document.getElementById("dcaptcha");	    
    submitp.appendChild(answerDiv, commentField);
</script>
	<?php
}
?>