# FindMyiPhoneModul
Beschreibung des Moduls.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen](#5-statusvariablen)
6. [Aktionen](#6-aktionen)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

### 2. Vorraussetzungen

- IP-Symcon ab Version 6.0
- Apple-ID und Passwort
### 3. Software-Installation

* Über den Module Store das 'FindMyiPhoneModul'-Modul installieren.

### 4. Einrichten der Instanzen in IP-Symcon

Instanzen sollten mit dem Konfigurator erstellt werden.
	Für jedes Gerät wird eine Instanz angelegt.  
	Unter dieser Instanz werden die Informationen abgelegt.  
	Die Karten und Adressinformationen können hier aktiviert werden.  
	Diese werden dann über Google Maps abgefragt. Dazu ist ein persönlicher API Key nötig.  
	Weitere Infos unter folgendem Link:  
	https://developers.google.com/maps/documentation/android-sdk/get-api-key?hl=de  
	![Instanz](docs/Instanz_Config.png)
__Konfigurationsseite__:

![Instanz](docs/Instanz_Config.png)

### 5. Statusvariablen

Die Statusvariablen werden automatisch angelegt. 
Wenn diese gelöscht werden, werden sie neu erstellt. Karten und Adressinformationen werden entfernt, wenn die entsprechenden Optionen, in der Konfiguration der Instanz, deaktiviert werden

### 6. Aktionen

	Es sind entsprechende Aktionen verfügbar, um die Sende Funktionen ausführen zu können.  
	![Aktion](docs/Aktion.png)  

### 7. PHP-Befehlsreferenz

Beispiel:
`FMiP_UpdateDeviceData(12345);`
Fragt die Daten aus ICloud ab.
