<?php
/**
 * SacloudFacility
 *
 *  @package    Sacloud
 *  @author     Shoichiro Fujiwara <warafujisho@gmail.com>
 *  @license    Apache License 2.0
 */
class SacloudFacility{
 protected $sacloud;

 const PATH_PREFIX_REGION = '/region';
 const PATH_PREFIX_ZONE = '/zone';
 
 public function __construct(Sacloud $sacloud)
 {
  $this->sacloud = $sacloud;
 }

 /**
  * Get region information.
  * If $regionId is not passed, it returns a list.
  * @param string $regionId
  * @return Ambigous <multitype:mixed multitype: , mixed>
  */
 public function getRegion($regionId = null)
 {
  $path = self::PATH_PREFIX_REGION; 
  if(empty($regionId)){
   $path .= '/'.$regionId;
  }
  return $this->sacloud
  ->api($path, 'GET');
 }
 
 /**
  * Get zone information.
  * If $zoneId is not passed, it returns a list.
  * @param string $zoneId
  * @return Ambigous <multitype:mixed multitype: , mixed>
  */
 public function getZone($zoneId = null)
 {
  $path = self::PATH_PREFIX_ZONE;
  if(!empty($zoneId)){
   $path .= '/'.$zoneId;
  }
  return $this->sacloud
  ->api($path, 'GET');
 }
}