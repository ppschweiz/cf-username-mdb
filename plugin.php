<?php
/**
 * Plugin Name: Caldera Forms username field
 * Plugin URI:  
 * Description: Username field for Caldera Forms
 * Version:     0.0.1
 * Author:      Stefan Thoeni
 * Author URI:  https://stefanthoeni.ch
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */


// Add field type config to caldera forms fields
// use a function name that is unique of if whithin a class, array($this, 'method_to_use')
add_filter('caldera_forms_get_field_types', 'cf_username_mdb_field_register_function');

/**
 * field type register function to add new field to registered fields array
 *
 * @param array $fields all registered field types with key as field type slug
 * @return array $fields
 */
function cf_username_mdb_field_register_function($fields){

	//be sure to give you field a unique slug. you are also able to redefine exisitng field by simply redefining it.
	// the only REQUIRED values are name, file, category, description
	$fields['username_mdb'] = array(
		"field"				=>	__("Username MDB", 'cf_username_mdb'),
		"file"				=>	plugin_dir_path( __FILE__ ) . 'field.php',
		"category"			=>	"Basic", 					// comma separated list of categories to place the field in
		"description" 		=>	__('Username field with MDB validation','cf_username_mdb'), 	// description explains what the field is for
		"viewer"			=>	'cf_username_mdb_viewer_function', // array($this, 'viewer_function')		// viewer function is used to processes the stored value for display purposes. i.e if saving a saving a post ID the viewer whould get the post title to display
		"handler"			=>	'cf_username_mdb_handler_function', // array($this, 'handler_function')		// handler function is used to processes the submitted value before storage. Like a file uploader to store the saved URL
		"setup"				=>	array(															// Setup array are config options used within the form editor
			"template"		=>	plugin_dir_path( __FILE__ ) . 'config.php',						// template is the config tempalte. the file loaded to capture field config options
			"preview"		=>	plugin_dir_path( __FILE__ ) . 'preview.php',					// the preview file is the file used for the preview of the field in the form editor
			"not_supported"	=>	array(															// the not_supported setting defines which base config options are not supported by this field
				'hide_label',	// adding hide_label removes the option to hide the lable/ used if a lable is not part of the field
				'caption',		// adding caption, removes the input to set the field desctiption
				'entry_list',	// adding entry_list removes capturing of this field value. used for buttons, spam check etc..
			),					
			"default"		=>	array(															// the default array are the default config options when inserting a new field
				'field_option'	=>	'field option value'										// config options are stored as option => value
			),
			"scripts" => array(																	// the scripts array are any javascript libraries that the field needs within the form edito
				"jquery"																		// can be a handle to a registered script or a url to the file.
			),
			"styles" => array(																	// the styles array are stlye sheets that the field needs within the form editor
																								// can be a handle to a regestered style or a url to the file
			)
		),
		"scripts" => array(																		// scripts array outside of setup are scripts that are used in the frontend form
																								// can be a handle to a regstered script or a url
		),
		"styles" => array(																		// styles array outside of setup are style sheets that are used in the frontend form
																								// can be a handle to a regstered style or a url
		)
	);

	return $fields; // be sure to return the full fields array.

}

/**
 * field type viewer function to filter the stored value into a human readable format.
 *
 * @param string|array $value the stored value of the captured entry
 * @param array $field the full field config array associated with the entry
 * @param array $form the full form config structure 
 * @return string $value the filtered version of $value
 */
function cf_username_mdb_viewer_function($value, $field ,$form){
	// do stuff to the value. like add an image url to an <img> tag etc..
	return $value;
}


/**
 * field type handler function to handle the submitted value to be stored
 *
 * @param string|array raw submitted $value to be processed for storage
 * @param array $field the full field config array associated with the entry
 * @param array $form the full form config structure 
 * @return string|array $value the filtered version of $value to be stored
 */
function cf_username_mdb_handler_function($value, $field ,$form){
	// do stuff to the value. like save a file upload and return the stored URL
	// arrays can be returned but a viewer function will be required to convert the array to a viewable string.

	// return a WP_Error to return and trigger an erro. the error will shown to the user
	//return new WP_Error( 'error', 'Nope, Sorry. Try again.');

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $field['config']['apiurl']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, array('username' => $value, 'apikey' => $field['config']['apikey']));

	$response = curl_exec($ch);

	curl_close($ch);

	if($response != '1')
	{
		return new WP_Error( 'error', 'Dieser Benutzername wird schon verwendet.');
	}
	else
	{
		return $value;
	}
}

