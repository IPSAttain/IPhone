# IPS Libary to read data from the Apple Device.
[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
![Version](https://img.shields.io/badge/Symcon%20Version-6.0%20%3E-green.svg)

Communication takes place via the "Where is?" Apple service.
The Apple ID connected to the device is required.
### 1. Requirements

- IP-Symcon Version 6.0 
- Apple device with connected Apple ID
- "Find my IPhone" must be activated in the Apple ID settings

### 2. Functions

###### Interrogate
- Charge status of the device
- Geo position of the device

###### Calculations
- Optional: Current address (Google)
- Planning: (determining the W3W location https://what3words.com/de/about/)
- Optional: entering the position on a map (Google)
- Optional: Calculate the route to the home address (km, time) (Google)

###### Send functions
- Play a search tone
- Send a message to the device
- Mark as lost
- Set notification when found 

### 3. Software-Installation

- Install the 'IPhone' module via the module store.
- Then add the configurator as an instance. The gateway is automatically installed for this.
- Enter Apple ID and password in the gateway. 

### 4. Included Module 

- __FindMyiPhoneGateway__  
	This module handles the connection with Apple.
	The Apple ID access data and update interval are entered here.
	If the interval is 0, there is no cyclical query. 
	![Instanz](docs/Gateway_Config.png)

- __FindMyiPhoneConfig__  
	Determines the devices connected to the Apple ID and corresponding instances can be created using them.
	Is only required to set up the instance and can then be removed again. 

- __FindMyiPhoneModul__  
	For each device, an Instance must create.
	The information is stored under this instance.
	Corresponding actions have been created for this instance in order to be able to carry out the send functions.
	![Aktion](docs/Aktion.png)  
	The cards and address information can be activated here.
	These are then queried via Google Maps. A personal API key is required for this.
	Further information under the following link:
	https://developers.google.com/maps/documentation/android-sdk/get-api-key?hl=de  
	![Instanz](docs/Instanz_Config.png)
### 5. Weiter Informationen

Special thank to

Copyright (c) 2013 Neal <neal@ineal.me>  
https://github.com/Neal/FindMyiPhone  
Thanks for sharing