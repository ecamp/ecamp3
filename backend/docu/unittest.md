# UnitTest

Durch UnitTests soll sicher gestellt werden, dass bereits entwickelte
Funktionalität erhalten bleibt.

Es gibt 3 Grundlengende Typen von UnitTests:

- UnitTests ohne Datenbank
- UnitTests mit Datenbank
- UnitTests mit Http-Requests



## UnitTests ohne Datenbank

Für UnitTests ohne Datenbank wird die Klasse 
```\eCamp\LibTest\PHPUnit\AbstractTestCase``` verwendet.

Beispiel:
```php
<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\User;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class UserTest extends AbstractTestCase
{

    public function testUserNonRegistered() {
        $user = new User();
        $user->setUsername('username');
        $user->setMailAddress('test@eCamp3.ch');

        $this->assertEquals(User::STATE_NONREGISTERED, $user->getState());
        $this->assertEquals(User::ROLE_GUEST, $user->getRole());
        $this->assertEquals('username', $user->getUsername());
        $this->assertEquals('test@eCamp3.ch', $user->getUntrustedMailAddress());

    }
}

```


## UnitTests mit Datenbank

Für UnitTests mit Datenbank wird die Klasse 
```\eCamp\LibTest\PHPUnit\AbstractDatabaseTestCase``` verwendet.

Dabei dient die statische Klasse ```\eCampApp``` als Container für die 
```\Laminas\Mvc\Application```. So können ```Services``` oder 
```Repositories``` geladen werden.

Beispiel:
```php
<?php

namespace eCamp\CoreTest\Service;

use eCamp\Core\Entity\User;
use eCamp\Core\Service\UserService;
use eCamp\LibTest\PHPUnit\AbstractDatabaseTestCase;

class UserServiceTest extends AbstractDatabaseTestCase
{

    public function testCreateUser() {
        /** @var UserService $userService */
        $userService = \eCampApp::GetService(UserService::class);

        $user = $userService->create((object)[
            'username' => 'username',
            'mailAddress' => 'test@eCamp3.ch'
        ]);

        $this->assertEquals(User::STATE_NONREGISTERED, $user->getState());

    }
}
```



## UnitTests mit Http-Requests

Für UnitTests mit HTTP-Request wird die Klasse 
```\eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase``` verwendet.

Mit ```$this->dispatch( ~URL~ )``` kann ein HTTP-Request abgesetzt werden.
Unter ```$this->getRequest()``` und ```$this->getResponse()``` kann Request 
und Response geladen, manipuliert und überprüft werden. 

Beispiel:
```php
<?php

namespace eCamp\ApiTest;

use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

class OrganizationApiTest extends AbstractApiControllerTestCase
{

    public function testOrganizationFetch() {

        $this->dispatch("/api/organization");
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();

    }
}

```