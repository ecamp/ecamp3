# eCamp v3 - API

## API für neue Entität erstellen:

Eine neue Entität für die eCamp V3 REST API wird mitte /apigility/ui als CodeGenerator erstellt. Der Code wird anschliessend an entsprechende Stellen verschoben.
Nach verschieben von SourceCode ist Anzeige unter /apigility/ui wieder leer (keine Services ersichtlich). 

- Apigility verwenden: /apigility/ui
- New Service
    - Doctrine Connected
    - EntityManager wählen
    - Entität wählen (eine)
- Service öffnen und anpassen
    - General Settings
        - Route: /api/ <originale-route>
        - Collection Name: items
    - Authorization:
        - Alle (mögliche) einschalten.

- Generierten SourceCode verschieben:
    - Neue Datei: /EcampApi/config/autoload/Vx/[EntityName].config.php
    - Inhalte zu neuer Entität von /EcampApi/config/module.config.php  in neue Datei verschieben
        - router
        - zf-versioning
        - zf-rest
        - zf-content-negotiation
        - zf-hal
        - zf-apigility
        - doctrine-hydrator
        - zf-content-validation
        - input-filter-specs
        - zf-mvc-auth

- Generierte Config anpssen  (/EcampApi/config/autoload/Vx/[EntityName].config.php)
    - doctrine-config
        - filters ergänzen - siehe bestehende Entität (EcampApi\Hydrator\Filter\BaseEntityFilter)



## Spalten einer Entität filtern

Apigility zeigt alle Daten-Spalten aus der Datenbank an. Dies ist nicht immer wünschenswert. Damit nicht alle Spalten angezeigt werden, lassen sich diese Filtern.

- Config anpassen (/EcampApi/config/autoload/Vx/[EntityName].config.php
    - doctrine-hydrator > filters
        - Neuen Eintrag erstellen ['condition' => 'and', 'filter' => ServiceManager-Key ]
    - service_manager
        - Factory für Filter eintragen.

- Filter-Factory erstellen
    - \EcampApi\Vx\Rest\[EntityName]\[EntityName]FilterFactory
        - new PropertyName(['name']);
        


## Pseudo-Spalten für eine Entität erstellen

Neben den Daten aus der DB können auch zusätzliche Informationen an den Client geschickt werden.
Diese zusätzlichen Informationen werden bei extract vom Hydrator ergänzt. 

- Config anpassen (/EcampApi/config/autoload/Vx/[EntityName].config.php)
    - doctrine-hydrator > hydrator = '\CampApi\Vx\Rest\[EntityName]\[EntityName]Hydrator'
    - \CampApi\Vx\Rest\[EntityName]\[EntityName]Hydrator  in ServiceManager registriereen (nicht HydratorManager)
        - factories: \CampApi\Vx\Rest\[EntityName]\[EntityName]Hydrator => \EcampApi\Vx\DoctrineHydratorFactory
  
- Hydrator implementieren
    - Von DoctrineModule\Stdlib\Hydrator\DoctrineObject ableiten
    - Methoden 'extract' und/oder 'hydrate' überschreiben



## Liste der gezeigten Entitäten filtern

Die Liste der gezeigten Entitäten muss einschränkbar sein. Dies muss abhängig von den Daten auf der betroffenen oder referenzierten Entitäten möglich sein. Weiter soll die Liste auch abhängig vom angemeldeten Benutzer gefiltert werden können.
Dafür wird Doctine einen QueryProvider für die betroffene Entität zur Verfügung gestellt.

- Config anpassen (/EcampApi/config/autoload/Vx/[EntityName].config.php
    - zf-apigility > doctrine-connected > query_provider
  	    - 'default' => '<EntityName>'
    - zf-apigility-doctrine-query-provider erstellen
        - aliases '<EntityName>' => 'FQCN von QueryProvider'
        - factories 'FQCN von QueryProvider' => InvokableFactory

- QueryProvider implementieren  (\EcampCore\Query\Provider\[EntityName])
    - function createQuery überschrieben
    - Basis-Function aufrufen
    - Filter ergänzen


