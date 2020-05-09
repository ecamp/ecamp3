<?php

return [
    'eCampApi\\V1\\Rpc\\Index\\Controller' => [
        'description' => 'Entrypoint',
    ],
    'eCampApi\\V1\\Rpc\\Auth\\Controller' => [
        'description' => '',
        'GET' => [
            'description' => '/index  
displays information about current authenticated user

/google  
redirect to google authentication

/logout  
logout of current authenticated user',
            'response' => '',
        ],
        'POST' => [
            'request' => '{
  "username": "john",
  "password": "my-password"
}',
            'description' => '/login  
allows login with username and password',
        ],
    ],
    'eCampApi\\V1\\Rpc\\Register\\Controller' => [
        'description' => '',
        'POST' => [
            'request' => '{
  "email": "john@example.com",
  "username": "john",
  "password": "my-password"
}',
            'description' => '/register  
creates a new user account',
        ],
    ],
    'eCampApi\\V1\\Rest\\CampType\\Controller' => [
        'collection' => [
            'description' => '',
            'GET' => [
                'description' => 'List of all CampTypes',
            ],
        ],
        'entity' => [
            'GET' => [
                'description' => 'Each Camp belongs to one CampType.  
CampType defines some rules for e Camp.
- isJS
- isCourse
- eventTypes',
            ],
        ],
    ],
    'eCampApi\\V1\\Rest\\EventType\\Controller' => [
        'collection' => [
            'GET' => [
                'description' => 'List of all EventTypes',
            ],
        ],
        'entity' => [
            'GET' => [
                'description' => 'Each EventCategory refers an EventType.  
Each EventType possibly belongs to one or many CampTypes.  

EventTypes define some initial values for new EventCategories.',
            ],
        ],
    ],
    'eCampApi\\V1\\Rest\\EventCategory\\Controller' => [
        'collection' => [
            'GET' => [
                'description' => 'List of all EventCategories  
Filters:  
- campId = [camp-id]',
            ],
            'POST' => [
                'description' => 'Add new EventCategory',
                'request' => '{
   "campId": "camp-id",
   "eventTypeId": "event-type-id",
   "short": "LS",
   "name": "Lagersport",
   "color": "22FF22",
   "numberingStyle": "i"
}',
            ],
        ],
        'entity' => [
            'GET' => [
                'description' => 'Each Event belongs to one EventCategory.  
Each EventCategory belongs to one EventType.  
EventCategory defines some rules for e Event.
- eventType
- color
- numberingStyle',
            ],
            'DELETE' => [
                'description' => 'Deletes an EventCategory',
            ],
        ],
    ],
    'eCampApi\\V1\\Rest\\Event\\Controller' => [
        'collection' => [
            'GET' => [
                'description' => 'List of all Events  
Filters:  
- campId = [camp-id]',
            ],
            'POST' => [
                'description' => 'Add new Event',
                'request' => '{
   "title": "Event-Title",
   "campId": "camp-id",
   "eventCategoryId": "event-category-id"
}',
            ],
        ],
        'entity' => [
            'GET' => [
                'description' => 'Each Event belongs to one Camp.  
Each Event refers one EventCategory.',
            ],
            'DELETE' => [
                'description' => 'Deletes an Event',
            ],
        ],
    ],
];
