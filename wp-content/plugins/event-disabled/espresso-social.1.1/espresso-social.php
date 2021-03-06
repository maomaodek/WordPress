<?php
/*
Plugin Name: Event Espresso - Social Media
Plugin URI: http://www.eventespresso.com
Description: A social media addon for Event Espresso. Includes includes Facebook and Twitter share buttons.
Version: 1.1
Usage: Add <?php echo espresso_show_social_media($event_id, 'twitter');?> and/or <?php echo espresso_show_social_media($event_id, 'facebook');?> to display  Twitter or Facebook buttons in your event templates.
Example: <p><?php echo espresso_show_social_media($event_id, 'twitter');?> <?php echo espresso_show_social_media($event_id, 'facebook');?></p>
Author: Seth Shoultes
Author URI: http://www.shoultes.net
Copyright 2010  Seth Shoultes  (email : seth@eventespresso.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Define the version of the plugin
function espresso_social_version() {
	return '1.1';
}
define("ESPRESSO_SOCIAL_VERSION", espresso_social_version() );

//Define the plugin directory and path
define("ESPRESSO_SOCIAL_PLUGINPATH", "/" . plugin_basename( dirname(__FILE__) ) . "/");
define("ESPRESSO_SOCIAL_PLUGINFULLPATH", WP_PLUGIN_DIR . ESPRESSO_SOCIAL_PLUGINPATH  );
define("ESPRESSO_SOCIAL_PLUGINFULLURL", WP_PLUGIN_URL . ESPRESSO_SOCIAL_PLUGINPATH );

//Globals
global $espresso_facebook;
$espresso_facebook = get_option('espresso_facebook_settings');

global $espresso_twitter;
$espresso_twitter = get_option('espresso_twitter_settings');

global $espresso_google;
$espresso_google = get_option('espresso_google_settings');

/* just say no to stumbleupon - cb #626
global $espresso_stumbleupon;
$espresso_stumbleupon = get_option('espresso_stumbleupon_settings');
*/

//Install the plugin
function espresso_social_install(){
	// Install Facebook Options
	$espresso_facebook = array(
					'espresso_facebook_layout' => 'button_count',
					'espresso_facebook_faces' => 'true',
					'espresso_facebook_action' => 'like',
					'espresso_facebook_font' => 'arial',
					'espresso_facebook_colorscheme' => 'light',
					'espresso_facebook_height' => '21',
					'espresso_facebook_width' => '450'
				);
	add_option( 'espresso_facebook_settings', $espresso_facebook );
	
	// Install Twitter Options
	$espresso_twitter = array(
					'espresso_twitter_text' => get_bloginfo('name'),
					'espresso_twitter_username' => 'EventEspresso',
					'espresso_twitter_count_box' => 'none',
					'espresso_twitter_lang' => 'en'
				);
	add_option( 'espresso_twitter_settings', $espresso_twitter );

	// Install  google+1 options
	$espresso_google = array(
				'espresso_google_button_size' => 'small',
				'espresso_google_url' => '',
				'espresso_google_annotation' => 'bubble'
				);
	update_option( 'espresso_google_settings', $espresso_google);
	
 // Install  stumbleupon options
 /* just say no to stumbleupon - cb #626
	$espresso_stumbleupon = array(
				'espresso_stumbleupon_button_style' => '2',
				'espresso_stumbleupon_button_url' => ''
				);	
	update_option('espresso_stumbleupon_settings', $espresso_stumbleupon); */
}	

register_activation_hook(__FILE__, 'espresso_social_install');

