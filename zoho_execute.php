<?php

include ("ZohoV2.php");
class ZOHO_Exec{

public $zoho;
    function __construct(){
        $configuration=
            array(
                "client_id"=>"",
                "client_secret"=> "",
                "redirect_uri"=>"",
                "currentUserEmail"=> " ",
                "access_type"=>__DIR__."/",
                "persistence_handler_class"=>"ZohoOAuthPersistenceHandler",
                "token_persistence_path"=>__DIR__."/",
                "applicationLogFilePath"=>__DIR__."/",
                "user_email_id"=>""
            );
            $this->connect_zoho($configuration);          
	}
	private function connect_zoho($conf){

        $this->zoho = new ZohoV2($conf);
        return true;
    }
    public function zoho_data($row=null){
    	if(empty($row))
        	$row = array(
			    'First_Name'=>'mon mohon',
			    'Last_Name'=>'singha',
			    'Email'=>'monmohon@gmail.com',
			    'Phone'=>'917002392380',
			    'Country'=>'India',
			    'Lead_Source'=>'ATNews.com'
			);

        return $row;
    }
    private function zohoupdate($row=null,$zohoid=1234567890){
        // Get ZOHO data 
		$lead = $this->zoho_data($row);
	    
        if(!empty($zohoid) && intval($zohoid)>0) {
            $this->zoho->updateRecord( $zohoid,$lead);
               
        } else {
            //new registration
            $zohoid = $this->zoho->newRecord($lead);
			$this->set_zoho_id($row, $zohoid);
        }
    }
    private function set_zoho_id($row,$zohoid){
    	/* 
    		Here you can update your record with new zoho ID so next time you can update zoho record 
    	*/
    }
    private function listleads($page=1){
        $per_page=10;
    	$zohoArray = $this->zoho->listRecords($page,$per_page);
    }
}

?>