<?php

namespace App\Component;

use Phalcon\Di\Injectable;
use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;

class MyHelper extends Injectable
{

    /**
     * getConroller function
     * It gives all the controllers present
     * @return void
     */
    public function getController()
    {
        $controllers = [];
        foreach (glob(APP_PATH . '/controllers/*Controller.php') as $controller) {
            $className = basename($controller, '.php');
            $controllers[$className] = [];
        }
        $eventsManager = $this->di->get('EventsManager');
        $data=$eventsManager->fire('notifications:restrictControllers', $this, $controllers);
        return $data;
    }


    /**
     * getMethods function
     * It guves all the action present
     * @param [type] $controller
     * @return void
     */
    public function getMethods($controller)
    {
        $ActionMethod = [];
        $controller = APP_PATH . '/controllers/' .$controller.'.php';

        $className = basename($controller, '.php');
        $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (\Phalcon\Text::endsWith($method->name, 'Action')) {
                $ActionMethod[] = substr($method->name, 0, -6);
            }
        }
        return $ActionMethod;
    }

    public function getTranslator(): NativeArray
    {
        $language=$_GET['locale']??'english';
        $translationFile = '../app/messages/' . $language . '.php';

        if (true !== file_exists($translationFile)) {
            $translationFile = '../app/messages/english.php';
        }
        
        require $translationFile;
        

        $interpolator = new InterpolatorFactory();
        $factory      = new TranslateFactory($interpolator);
        
        return $factory->newInstance(
            'array',
            [
                'content' => $messages,
            ]
        );
    }
}
