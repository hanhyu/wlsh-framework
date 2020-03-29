<?php declare(strict_types=1);

namespace App\Library;

use RuntimeException;

class Proxy
{
    private array $obj_arr = [];

    public function __construct($obj)
    {
        $this->obj_arr[] = new $obj();
    }

    public function __call(string $method_name, array $args)
    {
        foreach ($this->obj_arr as $obj) {
            $class = new \ReflectionClass($obj);
            if (($method = $class->getMethod($method_name)) && $method->isPublic() && !$method->isAbstract()) {
                $method->invoke($obj, $args);
            }
        }
        //$data     = call_user_func_array([$this, $method], $args);
    }

}
