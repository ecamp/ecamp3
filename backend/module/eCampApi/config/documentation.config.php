<?php
return array(
    'eCampApi\\V1\\Rpc\\Index\\Controller' => array(
        'description' => 'Entrypoint',
    ),
    'eCampApi\\V1\\Rpc\\Auth\\Controller' => array(
        'description' => '',
        'GET' => array(
            'description' => '/index  
displays information about current authenticated user

/google  
redirect to google authentication

/logout  
logout of current authenticated user',
            'response' => '',
        ),
        'POST' => array(
            'request' => '{
  "username": "john",
  "password": "my-password"
}',
            'description' => '/login  
allows login with username and password',
        ),
    ),
    'eCampApi\\V1\\Rpc\\Register\\Controller' => array(
        'description' => '',
        'POST' => array(
            'request' => '{
  "email": "john@example.com",
  "username": "john",
  "password": "my-password"
}',
            'description' => '/register  
creates a new user account',
        ),
    ),
    'eCampApi\\V1\\Rest\\CampType\\Controller' => array(
        'collection' => array(
            'description' => '',
            'GET' => array(
                'description' => 'List of all CampTypes',
            ),
        ),
        'entity' => array(
            'GET' => array(
                'description' => 'Each Camp belongs to one CampType.  
CampType defines some rules for e Camp.
- is_js
- is_course
- event_types',
            ),
        ),
    ),
    'eCampApi\\V1\\Rest\\EventType\\Controller' => array(
        'collection' => array(
            'GET' => array(
                'description' => 'List of all EventTypes',
            ),
        ),
        'entity' => array(
            'GET' => array(
                'description' => 'Each EventCategory refers an EventType.  
Each EventType possibly belongs to one or many CampTypes.  

EventTypes define some initial values for new EventCategories.',
            ),
        ),
    ),
    'eCampApi\\V1\\Rest\\EventCategory\\Controller' => array(
        'collection' => array(
            'GET' => array(
                'description' => 'List of all EventCategories  
Filters:  
- camp_id = [camp-id]',
            ),
            'POST' => array(
                'description' => 'Add new EventCategory',
                'request' => '{
   "camp_id": "camp-id",
   "event_type_id": "event-type-id",
   "short": "LS",
   "name": "Lagersport",
   "color": "22FF22",
   "numberingStyle": "i"
}',
            ),
        ),
        'entity' => array(
            'GET' => array(
                'description' => 'Each Event belongs to one EventCategory.  
Each EventCategory belongs to one EventType.  
EventCategory defines some rules for e Event.
- event_type
- color
- numbering_style',
            ),
            'DELETE' => array(
                'description' => 'Deletes an EventCategory',
            ),
        ),
    ),
    'eCampApi\\V1\\Rest\\Event\\Controller' => array(
        'collection' => array(
            'GET' => array(
                'description' => 'List of all Events  
Filters:  
- camp_id = [camp-id]',
            ),
            'POST' => array(
                'description' => 'Add new Event',
                'request' => '{
   "title": "Event-Title",
   "camp_id": "camp-id",
   "event_category_id": "event-category-id"
}',
            ),
        ),
        'entity' => array(
            'GET' => array(
                'description' => 'Each Event belongs to one Camp.  
Each Event refers one EventCategory.',
            ),
            'DELETE' => array(
                'description' => 'Deletes an Event',
            ),
        ),
    ),
);
