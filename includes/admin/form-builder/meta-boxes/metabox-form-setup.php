<?php

function buddyforms_metabox_form_setup() {
	global $post;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	// Get the BuddyForms Options
	$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );

	// Get all post types
	$post_types = get_post_types( array( 'show_ui' => true ), 'names', 'and' );

	// Generate the Post Type Array 'none' == Contact Form
	$post_types['bf_submissions'] = 'none';

	$post_types = buddyforms_sort_array_by_Array($post_types, array('bf_submissions'));

	// Remove the 'buddyforms' post type from the post type array
	unset( $post_types['buddyforms'] );


	// Get all Pages
	$pages = get_pages( array(
		'sort_order'  => 'asc',
		'sort_column' => 'post_title',
		'parent'      => 0,
		'post_type'   => 'page',
		'post_status' => 'publish'
	) );

	// Generate teh Pages Array
	$all_pages = Array();
	$all_pages['none'] = 'Select a Page to enable user post management';
	foreach ( $pages as $page ) {
		$all_pages[ $page->ID ] = $page->post_title;
	}


	// Get all values or set the default
	$slug                       = $post->post_name;
	$singular_name              = isset( $buddyform['singular_name'] )              ? stripslashes( $buddyform['singular_name'] )   : '';
	$after_submit               = isset( $buddyform['after_submit'] )               ? $buddyform['after_submit']                    : 'display_message';
	$after_submission_page      = isset( $buddyform['after_submission_page'] )      ? $buddyform['after_submission_page']           : 'false';
	$after_submission_url       = isset( $buddyform['after_submission_url'] )       ? $buddyform['after_submission_url']            : '';
	$post_type                  = isset( $buddyform['post_type'] )                  ? $buddyform['post_type']                       : 'false';

	$form_type                  = isset( $buddyform['form_type'] )                  ? $buddyform['form_type']                       : 'contact';

	$message_text_default       = $post_type == 'false' ? 'Your Message has been Submitted Successfully' : 'The [form_singular_name] [post_title] has been successfully Submitted!<br>1. [post_link]<br>2. [edit_link]';
	$after_submit_message_text  = isset( $buddyform['after_submit_message_text'] )  ? $buddyform['after_submit_message_text']       : $message_text_default;

	$attached_page              = isset( $buddyform['attached_page'] )              ? $buddyform['attached_page']                   : 'false';
	$status                     = isset( $buddyform['status'] )                     ? $buddyform['status']                          : 'false';
	$comment_status             = isset( $buddyform['comment_status'] )             ? $buddyform['comment_status']                  : 'false';
	$revision                   = isset( $buddyform['revision'] )                   ? $buddyform['revision']                        : 'false';
	$admin_bar                  = isset( $buddyform['admin_bar'] )                  ? $buddyform['admin_bar']                       : 'false';
	$edit_link                  = isset( $buddyform['edit_link'] )                  ? $buddyform['edit_link']                       : 'all';
	$bf_ajax                    = isset( $buddyform['bf_ajax'] )                    ? $buddyform['bf_ajax']                         : 'false';
	$list_posts_option          = isset( $buddyform['list_posts_option'] )          ? $buddyform['list_posts_option']               : 'list_all_form';
	$list_posts_style           = isset( $buddyform['list_posts_style'] )           ? $buddyform['list_posts_style']                : 'list';


	$local_storage              = isset( $buddyform['local_storage'] )              ? $buddyform['local_storage']                   : '';



	// Create The Form Array
	$form_setup     = array();

	//
	// Submission
	//
	$element = new Element_Select( '<b>' . __( "After Submission", 'buddyforms' ) . '</b>', "buddyforms_options[after_submit]", array(
		'display_message'    => __('Display Message', 'buddyforms'),
		'display_form'       => __('Display the Form and Message'),
		'display_page'       => __('Display Page Contents', 'buddyforms'),
		'display_post'       => __('Display the Post'),
		'display_posts_list' => __('Display the User\'s Post List'),
		'redirect'           => __('Redirect to url', 'buddyforms'),

	), array(
		'value' => $after_submit,
		'class' => 'bf-after-submission-action',
		'id'    => 'bf-after-submission-action'
	) );
	$element->setAttribute( 'data-hidden', 'display_page display_form display_message redirect');
	$form_setup['Form Submission'][] = $element;


	// Attached Page
	$element = new Element_Select( '<b>' . __( "After Submission Page", 'buddyforms' ) . '</b>', "buddyforms_options[after_submission_page]", $all_pages, array(
		'value'     => $after_submission_page,
		'shortDesc' => __('Select the Page from where the content gets displayed. Will redirected to the page if ajax is disabled, otherwise display the content.', 'buddyforms'),
		'class'     => 'display_page',
	) );
	$form_setup['Form Submission'][] = $element;

	$form_setup['Form Submission'][] = new Element_Url( '<b>' . __( "Redirect URL", 'buddyforms' ), "buddyforms_options[after_submission_url]", array(
		'value'     => $after_submission_url,
		'shortDesc' => __('Enter a valid URL', 'buddyforms'),
		'class'     => 'redirect'
	) );

	$form_setup['Form Submission'][]              = new Element_Textarea( '<b>' . __( 'After Submission Message Text', 'buddyforms' ) . '</b>', "buddyforms_options[after_submit_message_text]", array(
		'rows'      => 3,
		'style'     => "width:100%",
		'class'     => 'display_message display_form',
		'value'     => $after_submit_message_text,
		'shortDesc' => $post_type == 'false'
			? __('Add a after Submission Message', 'buddyforms')
			: __( ' You can use special shortcodes to add dynamic content:<br>[form_singular_name] = Singular Name<br>[post_title] = The Post Title<br>[post_link] = The Post Permalink<br>[edit_link] = Link to the Post Edit Form', 'buddyforms' )
	) );

	$form_setup['Form Submission'][] = new Element_Checkbox( '<b>' . __( 'AJAX', 'buddyforms' ) . '</b>', "buddyforms_options[bf_ajax]", array( 'bf_ajax' => __( 'Disable ajax form submission', 'buddyforms' ) ), array(
		'shortDesc' => __( '', 'buddyforms' ),
		'value'     => $bf_ajax
	) );

	$form_setup['Form Submission'][] = new Element_Checkbox( '<b>' . __( 'Local Storage', 'buddyforms' ) . '</b>', "buddyforms_options[local_storage]", array( 'disable' => __( 'Disable Local Storage', 'buddyforms' ) ), array(
		'shortDesc' => __( 'The form elements content is stored in the browser so it not gets lost if the tab gets closed by accident', 'buddyforms' ),
		'value'     => $local_storage
	) );
	$form_setup['Form Submission'][] = new Element_Checkbox( '<b>' . __( 'User Data', 'buddyforms' ) . '</b>', "buddyforms_options[bf_ajax]", array(
		'ipaddress' => __( 'Disable IP Address', 'buddyforms' ),
		'referer'   => __( 'Disable Referer', 'buddyforms' ),
		'browser'   => __( 'Disable Browser', 'buddyforms' ),
		'version'   => __( 'Disable Brovser Version', 'buddyforms' ),
		'platform'  => __( 'Disable Platform', 'buddyforms' ),
		'reports'   => __( 'Disable Reports', 'buddyforms' ),
		'userAgent' => __( 'Disable User Agent', 'buddyforms' ),
	), array(
		'shortDesc' => __( 'By default all above user data will be stored. In some country\'s for example in the EU you are not allowed to save the ip. Please make sure you not against the low in your country and adjust if needed', 'buddyforms' ),
		'value'     => $bf_ajax
	) );


	//
	// Create Content
	//
	$form_setup['Create Content'][] = new Element_Select( '<b>' . __( "Post Type", 'buddyforms' ) . '</b>', "buddyforms_options[post_type]", $post_types, array(
		'value'     => $post_type,
		'shortDesc' => 'Select a post type if you want to create posts from form submissions. <a target="_blank" href="#">Read the Documentation</a>',
		'id'        => 'form_post_type',
	) );

	$form_setup['Create Content'][] = new Element_Select( '<b>' . __( "Status", 'buddyforms' ) . '</b>', "buddyforms_options[status]", array(
		'publish',
		'pending',
		'draft'
	), array( 'value' => $status ) );

	$form_setup['Create Content'][] = new Element_Select( '<b>' . __( "Comment Status", 'buddyforms' ) . '</b>', "buddyforms_options[comment_status]", array(
		'open',
		'closed'
	), array( 'value' => $comment_status ) );

	$form_setup['Create Content'][] = new Element_Checkbox( '<b>' . __( 'Revision', 'buddyforms' ) . '</b>', "buddyforms_options[revision]", array( 'Revision' => __( 'Enable frontend revision control', 'buddyforms' ) ), array( 'value' => $revision ) );


	$form_setup['Create Content'][] = new Element_Textbox( '<b>' . __( "Singular Name", 'buddyforms' ), "buddyforms_options[singular_name]", array(
		'value'    => $singular_name,
		'shortDesc' => 'The Single Name is used by other plugins and Navigation ( Display Books, Add Book )'
	) );



	//
	// Edit Submissions
	//

	// Attached Page
	$form_setup['Edit Submissions'][] = new Element_Select( '<b>' . __( "Enable site members to manage there submissions", 'buddyforms' ) . '</b>', "buddyforms_options[attached_page]", $all_pages, array(
		'value'     => $attached_page,
		'shortDesc' => '<b><a href="#" id="bf_create_page_modal">Create a new Page</a></b>You can combine forms under the same page<br>The page you select will be used to create the endpoints to edit submissions. Its a powerful option. <a target="_blank" href="http://docs.buddyforms.com/article/139-select-page-in-the-formbuilder?preview=55b67302e4b0e667e2a4457e">Read the Documentation</a>',
		'id'        => 'form_page'
	) );

	$form_setup['Edit Submissions'][] = new Element_Checkbox( '<b>' . __( 'Admin Bar', 'buddyforms' ) . '</b>', "buddyforms_options[admin_bar]", array( 'Admin Bar' => __( 'Add to Admin Bar', 'buddyforms' ) ), array( 'value' => $admin_bar ) );

	$form_setup['Edit Submissions'][] = new Element_Radio( '<b>' . __( "Overwrite Frontend 'Edit Post' Link", 'buddyforms' ) . '</b>', "buddyforms_options[edit_link]", array(
		'none'          => 'None',
		'all'           => __( "All Edit Links", 'buddyforms' ),
		'my-posts-list' => __( "Only in My Posts List", 'buddyforms' )
	), array(
		'view'      => 'vertical',
		'value'     => $edit_link,
		'shortDesc' => __( 'The link to the backend will be changed to use the frontend editing.', 'buddyforms' ),
		'class'     => 'bf_field_view_if_form_type_post'
	) );

	$form_setup['Edit Submissions'][] = new Element_Radio( '<b>' . __( "List Posts Options", 'buddyforms' ) . '</b>', "buddyforms_options[list_posts_option]", array(
		'list_all_form' => 'List all Author Posts created with this Form',
		'list_all'      => 'List all Author Posts of the PostType'
	), array(
		'value' => $list_posts_option,
		'shortDesc' => '',
		'class'     => 'bf_field_view_if_form_type_post'
	) );


	$form_setup['Edit Submissions'][] = new Element_Radio( '<b>' . __( "List Style", 'buddyforms' ) . '</b>', "buddyforms_options[list_posts_style]", array(
		'list'  => 'List',
		'table' => 'Table'
	), array(
		'value' => $list_posts_style,
		'shortDesc' => 'Do you want to list post in a ul li list or as table.',
		'class'     => 'bf_field_view_if_form_type_post'
	) );


	// Check if form elements exist and sort the form elements
	if ( is_array( $form_setup ) ) {
		$form_setup = buddyforms_sort_array_by_Array( $form_setup, array( 'Form Submission', 'Create Content', 'Edit Submissions' ) );
	}

	// Display all Form Elements in a nice Tab UI and List them in a Table
	?>
	<span class="bf-form-type-wrap"> —
			<label for="bf-form-type-select">
				<select id="bf-form-type-select" name="buddyforms_options[form_type]">
					<optgroup label="Form Type">
						<option <?php selected($form_type, 'contact') ?> value="contact">Contact Form</option>
						<option <?php selected($form_type, 'registration') ?> value="registration">Registration Form</option>
						<option <?php selected($form_type, 'post') ?> value="post">Post Form</option>
					</optgroup>
				</select>
			</label>
	</span>
	<div class="tabs tabbable tabs-left">
		<ul class="nav nav-tabs nav-pills">
			<?php
			$i = 0;
			foreach ( $form_setup as $tab => $fields ) {
				$tab_slug = sanitize_title($tab); ?>
			<li class="<?php echo $i == 0 ? 'active' : '' ?><?php echo $tab_slug ?>_nav"><a
					href="#<?php echo $tab_slug; ?>"
					data-toggle="tab"><?php echo $tab; ?></a>
				</li><?php
				$i ++;
			}
			// Allow other plugins to add new sections
			do_action('buddyforms_form_setup_nav_li_last');
			?>

		</ul>
		<div class="tab-content">
			<?php
			$i = 0;
			foreach ( $form_setup as $tab => $fields ) {
				$tab_slug = sanitize_title($tab);
				?>
				<div class="tab-pane fade in <?php echo $i == 0 ? 'active' : '' ?>"
				     id="<?php echo $tab_slug; ?>">
					<div class="buddyforms_accordion_general">
						<table class="wp-list-table widefat posts striped fixed">
							<tbody>
							<?php foreach($fields as $field_key => $field ) {

								$type  = $field->getAttribute( 'type' );
								$class = $field->getAttribute( 'class' );

								?>

								<tr class="<?php echo $class ?>">
									<th scope="row">
										<label for="form_title"><?php echo $field->getLabel() ?></label>
									</th>
									<td>
										<?php echo $field->render() ?>
										<p class="description"><?php echo $field->getShortDesc() ?></p>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
				<?php
				$i ++;
			}
			// Allow other plugins to hook there content for there nav into the tab content
			do_action('buddyforms_form_setup_tab_pane_last');
			?>
		</div>
	</div>

	<?php

}