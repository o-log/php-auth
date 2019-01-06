<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Auth;

class ExtraCookie
{
    protected $cookie_name = '';
    protected $cookie_value = '';
    protected $secure = false;
    protected $http_only = true;

    /**
     * ExtraCookie constructor.
     * @param string $cookie_name
     * @param string $cookie_value
     * @param bool $secure
     * @param bool $http_only
     */
    public function __construct($cookie_name, $cookie_value, $secure, $http_only)
    {
        $this->setCookieName($cookie_name);
        $this->setCookieValue($cookie_value);
        $this->setSecure($secure);
        $this->setHttpOnly($http_only);
    }

    /**
     * @return string
     */
    public function getCookieValue()
    {
        return $this->cookie_value;
    }

    /**
     * @param string $cookie_value
     */
    public function setCookieValue($cookie_value)
    {
        $this->cookie_value = $cookie_value;
    }

    /**
     * @return string
     */
    public function getCookieName()
    {
        return $this->cookie_name;
    }

    /**
     * @param string $cookie_name
     */
    public function setCookieName($cookie_name)
    {
        $this->cookie_name = $cookie_name;
    }

    /**
     * @return boolean
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * @param boolean $secure
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;
    }

    /**
     * @return boolean
     */
    public function isHttpOnly()
    {
        return $this->http_only;
    }

    /**
     * @param boolean $http_only
     */
    public function setHttpOnly($http_only)
    {
        $this->http_only = $http_only;
    }
}
