<?php
class AdminPixTypesFormRenderer implements PixTypesFormRendererInterface
{
    /**
     * @var PixTypesFormManagerInterface
     */
    private $manager;

    private $prefix;

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    function render(PixTypesFormField $form)
    {
        switch ($form->getType()) {
            case 'main':
                echo "<div class=\"wrap\">";
                    screen_icon();
                    echo '<h2>'. esc_html( get_admin_page_title() ) .'</h2>';
                    echo '<form id="PixTypes_form" method="POST" action="'. esc_url( add_query_arg( array( 'page' => 'PixTypes' ), admin_url( 'options-general.php' ) ) ) .'">';
                        array_map(array($this, 'render'), $form->getChildren());
	                    echo '<button type="submit" name="submitted" value="submitted">Save</button>';
                    echo "</form>";
                echo '</div>';
                break;
            case 'group':
                echo "<fieldset class=\"group\">";
	            echo "<h4>". $form->getAttribute('label') ."</h4>";
                array_map(array($this, 'render'), $form->getChildren());
                echo "</fieldset>";
                break;
	        case 'extend':
		        echo "<fieldset class=\"extend\">";
		        echo "<h4>". $form->getAttribute('label') ."</h4>";
			    echo "<div class='main_field'>";
//		        array_map(array($this, 'render'), $form->getMainField());
		        echo "</div>";
		        array_map(array($this, 'render'), $form->getChildren());
		        echo "</fieldset>";
		        break;
            case 'text':
                echo $this->getInput($form);
                break;
            case 'checkbox':
                echo $this->getCheckbox($form);
                break;
        }
    }

    function setManager(PixTypesFormManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    function getName(PixTypesFormField $form)
    {
        if ($this->prefix != "") {
            return sprintf("%s[%s]", $this->prefix, $form->getName());
        } else {
            return $form->getName();
        }
    }

    function getInput(PixTypesFormField $form, $type = 'text')
    {
	    $value = $this->manager->get($form->getName(), @$form->getAttributes()['default']);
	    return '<fieldset class="field field-'. $type .'">'
            .'<label>' . $form->getAttribute('label') . '</label>'
		    .'<input type="' . $type . '" name="' . $this->getName($form) . '" value="' . $value . '" />'
	        .'</fieldset>';
    }

	function getCheckbox (PixTypesFormField $form)
	{
		$value = $this->manager->get($form->getName(), @$form->getAttributes()['default']);
		$checked = '';
		if ( $value === "on" ) {
			$checked = 'checked="checked"';
		}

		/**
		 * Keep the value in the hidden field, on checkbox the value won't submit if the field is unchecked
		 */

		return '<fieldset class="field field-checkbox">'.
			'<label>' . $form->getAttribute('label') . '</label>'.
			'<input type="checkbox" ' . $checked . ' />'.
			'<input type="hidden" name="' . $this->getName($form) . '" value="' . $value . '" />'.
			'</fieldset>';
	}

}