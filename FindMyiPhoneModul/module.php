<?php
	class FindMyiPhoneModul extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();
			$this->ConnectParent('{A5FAE93E-34F2-2F0E-8D5C-929BCD5CF5B7}'); 
			$this->RegisterPropertyString('DeviceID', "");
			$this->RegisterPropertyString('DeviceName', "");
			$this->RegisterPropertyString('Location', '{"latitude":52.5163,"longitude":13.3777}');
			$this->RegisterPropertyBoolean('active_googlemap', false);
			$this->RegisterPropertyBoolean('static_googlemap', false);
			$this->RegisterPropertyString('googlemap_api_key', '');
			$this->RegisterPropertyInteger('horizontal_mapsize', 600);
			$this->RegisterPropertyInteger('vertical_mapsize', 400);
			$this->RegisterPropertyBoolean('routing_information', false);
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
			$Idents = array("fmip_route_time", "fmip_route_distance", "fmip_route_address");
			if (!$this->ReadPropertyBoolean('routing_information')) $this->DeleteVars($Idents);
			$Idents = array("fmip_dynmap");
			if (!$this->ReadPropertyBoolean('active_googlemap')) $this->DeleteVars($Idents);
			$Idents = array("fmip_googlemap", "fmip_googlemap_zoom");
			if (!$this->ReadPropertyBoolean('static_googlemap')) $this->DeleteVars($Idents);
		}

		public function ReceiveData($JSONString) {
			$data = json_decode($JSONString,true);
			$data['Buffer'] = utf8_decode($data['Buffer']);
			$this->ProceedData($data['Buffer']);
		}

		protected function ProceedData($data)
		{
			$devices = json_decode($data,true);
			$this->SendDebug(__FUNCTION__, print_r($devices,true) , 0);
			// todo...
			if ($devices == "wrong credentials" || $devices == "") 
			{
				$this->LogMessage(__FUNCTION__. " No feedback from iCloud Server. Maybe wrong User or Password" ,10204);
				return;
			}
			foreach ($devices as $device)
			{
				if ($this->ReadPropertyString('DeviceID') == $device['id'])
				{
					$this->SendDebug(__FUNCTION__, "Matched Device ID => " . $device['id'] , 0);
					$Ident = 'batteryLevel';
					if(isset($device[$Ident]))
					{
						$Value = intval($device[$Ident] * 100);
						$this->SaveData('fmip_' . $Ident, $Value, $this->Translate('Battery'), '~Battery.100', 'Integer',2);
					}
					$Ident = 'batteryStatus';
					if(isset($device[$Ident])) $this->SaveData('fmip_' . $Ident, $device[$Ident], $this->Translate('Battery'), '', 'String',3);
					$Ident = 'name';
					if(isset($device[$Ident])) $this->SaveData('fmip_' . $Ident, $device[$Ident], $this->Translate('Name'), '', 'String',1);
					$Ident = 'lowPowerMode';
					if(isset($device[$Ident])) $this->SaveData('fmip_' . $Ident, $device[$Ident], $this->Translate('Low Power Mode'), '~Switch', 'Bool',5);
					$Ident = 'location';
					if(isset($device['location']))
					{
						$Ident = 'latitude';
						$this->RegisterProfile('FMiP.Location', 'Mobile', '', ' °', 0, 0, 0, 5, 2);
						$this->SaveData('fmip_' . $Ident, $device['location'][$Ident], $this->Translate('Latitude'), 'FMiP.Location', 'Float',11);
						$Ident = 'longitude';
						$this->RegisterProfile('FMiP.Location', 'Mobile', '', ' °', 0, 0, 0, 5, 2);
						$this->SaveData('fmip_' . $Ident, $device['location'][$Ident], $this->Translate('Longitude'), 'FMiP.Location', 'Float',12);
						$Ident = 'horizontalAccuracy';
						$this->RegisterProfile('FMiP.Distance', 'Distance', '', ' Meter', 0, 0, 0, 2, 2);
						$this->SaveData('fmip_' . $Ident, $device['location'][$Ident], $this->Translate('Accuracy'), 'FMiP.Distance', 'Float',13);
						$Ident = 'timeStamp';
						$timestamp = intval($device['location'][$Ident]/1000);
						$this->SaveData('fmip_' . $Ident, $timestamp, $this->Translate('Date'), '~UnixTimestamp', 'Integer',14);
						$pos = number_format(floatval($device['location']['latitude']), 6, '.', '') . ',' . number_format(floatval($device['location']['longitude']), 6, '.', '');
						if ($this->ReadPropertyBoolean('static_googlemap')) $this->StaticMap($pos);
						if ($this->ReadPropertyBoolean('active_googlemap')) $this->DynamicMap($pos);
						if ($this->ReadPropertyBoolean('routing_information')) $this->AdvancedRouting($pos);
					}
					else
					{
						$this->SendDebug(__FUNCTION__, "No location recived" , 0);
					}
				}
			}
			
		}

		protected function DynamicMap($pos)
		{
			$homelocation = json_decode($this->ReadPropertyString('Location'));
			$homepos = number_format($homelocation->latitude,5,'.','') . ',' . number_format($homelocation->longitude,5,'.','');
			$horizontal_size = $this->ReadPropertyInteger('horizontal_mapsize');
			$vertical_value = $this->ReadPropertyInteger('vertical_mapsize');
			$api_key = $this->ReadPropertyString('googlemap_api_key');
			$dynmap = '<iframe width="'. $horizontal_size . '"height="' . $vertical_value;
			$dynmap .='"src="https://maps.google.de/maps?key=' . $api_key;
			$dynmap .='&saddr='.$pos.'&daddr='.$homepos.'(Home)&t=h&output=embed" frameborder="0" scrolling="no" ></iframe>';
			$this->SaveData('fmip_dynmap', $dynmap, $this->Translate('map'), '~HTMLBox', 'String',33);	
		}

		protected function StaticMap($pos)
		{
			$homelocation = json_decode($this->ReadPropertyString('Location'));
			$homepos = number_format($homelocation->latitude,5,'.','') . ',' . number_format($homelocation->longitude,5,'.','');
			$horizontal_size = $this->ReadPropertyInteger('horizontal_mapsize');
			$vertical_value = $this->ReadPropertyInteger('vertical_mapsize');
			$markercolor = 'red';
			$api_key = $this->ReadPropertyString('googlemap_api_key');
			$this->RegisterVariableInteger('fmip_googlemap_zoom', $this->Translate('map zoom'), '~Intensity.100', 30);
			$zoom = $this->GetValue('fmip_googlemap_zoom');
			$url = 'https://maps.google.com/maps/api/staticmap?key=' . $api_key;
			$url .= '&center=' . rawurlencode($pos);
			// zoom 0 world - 21 building
			if ($zoom > 0) {
				$url .= '&zoom=' . rawurlencode(strval($zoom));
			}
			
			$url .= '&size=' . rawurlencode(strval($horizontal_size) . 'x' . strval($vertical_value));
			//$url .= '&maptype=' . rawurlencode(strval($maptype));
			$url .= '&markers=' . rawurlencode('color:' . strval($markercolor) . '|' . strval($pos));
			$url .= '&sensor=true';

			$this->SendDebug(__FUNCTION__, 'url=' . $url, 0);
			$output = '<img src="' . $url . '" />';
			$this->SaveData('fmip_googlemap', $output, $this->Translate('map'), '~HTMLBox', 'String',35);
		}

		protected function AdvancedRouting($source)
		{
			$homelocation = json_decode($this->ReadPropertyString('Location'));
			$homepos = number_format($homelocation->latitude,5,'.','') . ',' . number_format($homelocation->longitude,5,'.','');
			$api_key = $this->ReadPropertyString('googlemap_api_key');
			$Route = simplexml_load_file(utf8_encode('https://maps.googleapis.com/maps/api/directions/xml?key=' . $api_key . '&origin=' . $source . '&destination=' . $homepos . '&sensor=false'));
			if ((string)$Route->status == "OK"){
				$Time = strval($Route->route->leg->duration->text);
				$this->SaveData('fmip_route_time', $Time, $this->Translate('Travel Time'), '', 'String', 21);
				//Road Distance
				$Distance = strval($Route->route->leg->distance->text); 
				$this->SaveData('fmip_route_distance', $Distance, $this->Translate('Travel Distance'), '', 'String' , 20);
				$Address =strval($Route->route->leg->start_address);
				$this->SaveData('fmip_route_address', $Address, $this->Translate('Current Location'), '', 'String', 22);
			}
			return;
		}

		// save Data
		protected function SaveData($Ident, $Value, $Name, $Profil, $VarType, $TreePosition = 0)
		{
			if ($VarType == "Bool") 	$this->RegisterVariableBoolean($Ident, $this->Translate($Name), $Profil, $TreePosition);
			if ($VarType == "Integer") 	$this->RegisterVariableInteger($Ident, $this->Translate($Name), $Profil, $TreePosition);
			if ($VarType == "Float") 	$this->RegisterVariableFloat($Ident, $this->Translate($Name), $Profil, $TreePosition);
			if ($VarType == "String") 	$this->RegisterVariableString($Ident, $this->Translate($Name), $Profil, $TreePosition);
			if($this->GetValue($Ident) != $Value) 
			{
				$this->SetValue($Ident, $Value);
				$this->SendDebug(__FUNCTION__, 'Variable with Ident => "' . $Ident . '", set to Value: =>' . $Value, 0);
			}
		}

		// Delete Vars
		protected function DeleteVars($Idents)
		{
			//$Idents = preg_split(' ', $Idents);
			foreach ($Idents as $Ident)
			{
				$this->MaintainVariable($Ident, "", 3, "", 0, false);
				$this->SendDebug(__FUNCTION__, 'Variable deleted. Ident => ' . $Ident , 0);
			}
		}

		//Profile
		protected function RegisterProfile($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits, $Vartype)
		{
			if (!IPS_VariableProfileExists($Name)) {
				IPS_CreateVariableProfile($Name, $Vartype); // 0 boolean, 1 int, 2 float, 3 string,
			} else {
				$profile = IPS_GetVariableProfile($Name);
				if ($profile['ProfileType'] != $Vartype) {
					$this->SendDebug(__FUNCTION__, 'Variable profile type does not match for profile ' . $Name, 0);
				}
			}
			IPS_SetVariableProfileIcon($Name, $Icon);
			IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
			IPS_SetVariableProfileDigits($Name, $Digits); //  Nachkommastellen
			IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize); // string $ProfilName, float $Minimalwert, float $Maximalwert, float $Schrittweite
		}

		######### Public Send Functions #######

		public function Play_Sound()
		{
			$return = $this->Send_to_Parent("Play_Sound\r". $this->ReadPropertyString('DeviceID'));
			return $return;
		}
		
		public function Send_Message(string $message)
		{
			$return = $this->Send_to_Parent("Send_Message\r". $this->ReadPropertyString('DeviceID') . "\r" . $message);
			return $return;
		}

		public function Lost_Device(string $passcode, string $owner_phone_number, string $message)
		{
			$return = $this->Send_to_Parent("Lost_Device\r". $this->ReadPropertyString('DeviceID') . "\r" . $passcode . "\r" . $owner_phone_number . "\r".true."\r" . $message);
					//$data[2] => passcode ( 4 digits)
					//$data[3] => owner_phone_number
					//$data[4] => sound (true/false)
					//$data[5] => message
			return $return;
		}

		public function Notify_When_Found()
		{
			$return = $this->Send_to_Parent("Phone_Found\r". $this->ReadPropertyString('DeviceID'));
			return $return;
		}

		public function UpdateDeviceData()
		{
			$return = $this->Send_to_Parent("Update_Device_Data\r". $this->ReadPropertyString('DeviceID'));
			return $return;
		}
		
		protected function Send_to_Parent($Buffer)
		{
			$return = $this->SendDataToParent(json_encode([
				'DataID' => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}",
				'Buffer' => utf8_encode($Buffer),
			]));
			$this->SendDebug(__FUNCTION__,  $return , 0);
			return $return;
		}

	}