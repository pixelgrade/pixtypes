<?php
interface PixTypesFormManagerInterface
{
    /**
     * Set field value
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value);

    /**
     * Get field value
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null);
}