function espresso_social_config_mnu()	{
	global $wpdb, $espresso_twitter, $espresso_facebook, $espresso_google;
	
	/*Facebok*/
	function espresso_facebook_updated(){
	echo '<div class="updated fade"><p>' .  __('Facebook details saved.','event_espresso') . '</p></div>';
	}
	
	if (isset($_POST['update_facebook'])) {
		$espresso_facebook['espresso_facebook_layout'] = $_POST['espresso_facebook_layout'];
		$espresso_facebook['espresso_facebook_faces'] = $_POST['espresso_facebook_faces'];
		$espresso_facebook['espresso_facebook_action'] = $_POST['espresso_facebook_action'];
		$espresso_facebook['espresso_facebook_font'] = $_POST['espresso_facebook_font'];
		$espresso_facebook['espresso_facebook_colorscheme'] = $_POST['espresso_facebook_colorscheme'];
		$espresso_facebook['espresso_facebook_height'] = $_POST['espresso_facebook_height'];
		$espresso_facebook['espresso_facebook_width'] = $_POST['espresso_facebook_width'];
		
		update_option( 'espresso_facebook_settings', $espresso_facebook);
		add_action( 'admin_notices', 'espresso_facebook_updated');
	}
	$espresso_facebook = get_option('espresso_facebook_settings');
	
	/*Twitter*/
	function espresso_twitter_updated(){
	echo '<div class="updated fade"><p>'.  __('Twitter details saved.','event_espresso') . '</p></div>';
	}

	if (isset($_POST['update_twitter'])) {
		$espresso_twitter['espresso_twitter_text'] = stripslashes_deep($_POST['espresso_twitter_text']);
		$espresso_twitter['espresso_twitter_username'] = $_POST['espresso_twitter_username'];
		$espresso_twitter['espresso_twitter_count_box'] = $_POST['espresso_twitter_count_box'];
		$espresso_twitter['espresso_twitter_lang'] = $_POST['espresso_twitter_lang'];
		
		update_option( 'espresso_twitter_settings', $espresso_twitter);
		add_action( 'admin_notices', 'espresso_twitter_updated');
	}

	$espresso_twitter = get_option('espresso_twitter_settings');

	/*Google*/
	function espresso_google_updated(){
	echo '<div class="updated fade"><p>'. __('Google details saved.','event_espresso') . '</p></div>';
	}
		
	if ( isset($_POST['update_google']) ){
		$espresso_google['espresso_google_button_size'] = $_POST['espresso_google_button_size'];
		$espresso_google['espresso_google_url'] = $_POST['espresso_google_url'];
		$espresso_google['espresso_google_annotation'] = $_POST['espresso_google_annotation'];
		
		update_option('espresso_google_settings', $espresso_google);
		add_action('admin_notices', 'espresso_google_updated');
	}
		
	$espresso_google = get_option('espresso_google_settings');
	
	/*Stumbleupon*/
	/* just say no to stumbleupon - cb #626
	function espresso_stumbleupon_updated(){
	echo '<div class="updated fade"><p>'. __('Stumbleupon details saved.','event_espresso') . '</p></div>';
	}
		
	if ( isset($_POST['update_stumbleupon']) ){
		$espresso_stumbleupon['espresso_stumbleupon_button_style'] = $_POST['espresso_stumbleupon_button_style'];
		$espresso_stumbleupon['espresso_stumbleupon_url'] = $_POST['espresso_stumbleupon_url'];
		
		update_option('espresso_stumbleupon_settings', $espresso_stumbleupon);
		add_action('admin_notices', 'espresso_stumbleupon_updated');
	}
		
	$espresso_stumbleupon = get_option('espresso_stumbleupon_settings');	
*/
?>

<div id="configure_organization_form" class="wrap meta-box-sortables ui-sortable clearfix">

<div id="icon-options-event" class="icon32"> </div>
	<h2>
		<?php _e('Event Espresso - Social Media Settings','event_espresso'); ?>
	</h2>
	<?php do_action('admin_notices')?>
<!-- include right sidebar  -->
		
	<div id="poststuff" class="metabox-holder has-right-sidebar">
	<?php event_espresso_display_right_column(); ?>
		<div id="post-body">
			<div id="post-body-content">
			
	<!-- begin left column metaboxes  -->
				<div class="meta-box-sortables ui-sortables">

<!--  Start Facebook settings  -->
				<div class="metabox-holder">
					<div class="postbox">
						<div title="Click to toggle" class="handlediv"><br /></div>
						<h3 class="hndle">
						<?php _e('Facebook Settings ', 'event_espresso'); ?>
						</h3>
						<div class="inside">
							<div class="padding">  
							<form class="espresso_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']?>">
								<table id="event_espresso-facebook" class="form-table">
								<tbody>
									<tr>
										<th>
											<label for="espresso_facebook_layout">
												<?php _e('Layout Style:','event_espresso'); ?>
											</label>
										</th>
										<td>
										<?php
										$values=array(
											array('id'=>'button_count','text'=> __('Horizontal Button Count','event_espresso')),
											array('id'=>'standard','text'=> __('Horizontal Standard','event_espresso')),
											array('id'=>'box_count','text'=> __('Vertical','event_espresso'))
											);				
											echo select_input('espresso_facebook_layout', $values, $espresso_facebook['espresso_facebook_layout'], 'id="espresso_facebook_layout"');
											?>
										</td>
									</tr>
									<tr>
										<th>
											<label for="espresso_facebook_faces">
											 <?php _e('Show Faces:','event_espresso'); ?>
											</label>
										</th>
										<td>
											<?php
											$values=array(
												array('id'=>'true','text'=> __('Yes','event_espresso')),
												array('id'=>'false','text'=> __('No','event_espresso'))
											);
											echo select_input('espresso_facebook_faces', $values, $espresso_facebook['espresso_facebook_faces'], 'id="espresso_facebook_faces"');
											?>
										</td>
										</tr>
										<tr>
											<th>
												<label for="espresso_facebook_font">
													<?php _e('Font:','event_espresso'); ?>
												</label>
											</th>
											<td>
												<?php
												$values=array(
													array('id'=>'arial','text'=> __('arial','event_espresso')),
													array('id'=>'lucida grande','text'=> __('lucida grande','event_espresso')),
													array('id'=>'segoe ui','text'=> __('segoe ui','event_espresso')),
													array('id'=>'tahoma','text'=> __('tahoma','event_espresso')),
													array('id'=>'trebuchet ms','text'=> __('trebuchet ms','event_espresso')),
													array('id'=>'verdana','text'=> __('verdana','event_espresso'))
												);
												echo select_input('espresso_facebook_font', $values, $espresso_facebook['espresso_facebook_font'], 'id="espresso_facebook_font"');
												?>
											</td>
										</tr>
										<tr>
											<th>
												<label for="espresso_facebook_colorscheme">
													<?php _e('Color Scheme:','event_espresso'); ?>
												</label>
											</th>
											<td>
											<?php
											$values=array(
												array('id'=>'light','text'=> __('Light','event_espresso')),
												array('id'=>'dark','text'=> __('Dark','event_espresso'))
											);
											echo select_input('espresso_facebook_colorscheme', $values, $espresso_facebook['espresso_facebook_colorscheme'], 'id="espresso_facebook_colorscheme"');
											?>
										</td>
									</tr>
								</tbody>
							</table>
							<p>
								<input type="hidden" name="update_facebook" value="update" />
								<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Facebook Options', 'event_espresso'); ?>" id="save_facebook_settings" />
							</p>
					</form>

				</div><!-- / .padding -->
			</div><!-- / .inside -->
		</div><!-- / .postbox -->
</div><!-- / .metabox-holder -->
<!--  end Facebook settings  -->

<!--  Twitter settings  -->
		<div class="metabox-holder">
		<div class="postbox">
		<div title="Click to toggle" class="handlediv"><br /></div>
		<h3 class="hndle">
			<?php _e('Twitter Settings ', 'event_espresso'); ?>
		</h3>
		<div class="inside">
			<div class="padding">
				<form class="espresso_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']?>">
					<table id="event_espresso-facebook" class="form-table">
						<tbody>
							<tr>
								<th>
									<label for="espresso_twitter_username">
										<?php _e('Twitter Username:','event_espresso'); ?>
									</label>
								</th>
								<td>
									<input id="espresso_twitter_username" type="text" name="espresso_twitter_username" size="30" maxlength="20" value="<?php echo $espresso_twitter['espresso_twitter_username'];?>" />
								</td>
							</tr>
							<tr>
								<th>
									<label for="espresso_twitter_count_box">
										<?php _e('Count Box Position:','event_espresso'); ?>
									</label>
								</th>
								<td>
								<?php
								$values=array(					
									array('id'=>'none','text'=> __('None','event_espresso')),
									array('id'=>'horizontal','text'=> __('Horizontal','event_espresso')),
									array('id'=>'vertical','text'=> __('Vertical','event_espresso'))
								);
								echo select_input('espresso_twitter_count_box', $values, $espresso_twitter['espresso_twitter_count_box'], 'id="espresso_twitter_count_box"');
								?>
								</td>
							</tr>
							<tr>
								<th>
									<label for="espresso_twitter_lang">
										<?php _e('The language for the Tweet Button:','event_espresso'); ?>
									</label>
								</th>
								<td>
								<?php
								$values=array(
									array('id'=>'en','text'=> __('English','event_espresso')),
									array('id'=>'da','text'=> __('Danish','event_espresso')),
									array('id'=>'dl','text'=> __('Dutch','event_espresso')),
									array('id'=>'fr','text'=> __('French','event_espresso')),
									array('id'=>'de','text'=> __('German','event_espresso')),
									array('id'=>'es','text'=> __('Spanish','event_espresso'))
								);
								echo select_input('espresso_twitter_lang', $values, $espresso_twitter['espresso_twitter_lang'], 'id="espresso_twitter_lang"');
								?>
							</td>
						</tr>
					</tbody>
				</table>
				<p>
					<input type="hidden" name="update_twitter" value="update" />
					<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Twitter Options', 'event_espresso'); ?>" id="save_twitter_settings" />
				</p>
			</form>

			</div><!-- / .padding -->
		</div><!-- / .inside -->
	</div><!-- / .postbox -->
	</div><!-- / .metabox-holder -->
<!--  end twitter settings  -->

<!--  Google+1 settings  --> 
	<div class="metabox-holder">
		<div class="postbox">
		<div title="Click to toggle" class="handlediv"><br /></div>
			<h3 class="hndle">
				<?php _e('Google+1  Settings ', 'event_espresso'); ?>
			</h3>
			<div class="inside">
				<div class="padding">
					<form class="espresso_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']?>">
					<table id="event_espresso-facebook" class="form-table">
						<tbody>
							<tr>
								<th>
									<label for="espresso_google_button_size">
										<?php _e('Google Button size:','event_espresso'); ?>
									</label>
								</th>
								<td>
								<?php
								$values=array(
									array('id'=>'medium','text'=> __('Horizontal','event_espresso')),
									array('id'=>'tall','text'=> __('Vertical','event_espresso'))
								);
								echo select_input('espresso_google_button_size', $values, $espresso_google['espresso_google_button_size'], 'id="espresso_google_button_size"');
								?>
								</td>
							</tr>
							<tr>
								<th>
									<label for="espresso_google_annotation">
										<?php _e('Google text display:','event_espresso'); ?>
									</label>
								</th>
								<td>
								<?php
									$values=array(
										array('id'=>'none','text'=> __('No Text','event_espresso')),					
										array('id'=>'inline','text'=> __('Inline Text','event_espresso')),
										array('id'=>'bubble','text'=> __('In Speech Bubble','event_espresso'))
									);	
									echo select_input('espresso_google_annotation', $values, $espresso_google['espresso_google_annotation'], 'id="espresso_google_annotation"');
									?>
								</td>
							</tr>
						</tbody>
					</table>
					<p>
						<input type="hidden" name="update_google" value="update" />
						<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Google Options', 'event_espresso'); ?>" id="save_google_settings" />
					</p>
					</form>
				</div><!-- / .padding -->
			</div><!-- / .inside -->
		</div><!-- / .postbox -->
	</div><!-- / .metabox-holder -->
<!--  End Google+1 settings  -->

<!--  Stumbleupon settings  -->
<?php /* just say no to stumbleupon - cb #626
	<div class="metabox-holder">
		<div class="postbox">
		<div title="Click to toggle" class="handlediv"><br /></div>
			<h3 class="hndle">
				<?php _e('Stumbleupon  Settings ', 'event_espresso'); ?>
			</h3>
			<div class="inside">
				<div class="padding">
					<form class="espresso_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']?>">
					<table id="event_espresso-facebook" class="form-table">
						<tbody>
							<tr>
							<th>
								<label for="espresso_stumbleupon_button_style">
									<?php _e('Stumbleupon button styles:','event_espresso'); ?>
								</label>
							</th>
							<td>
							<?php
							$values=array(
								array('id'=>'1','text'=> __('Small (square text box)','event_espresso')),					
								array('id'=>'2','text'=> __('Small (round text box)','event_espresso')),
								array('id'=>'3','text'=> __('Small (plain text)','event_espresso')),
								array('id'=>'5','text'=> __('Large (square text box)','event_espresso')),
								array('id'=>'4','text'=> __('Small (no text)','event_espresso')),
								array('id'=>'6','text'=> __('Large (no text)','event_espresso'))
							);				
							echo select_input('espresso_stumbleupon_button_style', $values, $espresso_stumbleupon['espresso_stumbleupon_button_style'], 'id="espresso_stumbleupon_button_style"');
							?>
							</td>
						</tr>
					</tbody>
				</table> 
				<p>
					<input type="hidden" name="update_stumbleupon" value="update" />
					<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Stumbleupon Options', 'event_espresso'); ?>" id="save_google_settings" />
				</p>
				</form>
			</div><!-- / .padding -->
		</div><!-- / .inside -->
	</div><!-- / .postbox -->
</div><!-- / .metabox-holder -->
<!--  End stumbleupon settings  -->
*/ ?>
	<?php  // yeah, no.
	/* include_once('social-media_help.php'); */ ?>

			</div><!-- / .meta-box-sortables -->
		</div><!-- / #post-body-content -->
	</div><!-- / #post-body -->
	</div><!-- / #poststuff -->
</div><!-- / #wrap -->
	<script type="text/javascript" charset="utf-8">
		//<![CDATA[
		jQuery(document).ready(function() {
			postboxes.add_postbox_toggles('espresso_social');

		});
		//]]>
	</script> 
<?php
}

