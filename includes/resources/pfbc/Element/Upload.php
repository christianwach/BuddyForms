<?php

/**
 * Class Element_Upload
 */
class Element_Upload extends Element_Textbox {
	/**
	 * @var int
	 */
	public $bootstrapVersion = 3;
	/**
	 * @var array
	 */
	protected $_attributes = array( "type" => "file", "file_limit" => "", "accepted_files" => "", "multiple_files" => "", "delete_files" => "" );
	
	public function render() {
		global $buddyforms;
		ob_start();
		parent::render();
		$box = ob_get_contents();
		ob_end_clean();
		$id         = $this->getAttribute( 'id' );
		$action     = isset( $_GET['action'] ) ? $_GET['action'] : "";
		$entry      = isset( $_GET['entry'] ) ? $_GET['entry'] : "";
		$column_val = "";
		$result     = "";
		if ( ! empty( $entry ) && $action == 'edit' ) {
			$column_val = get_post_meta( $entry, $id, true );
			
			$attachmet_id = explode( ",", $column_val );
			foreach ( $attachmet_id as $id ) {
				$url    = wp_get_attachment_url( $id );
				$result .= " <a style='vertical-align: top;' target='_blank' href='" . $url . "'>$id</a>,";
			}
		}
		$message = "Drop files here to upload";
		if ( ! empty( $result ) ) {
			$message = rtrim( trim( $result ), ',' );
		}
		
		$max_size         = $this->getAttribute( 'file_limit' );
		$accepted_files   = $this->getAttribute( 'accepted_files' );
		$multiple_files   = $this->getAttribute( 'multiple_files' );
		$mime_type        = '';
		$mime_type_result = '';
		$allowed_types    = get_allowed_mime_types();
		if( isset( $accepted_files ) && is_array( $accepted_files ) ) {
			foreach ( $accepted_files as $key => $value ) {
				$mime_type .= $allowed_types[ $value ] . ',';
			}
		}
		if ( ! empty( $mime_type ) ) {
			$mime_type_result = rtrim( trim( $mime_type ), ',' );
		}
		
		$box = str_replace( "class=\"form-control\"", "class=\"dropzone\"", $box );
		$box = "<div class=\"dropzone dz-clickable\" id=\"$id\" file_limit='$max_size' accepted_files='$mime_type_result' multiple_files='$multiple_files' action='$action'>
                                 <div class=\"dz-default dz-message\" data-dz-message=\"\">
                                      <span>$message</span>
                                 </div>
                                 <input type='hidden' name='$id' value='' id='field_$id'/>
                </div>";
		if ( $this->bootstrapVersion == 3 ) {
			echo $box;
		} else {
			echo preg_replace( "/(.*)(<input .*\/>)(.*)/i",
				'${1}<label class="file">${2}<span class="file-custom"></span></label>${3}', $box );
		}
	}
	
}