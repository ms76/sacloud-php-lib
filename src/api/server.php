<?php

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