/************************
* 	Facebook Button 	*
************************/
if (!function_exists('espresso_facebook_button')) {
	function espresso_facebook_button ($event_id){
		//Override this function using the Custom Files Addon (http://eventespresso.com/download/add-ons/custom-files-addon/)
		global $org_options, $espresso_facebook;
		
		//Build the URl to the page
		// this is broken in facebook, so let's create the url a different way
		//$registration_url = espresso_reg_url($event_id); //get_option('siteurl') . '/?ee='. $event_id;
		$slug = basename(get_permalink());
		$registration_url = get_option('siteurl') . '/' . $slug . '/?ee=' . $event_id; // this breaks if they aren't using pretty permalinks
	// wow, this is a pile of poo.  let's fix it.
	/* old button
		$button = '<iframe src="http://www.facebook.com/plugins/like.php?href='.$registration_url.'&amp;layout=' . $espresso_facebook['espresso_facebook_layout'] . '&amp;show_faces=' . $espresso_facebook['espresso_facebook_faces'] . '&amp;width=' . $espresso_facebook['espresso_facebook_width'] . '&amp;action=' . $espresso_facebook['espresso_facebook_action'] . '&amp;font=' . $espresso_facebook['espresso_facebook_font'] . '&amp;colorscheme=' . $espresso_facebook['espresso_facebook_colorscheme'] . '&amp;height=' . $espresso_facebook['espresso_facebook_height'] . '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:' . $espresso_facebook['espresso_facebook_width'] . 'px; height:' . $espresso_facebook['espresso_facebook_height'] . 'px;" allowTransparency="true"></iframe>';
	*/
	// new button
	if (is_ssl()) {
		$button = '<iframe src="https://www.facebook.com/plugins/like.php?href=';
	}
	else {
		$button = '<iframe src="http://www.facebook.com/plugins/like.php?href=';
	}
		$button .= $registration_url;
		$button .= '&amp;layout=' . $espresso_facebook['espresso_facebook_layout'];
		$button .= '&amp;show_faces=' . $espresso_facebook['espresso_facebook_faces'];
		$button .= '&amp;width=' . $espresso_facebook['espresso_facebook_width'];
		$button .= '&amp;action=' . $espresso_facebook['espresso_facebook_action'];
		$button .= '&amp;font=' . $espresso_facebook['espresso_facebook_font'];
		$button .= '&amp;colorscheme=' . $espresso_facebook['espresso_facebook_colorscheme'];
		$button .= '&amp;height=' . $espresso_facebook['espresso_facebook_height'];
		$button .= '" scrolling="no" frameborder="0" ';
		if ( $espresso_facebook['espresso_facebook_layout'] == 'button_count' ) {
			$button .= 'style="border:none; overflow:hidden; width: 100px;'; // setting the width to 100px to give it some room for lots and lots of likes
			$button .= 'height: 20px;"'; // setting the height to 20px because that's what it actually is
		}
		elseif ( $espresso_facebook['espresso_facebook_layout'] == 'standard') {
			$button .= 'style="border:none; overflow:hidden; width: 300px;'; // setting the width to 300px. this is the default if you leave this blank which is at least better than the 450px we were giving it before
			$button .= 'height: 59px;"'; // setting the height to 59px which is high enough for a single row of faces
		}
		elseif ( $espresso_facebook['espresso_facebook_layout'] == 'box_count' ) {
			$button .= 'style="border:none; overflow:hidden; width: 46px;'; // setting the width to 46px, the width of the bubble/like button
			$button .= 'height: 62px;"'; // setting the height to 62px, the height of the vertical box count
		}
		$button .= 'allowTransparency="true"></iframe>';
		// that wasn't so hard, was it?
		return $button;
	
	}
}

