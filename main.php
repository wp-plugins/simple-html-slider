<?php
/*
Plugin Name: Simple Html slider
Plugin URI: http://www.wpfruits.com
Description: This is simple Html content carousel. Put Your HTML CONTENT directly in the slides. Use more than two slides for better performance. This is continuous carousel.
Author: WPFruits
Version: 1.0
Author URI: http://www.wpfruits.com
*/
function shs_slider_init_method() {
    wp_enqueue_script('jquery');
	if(is_admin())
	{
		if(isset($_REQUEST['page']))
		{
			if($_REQUEST['page']=="shs_slider_options")
			{
			 wp_enqueue_script('jquery-ui-sortable');
			}
		}
	}
	
}    
 
add_action('init', 'shs_slider_init_method');

function shs_slider_install() 
	{
   	$shs_settings['pause_time']=7000;
	$shs_settings['trans_time']=1000;
	$shs_settings['width']="250px";
	$shs_settings['height']="200px";
	$shs_settings['direction']="Up";
	$shs_settings['pause_on_hover']="Yes";
	add_option("shs_slider_settings", $shs_settings);
   }
register_activation_hook(__FILE__,'shs_slider_install');
function shs_slider_show()
{
$shs_settings=get_option('shs_slider_settings');
	$pause_time=$shs_settings['pause_time'];
	$trans_time=$shs_settings['trans_time'];
	$width=$shs_settings['width'];
	$height=$shs_settings['height'];
	$direction=$shs_settings['direction'];
	$pause_hover=$shs_settings['pause_on_hover'];
	
	if($direction=="Left"||$direction=="Right")
	{
	$li_style="style='width:$width;height:$height;float:left;margin:0;padding:0;overflow:hidden;'";
	}
	else
	{
	$li_style="style='width:$width;height:$height;margin:0;padding:0;overflow:hidden;'";
	}
$toret='<div id="shs_slider_cont" style="width:'.$width.';height:'.$height.';overflow:hidden;">
			<div class="shs_slider_wrp" id="shs_slider_ul" style="width:'.$ul_width.';list-style-type:none;position:relative;margin:0;padding:0;" >';				

				$contents=get_option('shs_slider_contents');
				if($contents)
				{
					foreach($contents as $content)
					{
						if($content)
						{
						$content=stripslashes($content);
    					$toret.="<div class='shs_items' $li_style >".$content."</div>";
						} // if($content)
					}// 	foreach($contents as $content)
				} // if($contents)
 $toret.="</div>
		</div>
		";
   $toret.="
        <script type='text/javascript'>
jQuery(document).ready(function() {";
switch($direction)
{
		case "Up":
		$toret.="
		function shs_animate()
		{
		var item_height = jQuery('#shs_slider_ul .shs_items').outerHeight();
		var top_indent = parseInt(jQuery('#shs_slider_ul').css('top')) - item_height;
		jQuery('#shs_slider_ul:not(:animated)').animate({'top' : top_indent},$trans_time,
		function(){
			jQuery('#shs_slider_ul .shs_items:last').after(jQuery('#shs_slider_ul .shs_items:first'));
			jQuery('#shs_slider_ul').css({'top':'0'});
					});
		}"; 
		break;
		case "Down":
	$toret.="
	jQuery('#shs_slider_ul .shs_items:first').before(jQuery('#shs_slider_ul .shs_items:last'));
	jQuery('#shs_slider_ul').css({'top':'-$height'});
	function shs_animate()
		{
		var item_height = jQuery('#shs_slider_ul .shs_items').outerHeight();
		var top_indent = parseInt(jQuery('#shs_slider_ul').css('top')) + item_height;
		jQuery('#shs_slider_ul:not(:animated)').animate({'top' : top_indent},$trans_time,
		function(){
			jQuery('#shs_slider_ul .shs_items:first').before(jQuery('#shs_slider_ul .shs_items:last'));
			jQuery('#shs_slider_ul').css({'top':'-$height'});
					});
					}";
	break;
	case "Right":
	$toret.="
	jQuery('#shs_slider_ul .shs_items:first').before(jQuery('#shs_slider_ul .shs_items:last'));
	var item_width = jQuery('#shs_slider_ul .shs_items').outerWidth();
	var total_width=jQuery('#shs_slider_ul .shs_items').length;
	jQuery('#shs_slider_ul').css({'left':'-$width','width':item_width*total_width+10});
	jQuery('#shs_slider_ul .shs_items').css({'float':'left'});
	function shs_animate()
		{
		var item_width = jQuery('#shs_slider_ul .shs_items').outerWidth();
		var left_indent = parseInt(jQuery('#shs_slider_ul').css('left')) + item_width;
		jQuery('#shs_slider_ul:not(:animated)').animate({'left' : left_indent},$trans_time,
		function(){
			jQuery('#shs_slider_ul .shs_items:first').before(jQuery('#shs_slider_ul .shs_items:last'));
			jQuery('#shs_slider_ul').css({'left':'-$width'});
					});
					}";
	break;
	case "Left":
	$toret.="
	var item_width = jQuery('#shs_slider_ul .shs_items').outerWidth();
	var total_width=jQuery('#shs_slider_ul .shs_items').length;
	jQuery('#shs_slider_ul').css({'left':'0','width':item_width*total_width+10});
	jQuery('#shs_slider_ul .shs_items').css({'float':'left'});
		function shs_animate()
		{
		var item_width = jQuery('#shs_slider_ul .shs_items').outerWidth();
		var left_indent = parseInt(jQuery('#shs_slider_ul').css('left')) -item_width;
		jQuery('#shs_slider_ul:not(:animated)').animate({'left' : left_indent},$trans_time,
		function(){
			jQuery('#shs_slider_ul .shs_items:last').after(jQuery('#shs_slider_ul .shs_items:first'));
			jQuery('#shs_slider_ul').css({'left':'0'});
					});
		}";
	break;
	}//switch($direction)
if($pause_hover=="No")
{
$toret.="
var shs=setInterval(function(){ shs_animate(); },$pause_time);";
}
else
{
$toret.="
var shs=setInterval(function(){ shs_animate(); },$pause_time);
jQuery('#shs_slider_cont').hover(function(){ clearInterval(shs); },function(){ shs=setInterval(function(){ shs_animate(); },$pause_time); });";
}
$toret.="
})
</script>";
return $toret;
}

