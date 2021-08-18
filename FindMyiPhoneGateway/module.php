<?php
require_once __DIR__ . '/../libs/FindMyiPhone.php';
	class FindMyiPhoneGateway extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();

			$this->RegisterPropertyString("User", "user@domain.tld");
			$this->RegisterPropertyString("Password", "");
			$this->RegisterPropertyInteger("Refresh",5);
			$this->RegisterTimer("Update", 300000, "FMiP_UpdateData($this->InstanceID);");
		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
			$this->SetTimerInterval("Update", $this->ReadPropertyInteger("Refresh")*60000);
		}

		public function ForwardData($JSONString)
		{
			$data = json_decode($JSONString,true);
			//$this->LogMessage(__FUNCTION__, utf8_decode($data->Buffer) , 10206);
			$data = preg_split('/\n|\r\n?/', $data['Buffer']);
			//$this->LogMessage(__FUNCTION__, print_r($data) , 10206) ;
			$User = $this->ReadPropertyString("User");
			$Password = $this->ReadPropertyString("Password");
			$returndata = "";
			switch ($data[0])
			{
				case 'Get_Data':
					$returndata = $this->GetData();
					break;

				case 'Update_Device_Data':
						$returndata = $this->UpdateData();
						break;

				case 'Play_Sound':
					$FindMyiPhone = new FindMyiPhone($User, $Password);  
					$returndata = ($FindMyiPhone->play_sound($data[1])->statusCode == 200) ? 'Sent!' : 'Failed!';
					break;

				case 'Send_Message':
					$FindMyiPhone = new FindMyiPhone($User, $Password);
					$returndata = ($FindMyiPhone->send_message($data[1], $data[2], false, 'Important Message')->statusCode == 200) ? 'Sent!' : 'Failed!';
					break;
				
				case 'Lost_Device':
					$FindMyiPhone = new FindMyiPhone($User, $Password);
					//$data[2] => passcode ( 4 digits)
					//$data[3] => owner_phone_number
					//$data[4] => sound (true/false)
					//$data[5] => message
					$returndata = ($FindMyiPhone->lost_device($data[1], $data[2], $data[3], true, $data[5])->statusCode == 200) ? 'Sent!' : 'Failed!';
					break;

				case 'Phone_Found':
					$FindMyiPhone = new FindMyiPhone($User, $Password);  
					$returndata = ($FindMyiPhone->notify_when_found($data[1])->statusCode == 200) ? 'Sent!' : 'Failed!';
					break;
			}
			return $returndata;
		}
		
		public function UpdateData()
		{
			$phonedata = $this->GetData();
			$this->SendDataToChildren(json_encode(Array("DataID" => "{018EF6B5-AB94-40C6-AA53-46943E824ACF}", "Buffer" => $phonedata)));
		}

		protected function GetData()
		{
			$User = $this->ReadPropertyString("User");
			$Password = $this->ReadPropertyString("Password");
			if ($Password == "" || $User == "") 
			{
				$this->LogMessage(__FUNCTION__, ' Empty User or Password' ,10204);
				$this->SendDebug("Config", "Empty User or Password" , 0);
				return "Empty User or Password";
			}
			$this->SendDebug("Request Data", "User = $User " , 0);
			$FindMyiPhone = new FindMyiPhone($User, $Password);
			$devices = $FindMyiPhone->devices;
			$devices = json_encode($devices);
			$this->SendDebug("Send to Child", print_r($devices,true) , 0);
			return $devices;
		}
	}