/************************
* 	Twitter Button 		*
************************/
if (!function_exists('espresso_twitter_button')) {
	//OVerride this function using the Custom Files Addon (http://eventespresso.com/download/add-ons/custom-files-addon/)
	function espresso_twitter_button ($event_id){
		global $wpdb, $org_options, $espresso_twitter;

		//Build the URl to the page
		$registration_url = espresso_reg_url($event_id); //get_option('siteurl') . '/?ee='. $event_id;
		// this is also a pile of poo (but not quite so large), so we'll fix this one, too
		/* old button
		$button = '<a href="http://twitter.com/share" class="twitter-share-button" data-url="' . $registration_url . '" data-text="' . $espresso_twitter['espresso_twitter_text'] . '" data-count="' . $espresso_twitter['espresso_twitter_count_box'] . '" data-via="' . $espresso_twitter['espresso_twitter_username'] . '" data-lang="' . $espresso_twitter['espresso_twitter_lang'] . '">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
		*/
		// new button
		if (is_ssl()) {
			$button = '<a href="https://twitter.com/share"';
		}
		else {
			$button = '<a href="http://twitter.com/share"';
		}
		$button .= 'class="twitter-share-button" data-url="' . $registration_url;
		$button .= '" data-count="' . $espresso_twitter['espresso_twitter_count_box'];
		$button .= '" data-via="' . $espresso_twitter['espresso_twitter_username'];
		$button .= '" data-lang="' . $espresso_twitter['espresso_twitter_lang'];
		if (is_ssl()) {
			$button .= '">Tweet</a><script type="text/javascript" src="https://platform.twitter.com/widgets.js"></script>';
		}
		else {
			$button .= '">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
		}
		// all done!
		return $button;
	
	}
}

