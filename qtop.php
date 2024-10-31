<?php
/*
Plugin Name: qTop [DEPRECATED]
Plugin URI: http://konrad-haenel.de/downloads/qtop-wordpress-widget/
Description: Please install the new version, the one not labeled [DEPRECATED]
Author: Konrad Haenel
Version: 0.0.5
Author URI: http://konrad-haenel.de/en

    This widget is released under the GNU General Public License (GPL)
    http://www.gnu.org/licenses/gpl.txt

    This is a WordPress plugin (http://wordpress.org) and widget
*/

// We're putting the plugin's functions in one big function we then
// call at 'plugins_loaded' (add_action() at bottom) to ensure the
// required Sidebar Widget functions are available.
function widget_qtop_init() {

    // Check to see required Widget API functions are defined...
    if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
        return; // ...and if not, exit gracefully from the script.

    // This function prints the sidebar widget--the cool stuff!
    function widget_qtop($args) {

        // $args is an array of strings which help your widget
        // conform to the active theme: before_widget, before_title,
        // after_widget, and after_title are the array keys.
        extract($args);

        // Collect our widget's options, or define their defaults.
        $options = get_option('widget_qtop');
		$title = empty($options['title']) ? 'TOP 5' : $options['title'];
		$maxentries = empty($options['maxentries']) ? 5 : $options['maxentries'];

         // It's important to use the $before_widget, $before_title,
         // $after_title and $after_widget variables in your output.
		echo $before_widget;
		
		// this widget only works with ak popularity contest and qTranslate
		if (function_exists('akpc_get_popular_posts_array') && function_exists('_e')) { 
			// get array of popular posts and pages
			global $akpc;
			$popposts =  $akpc->get_top_ranked_posts($maxentries);
		
		?>
			<h2 class="widgettitle"><?php _e($title); ?></h2>
				 <ul>
				 <?php 
					if ($popposts) {
						foreach ($popposts as $ppost) { 
							?><li><a href="<?php echo get_permalink($ppost->ID); ?>"><?php _e($ppost->post_title); ?></a></li><?php
						} 
					}
					?>
				</ul>
			<?php 
		}		
		echo $after_widget;
    }

    // This is the function that outputs the form to let users edit
    // the widget's title and so on. It's an optional feature, but
    // we'll use it because we can!
    function widget_qtop_control() {

        // Collect our widget's options.
        $options = get_option('widget_qtop');

        // This is for handing the control form submission.
        if ( $_POST['qtop-submit'] ) {
            // Clean up control form submission options
            $newoptions['title'] = strip_tags(stripslashes($_POST['qtop-title']));
			$newoptions['maxentries'] = strip_tags(stripslashes($_POST['qtop-maxentries']));
			
			// If original widget options do not match control form
	        // submission options, update them.
	        if ( $options != $newoptions ) {
	            $options = $newoptions;
	            update_option('widget_qtop', $options);
	        }
        }

        // Format string options as valid HTML. Hey, why not.
        $title = htmlspecialchars($options['title'], ENT_QUOTES);
		$maxentries = htmlspecialchars($options['maxentries'], ENT_QUOTES);

// The HTML below is the control form for editing options.
?>
        <div>
	        <label for="qtop-title" style="line-height:35px;display:block;">Title:</label> <input type="text" id="qtop-title" name="qtop-title" value="<?php echo $title; ?>" />
			<label for="qtop-maxentries" style="line-height:35px;display:block;">Max Entries:</label> <input type="text" id="qtop-maxentries" name="qtop-maxentries" value="<?php echo $maxentries; ?>" />
			<hr />
	        <input type="hidden" name="qtop-submit" id="qtop-submit" value="1" />
        </div>
    <?php
    // end of widget_qtop_control()
    }

    // This registers the widget. About time.
    register_sidebar_widget('qTop', 'widget_qtop');

    // This registers the (optional!) widget control form.
    register_widget_control('qTop', 'widget_qtop_control');
}

// Delays plugin execution until Dynamic Sidebar has loaded first.
add_action('plugins_loaded', 'widget_qtop_init');
?>