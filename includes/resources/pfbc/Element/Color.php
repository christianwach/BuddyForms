<?php

/**
 * Class Element_Color
 */
class Element_Color extends Element {
	/**
	 * @var array
	 */
	protected $_attributes = array( "type" => "color" );

	public function __construct( $label, $name, $props ) {

		if(isset($props['value']['style'])){
			$props["value_style"] = $props['value']['style'];
		}

		$props["value"] = $props['value']['color'];

		parent::__construct( $label, $name, $props );
	}

	public function render() {

		$value_style = '';
		if(isset($this->_attributes["value_style"])){
			$value_style = $this->_attributes["value_style"];
		}

		$this->_attributes["name"] = $this->_attributes["name"] . '[color]';
		$this->_attributes["pattern"] = "#[a-g0-9]{6}";
		$this->_attributes["title"]   = "6-digit hexidecimal color (e.g. #000000)";
		$this->validation[]           = new Validation_RegExp( "/" . $this->_attributes["pattern"] . "/", "Error: The %element% field must contain a " . $this->_attributes["title"] );
		parent::render();

		$style = str_replace( '[color]', '[style]', $this->_attributes["name"] );

		echo '
		<p style="display: inline-block; font-size: 11px; line-height: 2.5;">
		<input ' . checked( $value_style, 'color', false ) . ' id="" type="radio" name="' . $style . '" value="color"> Color <br>
		<input ' . checked( $value_style, 'auto', false ) . ' id="" type="radio" name="' . $style . '" value="auto"> Auto <br>
		<input ' . checked( $value_style, 'transparent', false) . ' id="" type="radio" name="' . $style . '" value="transparent"> Transparent
		</p>';

	}
}
