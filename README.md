# Phoenixchat

Der Phoenixchat, oder auch: die einfachst mögliche Implementierung eines Chats.
Benötigt wird:
* ein Server mit PHP
* eine MySQL-Datenbank
* das Paket [RedBeanPHP](https://www.redbeanphp.com)

## Technisches / API

Alle Anfragen werden per **POST**-Anfragen an den Server gemacht. **GET**-Anfragen werden nicht bearbeitet.

### Parameter

**action**

Alle Anfragen **müssen** den Parameter _action_ enthalten. Mögliche Werte sind:
* _new_

  Erstellt einen neuen Chatroom. Gibt die Nummer des Chatrooms als reinen Text zurück. (6-stellige Zahl)
  
  Erfordert, dass die Eigenschaft _user_ mitgegeben wird!
* _get_

  Gibt ein Array mit Objekten im Format JSON zurück.
  
  Erfordert, dass die Eigenschaften _user_ und _chat_ mitgegeben werde. Optional kann die Zeit (Unixtime) als _date_ mitgegeben werden. Dann werden alle Nachrichten ab diesem Timestamp mitgegeben.
* _add_

  Erstellt eine neue Nachricht. Gibt eine Erfolgsmeldung als reinen Text zurück.
  
  Erfordert, dass die Eigenschaften _user_, _chat_ und _message_ mitgegeben werden. _Message_ kann Unicode-Zeichen enthalten.
