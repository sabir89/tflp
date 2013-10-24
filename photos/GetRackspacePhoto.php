<?php 
require_once(WP_PLUGIN_DIR . "/tflp/photos/PhotoConstants.php");
require_once(WP_PLUGIN_DIR . "/tflp/photos/Logger.php");
//for rackspace API
require_once(WP_PLUGIN_DIR . "/tflp/photos/rackspace-api/lib/php-opencloud.php");

use OpenCloud\Rackspace;   //need for cloud storage API

class GetRackspacePhoto{
	
	private $objStore;
	private $log; // for logging
	
	public function __construct(){
		$this->log = new Logger("GetRackspacePhoto");
		$this->connect_to_cloud_storage();
	}
	
	public function getPropertyPhotoURL($propertyUID, $photoNum='1', $photoType='Photo'){
		$photoContainer = NULL;
		$propPhoto = NULL;
		try{
				$photoContainer = $this->objStore->Container($propertyUID);
		}catch(Exception $e){ //container doesn't exist, return
			$this->log->debug("Photo {$photoNum} not available for this property: {$propertyUID}");
			$photoContainer = NULL;
		}
		if(isset($photoContainer)){
			$photoName = "{$propertyUID}-{$photoNum}.{$photoType}.jpg";
			try{
					$propPhoto = $photoContainer->DataObject($photoName);
			}catch(Exception $e){//error getting photo from cloud
				$this->log->debug("Errror reading photo {$photoNum} of the property: {$propertyUID} from the cloud");
				$propPhoto = NULL;	
			}			 
		}
		//send public URL for this photo
		//echo("Photo URL: {$propPhoto->PublicURL()}");
		return $propPhoto->PublicURL();		
	}
	
	public function getConatinerURL($propertyUID){
		$photoContainer = NULL;
		try{
				$photoContainer = $this->objStore->Container($propertyUID);
		}catch(Exception $e){ //container doesn't exist, return
			$this->log->debug("Photo {$photoNum} not available for this property: {$propertyUID}");
			$photoContainer = NULL;
		}
		if(isset($photoContainer)){
			return $photoContainer->PublicURL();				 
		}
	}	
	
	
	private function connect_to_cloud_storage(){		
		//credentials
		$cred = array(
    		'username' => PhotoConstants::RACKSPACE_USER,
    		'apiKey' => PhotoConstants::RACKSPACE_API_KEY
		);

		try{
			// establish our credentials
			$cloudConn = new Rackspace(PhotoConstants::RACKSPACE_AUTH_URL, $cred);
			$this->objStore = $cloudConn->ObjectStore("cloudFiles", PhotoConstants::RACKSPACE_REGION, "publicURL");
			
			if(isset($this->objStore)){
				//$this->log->info("Connected to cloud!");	
			}		
		}catch(Exception $ex){
			die("Unable to connect to cloud storage: {$ex}");	
		}		 
	}		
}

?>