<?php

error_reporting(E_ALL & ~E_DEPRECATED);

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
- activityTypes',
            ],
        ],
    ],
    'eCampApi\\V1\\Rest\\ActivityType\\Controller' => [
        'collection' => [
            'GET' => [
                'description' => 'List of all ActivityTypes',
            ],
        ],
        'entity' => [
            'GET' => [
                'description' => 'Each ActivityCategory refers an ActivityType.  
Each ActivityType possibly belongs to one or many CampTypes.  

ActivityTypes define some initial values for new ActivityCategories.',
            ],
        ],
    ],
    'eCampApi\\V1\\Rest\\ActivityCategory\\Controller' => [
        'collection' => [
            'GET' => [
                'description' => 'List of all ActivityCategories  
Filters:  
- campId = [camp-id]',
            ],
            'POST' => [
                'description' => 'Add new ActivityCategory',
                'request' => '{
   "campId": "camp-id",
   "activityTypeId": "activity-type-id",
   "short": "LS",
   "name": "Lagersport",
   "color": "22FF22",
   "numberingStyle": "i"
}',
            ],
        ],
        'entity' => [
            'GET' => [
                'description' => 'Each Activity belongs to one ActivityCategory.  
Each ActivityCategory belongs to one ActivityType.  
ActivityCategory defines some rules for e Activity.
- activityType
- color
- numberingStyle',
            ],
            'DELETE' => [
                'description' => 'Deletes an ActivityCategory',
            ],
        ],
    ],
    'eCampApi\\V1\\Rest\\Activity\\Controller' => [
        'collection' => [
            'GET' => [
                'description' => 'List of all Activities  
Filters:  
- campId = [camp-id]',
            ],
            'POST' => [
                'description' => 'Add new Activity',
                'request' => '{
   "title": "Activity-Title",
   "campId": "camp-id",
   "activityCategoryId": "activity-category-id"
}',
            ],
        ],
        'entity' => [
            'GET' => [
                'description' => 'Each Activity belongs to one Camp.  
Each Activity refers one ActivityCategory.',
            ],
            'DELETE' => [
                'description' => 'Deletes an Activity',
            ],
        ],
    ],
];
