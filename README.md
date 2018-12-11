# Documentation Router for Laravel

Allow routing on controller class and method documentation

To install on laravel root directory terminal type
**composer require grimbees/docrouter** 

Supported annotation on controller class:  
**@middleware values**, values would be any middleware that is registered  
**@prefix values**, values would be any url escaped string

Supported annotation on controller method:  
**@method values**, values would be **any, post, get**  
**@route values**, values would be any url escaped string

To get started:  
Add **GrimBees\DocRouter\DocRouterServiceProvider::class** to **config/app.php** on **$providers**


Example: **MyController.php**

```php
namespace App\Http\Controllers;

/**
 * Class MyController
 * @middleware web
 * @prefix my
 */
class MyController extends Controller
{

    /**
     * @method any
     * @route test
     */
    public function test() {
        return "test";
    }

}
```
Type **composer dump-autoload** is required for new controller

Test controller method will be exposed on _/my/test_
