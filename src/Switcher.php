<?php
/**
 * File containing the class {@see \Mistralys\SSLSwitcher}.
 *
 * @package SSLSwitcher
 * @see \Mistralys\SSLSwitcher
 */

declare(strict_types=1);

namespace Mistralys;

use AppUtils\Interface_Optionable;
use AppUtils\Traits_Optionable;

/**
 * Class used to enforce an SSL connection in a website,
 * by automatically redirecting to the https version of
 * any http URL.
 *
 * @package SSLSwitcher
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class SSLSwitcher implements Interface_Optionable
{
    use Traits_Optionable;

    /**
     * @var string
     */
    private $websiteURL;

    public function __construct(string $websiteURL)
    {
        $this->websiteURL = $websiteURL;
    }

    /**
     * Static shorthand for creating an instance and calling `switch()`.
     *
     * @param string $websiteURL
     */
    public static function autoSwitch(string $websiteURL) : void
    {
        (new SSLSwitcher($websiteURL))->switch();
    }

    /**
     * Checks if the script is running on localhost.
     * @return bool
     */
    public function isLocalhost() : bool
    {
        return
            stristr($_SERVER['HTTP_HOST'], '127.0')
            ||
            stristr($_SERVER['HTTP_HOST'], 'localhost');
    }

    /**
     * Checks whether the current script is running via the command line.
     * @return bool
     */
    public function isCLI() : bool
    {
        return
            php_sapi_name() === 'cli'
            ||
            defined('STDIN');
    }

    /**
     * Checks whether the current connection is already SSL enabled.
     * @return bool
     */
    public function isSSLActive() : bool
    {
        return isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on';
    }

    /**
     * Returns the URL to switch to, with any active request
     * parameters appended to land on the same page.
     *
     * @return string
     */
    public function getTargetURL() : string
    {
        if(!isset($_SERVER['REQUEST_URI']))
        {
            return $this->websiteURL;
        }

        $req = trim($_SERVER['REQUEST_URI']);

        if(empty($req) || $req === '/')
        {
            return $this->websiteURL;
        }

        return $this->websiteURL .= '/'.$_SERVER['REQUEST_URI'];
    }

    /**
     * Checks whether a redirect to http is required.
     * @return bool
     */
    public function isSwitchRequired() : bool
    {
        return !$this->isSSLActive() && !$this->isLocalhost() && !$this->isCLI();
    }

    /**
     * Execute the switch to https as needed.
     *
     * NOTE: does not call `exit()`. This is
     * up to the script to handle.
     */
    public function switch() : void
    {
        if(!$this->isSwitchRequired()) {
            return;
        }

        header("Location: " . $this->getTargetURL());

        if($this->getBoolOption('exit')) {
            exit;
        }
    }

    /**
     * @param bool $enabled
     * @return SSLSwitcher
     */
    public function setExitEnabled(bool $enabled) : SSLSwitcher
    {
        $this->setOption('exit', $enabled);
        return $this;
    }

    public function getDefaultOptions(): array
    {
        return array(
            'exit' => true
        );
    }
}
