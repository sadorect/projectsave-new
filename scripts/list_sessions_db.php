<?php

// One-off script to list the first 5 rows in the sessions table.
// Run: php scripts/list_sessions_db.php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $table = config('session.table', 'sessions');
    $rows = DB::table($table)->limit(5)->get();

    if ($rows->isEmpty()) {
        echo "No rows found in table: {$table}\n";
        exit(0);
    }

    foreach ($rows as $row) {
        // Convert stdClass to array for nicer JSON
        echo json_encode((array) $row) . PHP_EOL;
    }
} catch (Throwable $e) {
    // If sessions table doesn't exist, try file-based sessions as a fallback
    $msg = $e->getMessage();
    if (stripos($msg, 'doesn\'t exist') !== false || stripos($msg, 'Base table or view not found') !== false) {
        echo "Sessions table not found, falling back to file-based sessions...\n";

        $path = __DIR__ . '/../storage/framework/sessions';
        if (!is_dir($path)) {
            echo "No session files directory found at: {$path}\n";
            exit(1);
        }

        $files = array_slice(scandir($path), 2); // strip . and ..
        if (empty($files)) {
            echo "No session files found in: {$path}\n";
            exit(0);
        }

        $count = 0;
        foreach ($files as $file) {
            if ($count >= 5) break;
            $full = $path . DIRECTORY_SEPARATOR . $file;
            if (!is_file($full)) continue;

            $contents = @file_get_contents($full);
            $data = @unserialize($contents);
            if ($data === false) {
                // try to parse php session format
                $data = [];
                $offset = 0;
                while ($offset < strlen($contents)) {
                    if (!strstr(substr($contents, $offset), "|")) break;
                    $pos = strpos($contents, "|", $offset);
                    $num = $pos - $offset;
                    $varname = substr($contents, $offset, $num);
                    $offset += $num + 1;
                    $val = @unserialize(substr($contents, $offset));
                    if ($val === false) break;
                    $data[$varname] = $val;
                    $offset += strlen(serialize($val));
                }
            }

            $userId = null;
            if (is_array($data)) {
                foreach ($data as $k => $v) {
                    if (strpos($k, 'login_web_') === 0) {
                        $userId = (int) $v;
                        break;
                    }
                    if ($k === 'user_id' || $k === 'user.id' || $k === 'user') {
                        if (is_array($v) && isset($v['id'])) {
                            $userId = (int) $v['id'];
                            break;
                        }
                        if (is_numeric($v)) {
                            $userId = (int) $v;
                            break;
                        }
                    }
                }
            }

            $last = date('Y-m-d H:i:s', filemtime($full));
            $size = filesize($full);

            echo json_encode(["id" => $file, "user_id" => $userId, "last_activity" => $last, "size" => $size]) . PHP_EOL;
            $count++;
        }

        exit(0);
    }

    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . PHP_EOL;
    exit(1);
}
