### Schnell

Schnell (meaning 'fast' in German) is the PHP micro framework that use most of the Slim framework components and Doctrine ORM as the default persistence manager.

#### Run locally

```bash
php -S localhost:8080 -t ./
```

#### Creating a controller

All declared controller must be:
- must be under ```Schnell\Api\Controller``` controller
- extends ```Schnell\Controller\AbstractController```
- have ```$request```, ```$response```, and ```$args``` in route invokable method, where each of that are:
  - ```$request``` must be an instance of ```Psr\Http\Message\RequestInterface```
  - ```$response``` must be an instance of ```Psr\Http\Message\ResponseInterface```
  - ```$args``` must be an array
- route invokable method must be annotated with ```Schnell\Attribute\Route``` class
- route invokable method must return ```Psr\Http\Message\ResponseInterface```

Here is an example:

```php
<?php

declare(strict_types=1);

namespace Schnell\Api\Controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Schnell\Attribute\Route;
use Schnell\Controller\AbstractController;

class FooController extends AbstractController
{
    #[Route('/foo', method: 'GET')]
    public function invokeFoo(
        Request $request,
        Response $response,
        array $args
    ): ResponseInterface {
        $response->getBody()->write('Hello, World!');
        return $response->withHeader('Content-Type', 'text/plain');
    }
}
```

#### Creating an entity

All declared entity must be:
- must extend ```Schnell\Entity\AbstractEntity```
- class entity property must be annotated with anything under namespace ```Doctrine\ORM\Mapping```

Here is an example:

```php
<?php

declare(strict_types=1);

namespace Schnell\Api\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Schnell\Entity\AbstractEntity;

#[Entity, Table(name: 'users')]
class User extends AbstractEntity
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private $id;

    #[Column(type: 'string', nullable: false)]
    private $name;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(); string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
```

#### Persisting that table metadata into database

```bash
cd bin
./doctrine orm:schema-tool:create
```

#### Removing synced table metadata from database

```bash
cd bin
./doctrine orm:schema-tool:drop --force
```