add_shortcode('shs_slider_show', 'shs_slider_show');

function shs_slider_view($ech=true)
{
	if($ech)
	{
	echo shs_slider_show();
	}
	else
	{
	shs_slider_show();
	return shs_slider_show();
	}
}

add_action('admin_menu', 'shs_slider_add_menu');

function shs_slider_add_menu() {
add_options_page('Simple HTML slider', 'Simple HTML slider', 'administrator','shs_slider_options', 'shs_slider_menu_op');
}

function shs_slider_menu_op() {
	echo '<div class="wrap">';
	echo '<h2>Simple HTML Slider </h2>';
	echo "<h5>Use shortcode [shs_slider_show] or <br/>
	Use &lt;?php if(function_exists('shs_slider_view')){ shs_slider_view(); } ?&gt;</h5>";
	echo "<h4>Settings:</h4>";
	if(isset($_POST['jsetsub']))
	{
	$pause_time=$_POST['pause_time'];
	$trans_time=$_POST['trans_time'];
	$width=$_POST['width'];
	$height=$_POST['height'];
	$direction=$_POST['direction'];
	$pause_hover=$_POST['pause_on_hover'];
	$shs_settings['pause_time']=$pause_time;
	$shs_settings['trans_time']=$trans_time;
	$shs_settings['width']=$width;
	$shs_settings['height']=$height;
	$shs_settings['direction']=$direction;
	$shs_settings['pause_on_hover']=$pause_hover;
	update_option('shs_slider_settings',$shs_settings);
	?>
      <div class="updated"><p><strong><font color="green"><?php _e('Setting Saved' ); ?></font></strong></p></div>
    <?php
	}
	$shs_settings=get_option('shs_slider_settings');
	$pause_time=$shs_settings['pause_time'];
	$trans_time=$shs_settings['trans_time'];
	$width=$shs_settings['width'];
	$height=$shs_settings['height'];
	$direction=$shs_settings['direction'];
	$pause_hover=$shs_settings['pause_on_hover'];
	echo "<form name='settings' method='post'>";
	echo "<table>";
	?>
    <tr><td>Width</td><td><input type='text' name='width' value='<?php echo $width; ?>' /> eg:200px</td></tr>
    <tr><td>Height</td><td><input type='text' name='height' value='<?php echo $height; ?>' /> eg:200px</td></tr>
    <tr><td>Pause on Hover</td><td>
    <select name="pause_on_hover">
    <option <?php shs_check_for_selected($pause_hover,"Yes"); ?> >Yes</option>
    <option <?php shs_check_for_selected($pause_hover,"No"); ?> >No</option>
    </select></td></tr>
    <tr><td>Direction</td><td>
    <select name="direction">
    <option <?php shs_check_for_selected($direction,"Left"); ?> >Left</option>
    <option <?php shs_check_for_selected($direction,"Right"); ?> >Right</option>
    <option <?php shs_check_for_selected($direction,"Up"); ?> >Up</option>
    <option <?php shs_check_for_selected($direction,"Down"); ?> >Down</option>
    </select></td></tr>
	<tr><td>Pause time</td><td><input type='text' name='pause_time' value='<?php echo $pause_time; ?>' /> eg:7000</td></tr>
	<tr><td>Transition time</td><td><input type='text' name='trans_time' value='<?php echo $trans_time; ?>' /> eg:1000</td></tr>
	<tr><td></td><td><input type='submit' name='jsetsub'  class='button-primary' value='save settings' /></td></tr>
    <?php
	echo "</table>";
	echo "</form>";
	?>
    <?php
	if(isset($_POST['joptsv']))
	{
	$contents=$_POST['cnt'];
	update_option('shs_slider_contents',$contents);
	?>
    <div class="updated"><p><strong><font color="green"><?php _e('Saved' ); ?></font></strong></p></div>
    <?php
	}
	?>
      <style>
    #joptions{ list-style-type: none; margin: 0; padding: 0; }
    
    </style>
	<div>
    <h3>Add slider contents below (Drap up-down to re-order)</h3>
    <h5>
    Note:You can add HTML here.
    <br/>
    More Than two slides Recommended.
    </h5>
    <form name="qord" method="post">

	<ul id="joptions">
    <?php
	$contents=get_option('shs_slider_contents');
	if($contents)
	{
	foreach($contents as $content)
	{
		if($content)
		{
		$content=stripslashes($content);
	?>
    <li><textarea name="cnt[]" rows="3" style="width:70%;" ><?php echo $content; ?></textarea><input type="button" value="Delete" onClick="delete_field(this);"  /><input type="button" value="Add new" onClick="add_to_field(this);"  /></li>
    <?php
		} // if($content)
	}// 	foreach($contents as $content)
	} // if($contents)
	?>
	<li><textarea name="cnt[]" rows="3" style="width:70%;" ></textarea><input type="button" value="Delete" onClick="delete_field(this);"  /><input type="button" value="Add new" onClick="add_to_field(this);"  /></li>

    </ul>

    <input type="submit" name="joptsv" class="button-primary" value="save now" />

    </form>

    </div>
  	<script type="text/javascript">

		function add_to_field(field)

		{

		var field_html = '<textarea name="cnt[]" rows="3" style="width:70%;" ></textarea>';

			field_html += "<input type=\"button\" value=\"Delete\" onClick=\"delete_field(this);\"  /><input type=\"button\" value=\"Add new\" onClick=\"add_to_field(this);\"  />";

			jQuery(field).parent().after("<li style='display:none;'>" + field_html + "</li>");
			jQuery(field).parent().next().slideDown();
		}

			function delete_field( field ) {
			jQuery(field).parent().slideUp('fast', function(e){ jQuery(this).html(''); });
		}
		jQuery(document).ready(function() {
  			jQuery( "#joptions" ).sortable();
			jQuery( "#joptions li" ).css({'cursor':'move'});
		});

	</script>
    
	<?php
	echo '</div>'; // .wrap
}

function shs_check_for_selected($option,$check)
{
	if($option==$check)
	{
	echo "selected='selected'";
	}
}

function shs_slider_cont_count()
{
global $wpdb;
$number=0;
$contents=get_option('shs_slider_contents');
				if($contents)
				{
					foreach($contents as $content)
					{
						if($content)
						{
						$number++;
						} 
					}
				}
return $number;
}
