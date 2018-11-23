<?php

namespace app\metrics;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;

class Index
{
    function get()
    {
        $registry = CollectorRegistry::getDefault();
        $renderer = new RenderTextFormat();
        $result = $renderer->render($registry->getMetricFamilySamples());
        header('Content-type:' . RenderTextFormat::MIME_TYPE);
        echo $result;
    }
}
