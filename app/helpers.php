<?php

if (!function_exists('routeHas')) {
    function routeHas($name) {
        return Route::has($name);
    }
}
