<?php
class PixTypesFormOptionsManager implements PixTypesFormManagerInterface
{

    private $options = array();
	private $options_key;
    function __construct($name)
    {
	    $this->options_key = $name;
        $this->options = get_option($this->options_key);
    }


    /**
     * Set field value
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value)
    {
        $this->options[$key] = $value;
    }

    /**
     * Get field value
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    public function save(array $values)
    {
        foreach ($values as $key => $value) {
//            if (isset($this->options[$key])) {
                $this->options[$key] = $value;
//            }
        }
	    update_option($this->options_key, $this->options);
    }
}
