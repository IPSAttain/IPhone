# IPS PHP Biliothek für die Abfrage von Daten eines Apple Gerätes.
[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
![Version](https://img.shields.io/badge/Symcon%20Version-6.0%20%3E-green.svg)

Die Kommunikation erfolgt über den "Wo ist?" Dienst von Apple.  
Dazu ist die mit dem Gerät verbundene Apple-ID nötig. 

### 1. Funktionsumfang
##### Abfragen
- Ladezustand und Ladestatus des Geräts
- Geo Position des Gerätes
##### Berrechnungen aus Position
- Aktuelle Adresse
- in Planung (Ermittel der W3W Standorts https://what3words.com/de/about/)
- Eintragen der Position in eine Karte
- Berrechnen der Route zur Home Adresse (km , Zeit)
##### Sende Funktionen
- Gerät zum Abspielen eines Suchtones veranlassen (Play Sound)
- An das Gerät eine Mitteilung senden
- Als verloren markieren
- Benachrichtigung wenn gefunden setzen
### 2. Vorraussetzungen

- IP-Symcon ab Version 6.0 (Für die Sende Funktionen stehen entsprechende Aktionen zur Verfügung )
- Apple Gerät mit verbundener Apple-ID

### 3. Folgende Module beinhaltet das IPhone Repository:

- __FindMyiPhoneGateway__ ([Dokumentation](FindMyiPhoneGateway))  
	Dieses Modul händelt die Verbindung mit Apple.  
	Hier werden die Apple-ID Zugangsdaten und Aktualisierungsintervall eingetragen.

	![Instanz](docs/Gateway_Config.png)

- __FindMyiPhoneConfig__ ([Dokumentation](FindMyiPhoneConfig))  
	Ermittelt die mit der Apple-ID verbundennen Geräte und entsprechende Instanzen können darüber angelegt werden.  
	Wird nur zur Instanzeinrichtung benötigt und kann danach wieder entfernt werden.

- __FindMyiPhoneModul__ ([Dokumentation](FindMyiPhoneModul))  
	Instanz pro Gerät.  
	