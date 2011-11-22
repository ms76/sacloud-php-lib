<?php
/**
 *  Sacloud.php
 *
 *  @package    Sacloud
 *  @author     Masashi Sekine <sekine@cloudrop.jp>
 *  @license    Apache License 2.0
 */

// Look for include file in the same directory (e.g. `./config.inc.php`).
if (!class_exists('Sacloud')) {
    die('No direct access allowed.');
}
if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php')) {
    include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.inc.php';
}

/**
 * Sacloud
 *
 *  @package    Sacloud
 *  @author     Masashi Sekine <sekine@cloudrop.jp>
 *  @license    Apache License 2.0
 */
class Sacloud
{
    const END_POINT = 'https://secure.sakura.ad.jp/cloud/api/cloud/0.2';
    protected $key;
    protected $secretKey;
    protected $isDebug = false;

    public function __construct($key = null, $secretKey = null)
    {
        if ($key && $secretKey) {
            $this->key = $secretKey;
            $this->secretKey = $secretKey;
            return;

        } elseif (defined('SACLOUD_KEY') && defined('SACLOUD_SECRET_KEY')) {
            $this->key = SACLOUD_KEY;
            $this->secretKey = SACLOUD_SECRET_KEY;
            return;

        } else {
            throw new SacloudException('No valid credentials were used to authenticate with SAKURA Internet API.');
        }
    }

    public function api($path, $method = 'GET', $params = null, $format = 'json')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key . ':' . $this->secretKey);
        curl_setopt($ch, CURLOPT_URL, self::END_POINT . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $params = (is_array($params)) ? http_build_query($params) : $params;

        switch ($method) {
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($params !== null) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                }
                break;

            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;

            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($params !== null) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                }
                break;
        }

        if ($this->isDebug === true) {
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
        }

        $response = curl_exec($ch);

        if ($this->isDebug === true) {
            $requestHeader = curl_getinfo($ch, CURLINFO_HEADER_OUT);
            $responseHeader = explode("\r\n\r\n", $response);
            $body = array_pop($responseHeader);
            return array('requestHeader' => $requestHeader, 'responseHeader' => $responseHeader, 'body' => $body);
        }

        switch ($format) {
            default:
            case 'json':
                $result = json_decode($response, true);
                break;

            case 'raw':
                $result = $response;
                break;
        }

        return $result;
    }

    /**
     * Create server instance
     *
     * @param integer $serverId
     * @return SacloudServer $server
     */
    public function server($serverId)
    {
        $server = new SacloudServer($this, $serverId);
        return $server;
    }

    /**
     * Alias to server()
     *
     * @see Sacloud::server()
     */
    public function setServer($serverId)
    {
        return $this->server($serverId);
    }

    /**
     * Set debug-mode flag
     *
     * @param boolean $debug
     */
    public function debugMode($debug)
    {
        if (is_bool($debug)) {
            $this->isDebug = $debug;
        }

        return $this;
    }
}

/**
 * SacloudServer
 *
 *  @package    Sacloud
 *  @author     Masashi Sekine <sekine@cloudrop.jp>
 *  @license    Apache License 2.0
 */
class SacloudServer
{
    protected $serverId;
    protected $sacloud;

    public function __construct(Sacloud $sacloud, $serverId)
    {
        $this->sacloud = $sacloud;
        $this->serverId = $serverId;
    }

    public function getStatus()
    {
        $path = '/server/' . $this->serverId;
        return $this->sacloud
                    ->api($path, 'GET');
    }

    public function getInstanceStatus()
    {
        $path = '/server/' . $this->serverId . '/power';
        return $this->sacloud
                    ->api($path, 'GET');
    }

    public function powerOn()
    {
        $path = '/server/' . $this->serverId . '/power';
        return $this->sacloud
                    ->api($path, 'PUT');
    }

    public function powerOff()
    {
        $path = '/server/' . $this->serverId . '/power';
        return $this->sacloud
                    ->api($path, 'DELETE');
    }

    public function getScreenShot($format = 'png')
    {
        $path = '/server/' . $this->serverId . '/vnc/snapshot.png';
        return $this->sacloud
                    ->api($path, 'GET', null, 'raw');
    }

    public function sendCtrlAltDelete()
    {
        $path = '/server/' . $this->serverId . '/keyboard';
        return $this->sacloud
                    ->api($path, 'PUT', '{"Keys": ["ctrl","alt","delete"]}');
    }

    public function getMonitor()
    {
        $path = '/server/' . $this->serverId . '/monitor';
        return $this->sacloud
                    ->api($path, 'GET');
    }

    public function reset()
    {
        $path = '/server/' . $this->serverId . '/reset';
        return $this->sacloud
                    ->api($path, 'PUT');
    }
}

/**
 * SacloudException
 *
 *  @package    Sacloud
 *  @author     Masashi Sekine <sekine@cloudrop.jp>
 *  @license    Apache License 2.0
 */
class SacloudException extends Exception
{
}
