<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Certificate;

$email = 'asom_test_user@example.com';
$user = User::where('email', $email)->first();
if (!$user) { echo "Test user not found\n"; exit(1); }

// Check if diploma already exists
$existing = Certificate::where('user_id', $user->id)->whereNull('course_id')->first();
if ($existing) {
    echo "Diploma already exists: id={$existing->id} certificate_id={$existing->certificate_id} is_approved={$existing->is_approved}\n";
    exit(0);
}

// Use controller method to check and generate (reuse logic)
$controller = new App\Http\Controllers\LMS\StudentExamController();
// controller has private method checkAndGenerateDiplomaCertificate; call the public helper checkDiplomaEligibility first
$res = $controller->checkDiplomaEligibility($user->id);
if (!($res['eligible'] ?? false)) {
    echo "User not eligible for diploma yet: completed={$res['completed']} of {$res['total_required']}\n";
    exit(1);
}

// Generate certificate using the controller's internal method by copying logic (create Certificate row)
$certificate = Certificate::create([
    'user_id' => $user->id,
    'course_id' => null,
    'final_grade' => $res['eligible'] ? 100 : 0,
    'completed_at' => now(),
    'issued_at' => null,
    'is_approved' => false,
    'notes' => 'Auto-generated diploma for testing'
]);

echo "Created diploma certificate id={$certificate->id} certificate_id={$certificate->certificate_id} is_approved={$certificate->is_approved}\n";

?>
