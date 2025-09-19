<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminUserController;

$user = User::first();
if (!$user) {
    echo "No users found to test.\n";
    exit(1);
}

$controller = new AdminUserController();
$request = Request::create('/admin/users/'.$user->id.'/verify', 'PATCH', []);
// Simulate AJAX/JSON
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('Accept', 'application/json');

$response = $controller->verify($request, $user);

if ($response instanceof Illuminate\Http\JsonResponse) {
    echo "JSON response: ";
    print_r($response->getData(true));
} else {
    echo "Non-JSON response received.\n";
}
