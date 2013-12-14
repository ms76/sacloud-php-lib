<?php

/**
 * SacloudArchive
 *
 *  @package    Sacloud
 *  @author     Shoichiro Fujiwara <warafujisho@gmail.com>
 *  @license    Apache License 2.0
 */
class SacloudArchive{
 protected $id;
 protected $sacloud;
 
 const PATH_PREFIX = '/archive';

 public function __construct(Sacloud $sacloud, $archiveId)
 {
  $this->sacloud = $sacloud;
  $this->id = $archiveId;
 }

 /**
  * Get archive information.
  * If both $archiveId and $this->id is null, then it return a list.
  */
 public function get($archiveId = null)
 {
  if(empty($archiveId) && empty($this->id)){
   return $this->getList();
  }
  $id = !empty($archiveId)?$archiveId:$this->id;
  $path = self::PATH_PREFIX.'/'.$id;
  return $this->sacloud->api($path, 'GET');
 }

 /**
  * Get an archive information list.
  */
 public function getList(){
  $path = self::PATH_PREFIX;
  return $this->sacloud->api($path, 'GET');
 }
 
 /**
  * Create a new archive from a resource.
  * The priority between source configuration options: 
  * 1. sourceDiskId
  * 2. sourceArchiveId
  * 3. sizeMb
  * These options should not be passed together. 
  * 
  * WARNING: It actually creates a resource which you have to pay for.
  */
 public function create($options = array()){
  $name = SacloudUtility::getValue('name', $options, '');
  $description = SacloudUtility::getValue('description', $options, '');
  $zone = SacloudUtility::getValue('zone', $options, '');

  if(!empty($options['zoneId'])){
   $zoneParamStr = '"Zone":{"ID":"'.$options["zoneId"].'"},';
  }else{
   $zoneParamStr = '';
  }
  
  if(!empty($options['sourceDiskId'])){
   $sourceParamStr = '"SourceDisk":{"ID":"'.$options['sourceDiskId'].'"}';
  }else if(!empty($options['sourceArchiveId'])){
   $sourceParamStr = '"SourceArchive":{"ID":"'.$options['sourceArchiveId'].'"}';
  }else if(!empty($options['sizeMb'])){
   $sourceParamStr = '"SizeMB":'.$options['sizeMb'];
  }else {
   throw new SacloudException('No source specified.');
  }
  
  $path = self::PATH_PREFIX;
  $param = sprintf('{"Archive": {"Name":"%s","Description":"%s",%s%s}}', 
    $name, $description, $zoneParamStr, $sourceParamStr);
  return $this->sacloud->api($path, 'POST', $param);
 }
 
 /**
  * Delete a archive.
  * WARNING: Once deleted, you cannot undo it.
  */
 public function delete($archiveId = null){
  $id = !empty($archiveId)?$archiveId:$this->id;
  $path = self::PATH_PREFIX.'/'.$id;
  return $this->sacloud->api($path, 'DELETE');
 }
 
}