//Twitter button short code

//Example usage:
//[ESPRESSO_TWITTER text="Test Event" user_name="EventEspresso" count_box="vertical"]
//[ESPRESSO_TWITTER]

//Shortcode parameters:
// text - Default Tweet text
// count_box - Count box position: none (default) | horizontal | vertical
// user_name - Screen name of the user to attribute the Tweet to
// url - URL of the page to share
// lang - The language for the Tweet Button. Set it to the two letter ISO-639-1 language code (http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes). Default is English (en)

//For more information, check out http://dev.twitter.com/pages/tweet_button_faq
/*if (!function_exists('espresso_twitter_button_shortcode')) { 
	//Override this function using the Custom Files Addon (http://eventespresso.com/download/add-ons/custom-files-addon/)
	function espresso_twitter_button_shortcode ($atts){
		//global $wpdb, $org_options;
		
		//Get the shortcode parameters
		extract(shortcode_atts(array('text' => __('Register for','event_espresso'), 'count_box' => 'none', 'lang' => 'en', 'user_name' => '', 'url' => ''), $atts));
		
		$text = "{$text}"; //Default Tweet text
		$count_box = "{$count_box}"; //Count box position
		$user_name = "{$user_name}"; //Screen name of the user to attribute the Tweet to
		$url = "{$url}"; //URL of the page to share
		$lang = "{$lang}"; //The language for the Tweet Button
		
		//Build the URL if none is provided
		$url != "" ? 'data-url="' . $url . '"' : '';
		
		$button = '<a href="http://twitter.com/share" class="twitter-share-button" ' . $url . ' data-text="' . $text . '" data-count="' . $count_box . '" data-via="' . $user_name . '" data-lang="' . $lang . '">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
		
		//return "{$user_name}";
		return $button;
	
	}
}
add_shortcode('ESPRESSO_TWITTER', 'espresso_twitter_button_shortcode');
*/
/******************
* Google+1 button *
*******************/
if (!function_exists('espresso_google_button')) {
	// Override this function using the Custom Files Addon (http://eventespresso.com/download/add-ons/custom-files-addon/)
	function espresso_google_button ($event_id){
		global $wpdb, $org_options, $espresso_google;

		$registration_url = espresso_reg_url($event_id); //get_option('siteurl') . '/?ee='. $event_id;
		$g_button = '<div class="g-plusone" href="' . $registration_url . '" data-href="' . $registration_url . '" data-size="' . $espresso_google['espresso_google_button_size'] . '"></div>';
		$g_button .= '<script type="text/javascript">
  		(function() {
    		var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true; ';
    	if ( is_ssl() ) {
    		$g_button .= 'po.src = \'https://apis.google.com/js/plusone.js\';';
    	}
    	else {
    		$g_button .= 'po.src = \'http://apis.google.com/js/plusone.js\';'; // only load https address if we're using ssl on the page
    	}
    	$g_button .= 'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
  		})();
			</script>';
		
			return $g_button; ;

		
	}
}
/****************
* Stumbleupon *
*****************/	
/* just say no to stumbleupon - cb #626
if (!function_exists('espresso_stumbleupon_button')) {
	// Override this function using the Custom Files Addon (http://eventespresso.com/download/add-ons/custom-files-addon/)
	function espresso_stumbleupon_button ($event_id){
		global $wpdb, $org_options, $espresso_stumbleupon;
		
		$registration_url = espresso_reg_url($event_id);
		$size = $espresso_stumbleupon['espresso_stumbleupon_button_style'];
		
		$button = '<script src="http://www.stumbleupon.com/hostedbadge.php?s=' . $size . '&r=' . $registration_url . '"></script>';
		
		return $button;
	}
}
*/
//Social media buttons
if (!function_exists('espresso_social_media_buttons')) {

	function espresso_social_media_buttons($event_id, $type = '') {
		switch ($type) {
			case 'twitter':
				if (function_exists('espresso_twitter_button')) {
					return espresso_twitter_button($event_id);
				}
				break;
			case 'facebook':
				if (function_exists('espresso_facebook_button')) {
					return espresso_facebook_button($event_id);
				}
				break;
			case 'google':
				if (function_exists('espresso_google_button')) {
					return espresso_google_button($event_id);
				}
				break;
			/* just say no to stumbleupon - cb #626
			case 'stumbleupon':
				if (function_exists('espresso_stumbleupon_button')) {
					return espresso_stumbleupon_button($event_id);
				}
				break;
			*/
			default:
				break;
		}
	}

}

