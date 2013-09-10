<?php
abstract class PixTypesFormBuilder extends PixTypesFormField
{
    public function __construct($name)
    {
        parent::__construct($name, 'main');
        $this->configureForm($this);
    }

    abstract public function configureForm(PixTypesFormField $form);

    public function configureFromArray(array $elements)
    {
        foreach ($elements as $element) {
            if (!isset($element['name'], $element['type'])) {
                continue;
            }
            $formElement = new PixTypesFormField($element['name'], $element['type']);
            if (isset($element['attributes']) && is_array($element['attributes'])) {
                $formElement->setAttributes($element['attributes']);
            }
        }
    }
}