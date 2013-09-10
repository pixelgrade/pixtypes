<?php

/**
 * Class FormType
 */
class PixTypesFormField
{
    /**
     * @var
     */
    protected $name;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var array
     */
    protected $attributes = array();
	/**
	 * @var PixTypesFormField[]
	 */
	protected $main_field = array();
    /**
     * @var PixTypesFormField[]
     */
    protected $children = array();

    function __construct($name = "", $type = "")
    {
        $this->setName($name);
        $this->setType($type);
    }


    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

	/**
	 * @param string $key
	 * @return mixed value
	 */

	public function getAttribute($key)
	{
		return $this->attributes[$key];
	}

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param \PixTypesFormField[] $children
     */
    public function setChildren(array $children)
    {
        $this->children = $children;
    }

    /**
     * @param PixTypesFormField $children
     */
    public function addChildren(PixTypesFormField $children)
    {
        $this->children[] = $children;
    }

    /**
     * @return \PixTypesFormField[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function fromArray(array $elements)
    {
        foreach ($elements as $element) {
            if (!isset($element['name'], $element['type'])) {
                continue;
            }
            $formElement = new PixTypesFormField($element['name'], $element['type']);
            if (isset($element['attributes']) && is_array($element['attributes'])) {
                $formElement->setAttributes($element['attributes']);
            }

	        if (isset($element['main_field'])) {
		        echo 'this is main';
	        }

            if (isset($element['children'])) {
                $formElement->fromArray($element['children']);
            }
            $this->addChildren($formElement);
        }
    }
}