function espresso_social_display_buttons ($event_id){
	/*
		fetching the options here so I can output the alignment of each button and apply some conditional styling based on the orientation of the button
	*/
	$espresso_social_twitter = get_option('espresso_twitter_settings');
	$espresso_social_google = get_option('espresso_google_settings');
	$espresso_social_facebook = get_option('espresso_facebook_settings');
	//echo $event_id;
	echo '<div class="ee-social-media-buttons">';print_r($sql);
	if( espresso_social_media_buttons($event_id, 'twitter')) { echo  '<span class="twitter-button ee-social-media-button ' . $espresso_social_twitter['espresso_twitter_count_box'] . '">'.espresso_social_media_buttons($event_id, 'twitter').'</span>'; }
	/* just say no to stumbleupon - cb #626
	if( espresso_social_media_buttons($event_id, 'stumbleupon')) { echo '<span class="stumbleupon-button ee-social-media-button">'.espresso_social_media_buttons($event_id, 'stumbleupon').'</span>'; }
	*/
	if( espresso_social_media_buttons($event_id, 'google')) { echo '<div class="google-button ee-social-media-button ' . $espresso_social_google['espresso_google_button_size'] . '">'.espresso_social_media_buttons($event_id, 'google').'</div>'; }
	if( espresso_social_media_buttons($event_id, 'facebook')) { echo '<span class="facebook-button ee-social-media-button ' . $espresso_social_facebook['espresso_facebook_layout'] . '">'.espresso_social_media_buttons($event_id, 'facebook').'</span>'; } // moving facebook to the last position to, hopefully, fix cb #587
	echo '</div>';
	echo '<div style="clear: both;"></div>';
	return;
}
add_action( 'action_hook_espresso_social_display_buttons', 'espresso_social_display_buttons', 10, 1 );