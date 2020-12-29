<?php


namespace Grafikart;


class Cookie implements SessionInterface{

    /**
     * Get a value from storage
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return isset($_COOKIE[$key]) ? unserialize($_COOKIE[$key]) : null;
    }

    /**
     * set a value into the Storage
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set($key, $value)
    {
        setcookie($key, serialize($value));
    }

    /**
     * Delete a value from storage
     * @param string $key
     * @return mixed
     */
    public function delete($key)
    {
        if(isset($_COOKIE[$key])){
            unset($_COOKIE[$key]);
            setcookie($key, '', time() - 3600);
        }
    }
}