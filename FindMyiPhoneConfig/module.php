<?php
	class FindMyiPhoneConfig extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();
			$this->RequireParent("{A5FAE93E-34F2-2F0E-8D5C-929BCD5CF5B7}");
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
		}

		public function GetConfigurationForm() 
		{
			$Values = json_decode($this->GetFormData());	
			$this->SendDebug("Elements", json_encode($Values), 0);
			$form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
			$form['actions'][0]['values'] = $Values;
			return json_encode($form);
		}
		
		private function GetFormData()
		{
			$devices = $this->SendData();
			

			$guid = "{7B500376-2990-711D-7B4D-6D7D47351D73}";
			$Instances = IPS_GetInstanceListByModuleID($guid);
			
			// Configurator
			$Values = array();
			$devices = json_decode($devices, true);
			//IPS_LogMessage($this->InstanceID . " " . __FUNCTION__,print_r($devices,true));
			if ($devices == "Empty User or Password") 
			{
				IPS_LogMessage($this->InstanceID . " " . __FUNCTION__, "Empty User or Password");
				return;
			}
			elseif ($devices == "wrong credentials" || $devices == "") 
			{
				IPS_LogMessage($this->InstanceID . " " . __FUNCTION__, "No feedback from iCloud Server. Maybe wrong User or Password");
				return;
			}
			else
			{
			foreach ($devices as $device)
			{
				$ID	= 0;
				foreach ($Instances as $Instance){
					$this->SendDebug("Created Instances", IPS_GetObject($Instance)['ObjectName'] , 0);
					if (IPS_GetProperty($Instance,'DeviceID')== $device['id'])
					{
						$ID = $Instance;
					}
				}
				$Values[] = [
					'instanceID' => $ID,
					'name'       => $device['name'],
					'DeviceID'   => $device['id'],
					'modelID'    => $device['deviceDisplayName'],
					'DetailType' => $device['modelDisplayName'],
					'create'	 => 
					[
						"moduleID"      => "{7B500376-2990-711D-7B4D-6D7D47351D73}",
						"configuration" => [
							"DeviceID"  => $device['id'],
							"DeviceName" => $device['name']
						]
					]
				];
			}
			return json_encode($Values);
		}
		}

		protected function SendData() {
			$return = $this->SendDataToParent(json_encode([
				'DataID' => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}",
				'Buffer' => utf8_encode("Get_Data"),
			]));
			$this->SendDebug("Received from Gateway", $return , 0);
			//IPS_LogMessage($this->InstanceID . " " . __FUNCTION__, $return);
			return $return;
		}
	}