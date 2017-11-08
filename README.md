BugTracker bundle
===============
----------

Allows easy integration between Symfony and BugTracker service


Installation
---------------
1. Install the bundle via composer
> composer require elemento115/bugtracker-bundle

2. Enable the bundle
```php
<?php
// ../app/AppKernel.php
use \Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
    	$bundles = array(
    	    // ...
    		new Elemento115\BugtrackerBundle\BugtrackerBundle(),
        );
    
    	// ...
    
    	return $bundles;
    }
    
    // ...
}

```

3. Configure the bundle
```yaml
bugtracker:
    api_url: 'http://bugtracker.io/api/'
    api_user: 'api'
    api_password: '12345'
    api_version: 'v1'
    registries:
        test:
            environment: 'debug'
            token: '1509355438-59f6efae96b38'
```

----

Log what you want
---------------------------
Your'e ready to go. Any entry registered under "registries" should map a registry of your app created on the BugTracker service.

To send data use the dynamically created services:
```php
<?php

namespace AppBundle\Controller;

use Elemento115\BugtrackerBundle\Services\ApiClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class ExampleController
 * @package AppBundle\Controller
 */
class ExampleController extends Controller
{
    /**
     * @Route("/example")
     */
    public function logAction()
    {
        /** @var ApiClient $bugTracker */
        $bugTracker = $this->get('bugtracker.client.test'); 
        // bugtracker.client.test maps to registries.test of your config.yml
        
        $bugTracker->post([
            'log' => [
                'message' => 'This is a message',
                'level' => 'debug'
            ]
        ]);
    }
}
```