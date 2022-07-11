<?php
    require_once __DIR__ . '/../libs/FindMyiPhone.php';
    class FindMyiPhoneConfig extends IPSModule
    {
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
            $devices = json_decode($this->SendData(),true);
            $guid = "{7B500376-2990-711D-7B4D-6D7D47351D73}";
            //$Instances = IPS_GetInstanceListByModuleID($guid);

            // Get all the instances that are connected to the configurators I/O
            $connectedInstanceIDs = [];
            foreach (IPS_GetInstanceListByModuleID($guid) as $instanceID) {
                if (IPS_GetInstance($instanceID)['ConnectionID'] === IPS_GetInstance($this->InstanceID)['ConnectionID']) {
                    // Add the instance ID to a list for the given address. Even though addresses should be unique, users could break things by manually editing the settings
                    $connectedInstanceIDs[IPS_GetProperty($instanceID, 'DeviceID')][] = $instanceID;
                }
            }
            
            // Configurator
            $values = [];
            if ($devices == "Empty User or Password") {
                $this->SendDebug(__FUNCTION__, " Empty User or Password", 0);
                return;
            } elseif ($devices == "wrong credentials" || $devices == "") {
                $this->SendDebug(__FUNCTION__, " No feedback from iCloud Server. Maybe wrong User or Password", 0);
                return;
            } else {
                foreach ($devices as $device) {
                    $ID	= 0;
                    $value = [
                    'DeviceID'  => $device['id'],
                    'create'	=> [
                        "moduleID"      => "{7B500376-2990-711D-7B4D-6D7D47351D73}",
                        "configuration" => [
                            "DeviceID"  => $device['id'],
                            "DeviceName" => $device['name']
                        ]
                    ]
                ];
                if (isset($connectedInstanceIDs[$device])) {
                    $value['name'] = IPS_GetName($connectedInstanceIDs[$device][0]);
                    $value['instanceID'] = $connectedInstanceIDs[$device][0];
                    $value['modelID']    = $device['deviceDisplayName'];
                    $value['DetailType'] = $device['modelDisplayName'];
                }
                else {
                    $value['name'] = 'Device ' . $device;
                    $value['instanceID'] = 0;
                    $value['modelID']    = '';
                    $value['DetailType'] = '';
                }
                $values[] = $value;
                }

                foreach ($connectedInstanceIDs as $address => $instanceIDs) {
                    foreach ($instanceIDs as $index => $instanceID) {
                        // The first entry for each found address was already added as valid value
                        if (($index === 0) && (in_array($address, $devices))) {
                            continue;
                        }
    
                        // However, if an address is not a found address or an address has multiple instances, they are erroneous
                        $values[] = [
                            'DeviceID' => $address,
                            'name' => IPS_GetName($instanceID),
                            'instanceID' => $instanceID
                        ];
                    }
                }

                return json_encode([
                    'elements' => [
                        [
                            'type' => 'Configurator',
                            'values' => $values
                        ]
                    ]
                ]);

                //return json_encode($values);
            }
        }

        protected function SendData()
        {
            $return = $this->SendDataToParent(json_encode([
                'DataID' => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}",
                'Buffer' => utf8_encode("Get_Data"),
            ]));
            $this->SendDebug("Received from Gateway", $return, 0);
            //$this->LogMessage(__FUNCTION__, $return , 10206);
            return $return;
        }
    }
