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

require_once __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'server.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'archive.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'facility.php';

/**
 * Sacloud
 *
 *  @package    Sacloud
 *  @author     Masashi Sekine <sekine@cloudrop.jp>
 *  @license    Apache License 2.0
 */
class Sacloud
{
    const IS1A = 'is1a';
    const IS1B = 'is1b';
    const END_POINT_IS1A = 'https://secure.sakura.ad.jp/cloud/zone/is1a/api/cloud/1.1/';
    const END_POINT_IS1B = 'https://secure.sakura.ad.jp/cloud/zone/is1b/api/cloud/1.1/';
    protected $key;
    protected $secretKey;
    protected $isDebug = false;
    protected $endPoint;

    public function __construct($key = null, $secretKey = null, $endPoint = self::END_POINT_IS1A)
    {
        if ($key && $secretKey) {
            $this->key = $secretKey;
            $this->secretKey = $secretKey;

        } elseif (defined('SACLOUD_KEY') && defined('SACLOUD_SECRET_KEY')) {
            $this->key = SACLOUD_KEY;
            $this->secretKey = SACLOUD_SECRET_KEY;

        } else {
            throw new SacloudException('No valid credentials were used to authenticate with SAKURA Internet API.');
        }

        $this->endPoint = $endPoint;

    }

    public function api($path, $method = 'GET', $params = null, $format = 'json')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key . ':' . $this->secretKey);
        curl_setopt($ch, CURLOPT_URL, $this->endPoint . $path);
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
    public function server($serverId = '')
    {
        $server = new SacloudServer($this, $serverId);
        return $server;
    }

    /**
     * Alias to server()
     *
     * @see Sacloud::server()
     */
    public function setServer($serverId = '')
    {
        return $this->server($serverId);
    }

    /**
     * Create an archive instance.
     */
    public function archive($archiveId = '')
    {
        $archive = new SacloudArchive($this, $archiveId);
        return $archive;
    }

    /**
     * Create an facilty instance.
     */
    public function facility()
    {
     $archive = new SacloudFacility($this);
     return $archive;
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
 * SacloudException
 *
 *  @package    Sacloud
 *  @author     Masashi Sekine <sekine@cloudrop.jp>
 *  @license    Apache License 2.0
 */
class SacloudException extends Exception
{
}

/**
 * Some uility methods.
 *
 *  @package    Sacloud
 *  @author     Shoichiro Fujiwara <warafujisho@gmail.com>
 *  @license    Apache License 2.0
 */
class SacloudUtility
{
 public static function getValue($name, array $options, $defaultValue)
 {
  return isset($options[$name])?$options[$name]:$defaultValue;
 }
}
