<?php
/*
Plugin Name: FeedOnly
Plugin URI: http://coolidea.ru/2008/06/07/feedonly
Description: Allows to hide parts of postings from common readers and shows it to feed-subscribers. Place the text you want to show only to your subscribers between &lt;!&ndash;&ndash;hide&ndash;&ndash;&gt; and &lt;!&ndash;&ndash;/hide&ndash;&ndash;&gt;.
Version: 1.0
Author: Oleg Shalomanov
Author URI: http://coolidea.ru
*/

add_filter('the_content', 'cool_hide');

define (start_hide_tag, '<!--hide-->');
define (finish_hide_tag, '<!--/hide-->');
define (start_hide_tag_length, strlen(start_hide_tag));
define (finish_hide_tag_length, strlen(finish_hide_tag));

$blog_feed_url='/wp-rss2.php';

//you can change the values of variables below
$hidden_content_mark = '</p>[&#1058;&#1086;&#1083;&#1100;&#1082;&#1086; &#1076;&#1083;&#1103; rss-&#1087;&#1086;&#1076;&#1087;&#1080;&#1089;&#1095;&#1080;&#1082;&#1086;&#1074;. <a href="'.$blog_feed_url.'">&#1055;&#1086;&#1076;&#1087;&#1080;&#1096;&#1080;&#1090;&#1077;&#1089;&#1100;</a> &#1076;&#1083;&#1103; &#1088;&#1077;&#1075;&#1091;&#1083;&#1103;&#1088;&#1085;&#1086;&#1075;&#1086; &#1087;&#1086;&#1083;&#1091;&#1095;&#1077;&#1085;&#1080;&#1103; &#1101;&#1082;&#1089;&#1082;&#1083;&#1102;&#1079;&#1080;&#1074;&#1085;&#1086;&#1081; &#1080;&#1085;&#1092;&#1086;&#1088;&#1084;&#1072;&#1094;&#1080;&#1080;.]<p>';
$hidden_content_start = '</p>&lt;&#1069;&#1082;&#1089;&#1082;&#1083;&#1102;&#1079;&#1080;&#1074;&#1085;&#1086; &#1076;&#1083;&#1103; &#1087;&#1086;&#1076;&#1087;&#1080;&#1089;&#1095;&#1080;&#1082;&#1086;&#1074;&gt;&nbsp;<p>';
$hidden_content_finish = '</p>&nbsp;&lt;/&#1069;&#1082;&#1089;&#1082;&#1083;&#1102;&#1079;&#1080;&#1074;&#1085;&#1086; &#1076;&#1083;&#1103; &#1087;&#1086;&#1076;&#1087;&#1080;&#1089;&#1095;&#1080;&#1082;&#1086;&#1074;&gt;<p>';
//

function cool_hide ($content)
{
	global $hidden_content_mark, $hidden_content_start, $hidden_content_finish;

	$processedContent = $content;
	$patternFound = true;

	while ($patternFound == true)
	{
		$startPos = strpos($processedContent, start_hide_tag);

		if ($startPos === false)
		{
			$patternFound = false;
		}
		else
		{
			$endPos = strpos($processedContent, finish_hide_tag, $startPos);

			if ($endPos === false) {
				// end tag is absent
				if (is_feed()) {
					$processedContent = substr($processedContent,0,$startPos) . $hidden_content_start . substr($processedContent, $startPos + start_hide_tag_length) . $hidden_content_finish;					
				} else {
					$processedContent = substr($processedContent,0,$startPos) . $hidden_content_mark;
				}
			} else {
				if (is_feed()) {
					$processedContent = substr($processedContent,0,$startPos) . $hidden_content_start . substr($processedContent, $startPos + start_hide_tag_length, $endPos - ($startPos + start_hide_tag_length)) . $hidden_content_finish . substr($processedContent,$endPos + finish_hide_tag_length);
				} else {
					$processedContent = substr($processedContent,0,$startPos) . $hidden_content_mark . substr($processedContent,$endPos + finish_hide_tag_length);
				}
			}
		}
	}
	return $processedContent;
}
?>