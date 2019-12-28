<?php
require 'vendor/autoload.php';

use zcrmsdk\oauth\ZohoOAuth;

use zcrmsdk\crm\crud\ZCRMInventoryLineItem;
use zcrmsdk\crm\crud\ZCRMJunctionRecord;
use zcrmsdk\crm\crud\ZCRMNote;
use zcrmsdk\crm\crud\ZCRMRecord;
use zcrmsdk\crm\crud\ZCRMModule;
use zcrmsdk\crm\crud\ZCRMTax;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\crm\setup\users\ZCRMUser;

use zcrmsdk\crm\crud\ZCRMCustomView;
use zcrmsdk\crm\crud\ZCRMTag;
use zcrmsdk\crm\exception\ZCRMException;

class ZohoV2 {
    
    private $oAuthClient=null;
    private $configuration=null;
    public function __construct($conf=null) {
       
       if($conf!=null)
       $this->configuration=$conf;
        //Initialize Core SDK library   
        ZCRMRestClient::initialize($this->configuration);
        $this->oAuthClient = ZohoOAuth::getClientInstance();
    
    }
    //For set all configurations
    public function setConfiguration($config){
        $this->configuration=$config;
    }

    // When after authorization you redirect you will get this grantToken and using that you will get access token
    public function generateToken($grantToken){
        return $oAuthTokens = $this->oAuthClient->generateAccessToken($grantToken);
    }
    //This function if access token expire then using refresh token you can generate new Access token
    public function refreshToken($refreshToken,$userIdentifier){
       return $oAuthTokens = $this->oAuthClient->generateAccessTokenFromRefreshToken($refreshToken,$userIdentifier);
    }

    //Update existing record
    public function updateRecord($zohoid,$data,$module="Leads"){
       try{

          $zcrmRecordIns = ZCRMRecord::getInstance($module, $zohoid);
       
          foreach($data as $d=>$v){
            $zcrmRecordIns->setFieldValue($d, $v);
          }
        
          $entityResponse=$zcrmRecordIns->update();

            if("success"==$entityResponse->getStatus()){
                
                $createdRecordInstance=$entityResponse->getData();
        
                return $createdRecordInstance->getEntityId();
                echo "Status:".$entityResponse->getStatus();
                echo "Message:".$entityResponse->getMessage();
                echo "Code:".$entityResponse->getCode();
                
                echo "EntityID:".$createdRecordInstance->getEntityId();
                echo "moduleAPIName:".$createdRecordInstance->getModuleAPIName();
           
            }
            return "";
       }catch(Exception $e){
           	$file_names = __DIR__."/zoho_ERROR_UPDATE_log.txt";
				    file_put_contents($file_names, $e.PHP_EOL , FILE_APPEND | LOCK_EX);	
            return "";
       }
        
    }
    //Create a new contact record
    public function newRecord($data,$module="Leads") {
      
    
        //if(count($data)&lt;=0)return "";
        $records = [];
        try{
       
            $record = ZCRMRecord::getInstance( $module, null );
            foreach($data as $d=>$v)
                $record->setFieldValue($d, $v);
           

            $records[] = $record;
            
            $zcrmModuleIns = ZCRMModule::getInstance($module);
            $bulkAPIResponse=$zcrmModuleIns->createRecords($records); // $recordsArray - array of ZCRMRecord instances filled with required data for creation.
            $entityResponses = $bulkAPIResponse->getEntityResponses();
       
            foreach($entityResponses as $entityResponse){
                if("success"==$entityResponse->getStatus()){
                   $createdRecordInstance=$entityResponse->getData();
                   return $createdRecordInstance->getEntityId();
                   
                }
                else{
                    $file_names = __DIR__."/zoho_ERROR_ADDNEW_log.txt";
    				        file_put_contents($file_names, (json_encode($entityResponses)).PHP_EOL , FILE_APPEND | LOCK_EX);	
    				        return "-1";
                }
            }
       
        
        }catch(Exception $e){
          $file_names = __DIR__."/zoho_ERROR_ADDNEW_log.txt";
				  file_put_contents($file_names, $e.PHP_EOL , FILE_APPEND | LOCK_EX);	
          return "";
       }
        
        
    }
    // GEt specific record by using id
    public function getRecordById($id,$module="Leads"){
        $zoho_data = array();
        $zcrmModuleIns = ZCRMModule::getInstance($module)->getRecord($id);
        $records = $zcrmModuleIns->getData();
            $record = array();
            $record['Id'] =  $records->getEntityId();
            $record['First_Name'] =  $records->getFieldValue("First_Name");
            $record['Last_Name'] =  $records->getFieldValue("Last_Name");
            $record['Email'] =  $records->getFieldValue("Email");
            $record['Country'] =  $records->getFieldValue("Country");
            $record['Lead_Status'] =  $records->getFieldValue("Lead_Status");
            $zoho_data[] = $record;
        return $zoho_data;
    }
    public function listRecords($page,$num,$module="Leads"){
        $zoho_data = array();
        $zcrmModuleIns = ZCRMModule::getInstance($module);
        $bulkAPIResponse=$zcrmModuleIns->searchRecords("ATNews",$page,$num);
        $recordsArray = $bulkAPIResponse->getData();

        foreach ($recordsArray as $field) { // each field
            $record = array();
            $record['Id'] =  $field->getEntityId();
            $record['First_Name'] =  $field->getFieldValue("First_Name");
            $record['Last_Name'] =  $field->getFieldValue("Last_Name");
            $record['Email'] =  $field->getFieldValue("Email");
            $record['Phone'] =  $field->getFieldValue("Phone");
            $record['Country'] =  $field->getFieldValue("Country");
            $record['Lead_Status'] =  $field->getFieldValue("Lead_Status");
            $zoho_data[] = $record;
        }
        return $zoho_data;
    }
}
?>