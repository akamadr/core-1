<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Request;
use \TypeRocket\Http\Response;
use \TypeRocket\Register\Registry;
use TypeRocket\Utility\Str;

class TaxonomiesResponder extends Responder
{

    public $taxonomy = null;

    /**
     * Respond to posts hook
     *
     * Detect the post types registered resource and run the Kernel
     * against that resource.
     *
     * @param $args
     */
    public function respond( $args )
    {
        $taxonomy   = $this->taxonomy;
        $resource   = Registry::getTaxonomyResource( $taxonomy );
        $prefix     = Str::camelize( $resource[0] );
        $controller = $resource[3] ?? tr_app("Controllers\\{$prefix}Controller");
        $model      = $resource[2] ?? tr_app("Models\\{$prefix}");
        $resource = $resource[0];

        if ( empty($prefix) || ! class_exists( $controller ) || ! class_exists( $model )) {
            $resource = 'category';
        }

        $request  = new Request( $resource, 'PUT', $args, 'update', $this->hook, $controller );
        $response = new Response();
        $response->blockFlash();

        $this->runKernel($request, $response);
    }

}