<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminSessionController extends Controller
{
    public function index()
    {
        $driver = config('session.driver');

        $sessions = [];

        if ($driver === 'file') {
            $path = storage_path('framework/sessions');
            $files = File::files($path);

            foreach ($files as $file) {
                try {
                    $contents = File::get($file->getPathname());
                    // Attempt to unserialize PHP session payload
                    // Laravel stores serialized array format for session files
                    $data = @unserialize($contents);
                    // Some session files are serialized with session_encode format
                    if ($data === false) {
                        // Try to parse 'key|serialized' pairs
                        $data = $this->parsePhpSession($contents);
                    }

                    $userId = null;
                    if (is_array($data)) {
                        foreach ($data as $k => $v) {
                            if (strpos($k, 'login_web_') === 0) {
                                $userId = (int) $v;
                                break;
                            }
                        }
                    }

                    $user = $userId ? User::find($userId) : null;

                    $sessions[] = [
                        'id' => $file->getFilename(),
                        'user_id' => $userId,
                        'user' => $user,
                        'last_activity' => date('Y-m-d H:i:s', $file->getMTime()),
                        'size' => $file->getSize(),
                        'driver' => 'file',
                    ];
                } catch (\Throwable $e) {
                    // ignore files we cannot parse
                }
            }
        } elseif ($driver === 'database' || $driver === 'db') {
            // sessions table (Laravel default migration) stores payload and last_activity
            $rows = DB::table(config('session.table', 'sessions'))->get();

            foreach ($rows as $row) {
                $payload = $row->payload ?? null;
                $userId = null;

                if ($payload) {
                    // payload is base64-encoded serialized data when using database driver
                    try {
                        $decoded = base64_decode($payload, true);
                        if ($decoded !== false) {
                            $data = @unserialize($decoded);
                            if ($data === false && is_string($decoded)) {
                                // payload may contain serialized php session data; attempt parse
                                $data = $this->parsePhpSession($decoded);
                            }
                        } else {
                            $data = [];
                        }
                    } catch (\Throwable $e) {
                        $data = [];
                    }

                    if (is_array($data)) {
                        foreach ($data as $k => $v) {
                            // Laravel stores login key as login_web_{id} => id
                            if (strpos($k, 'login_web_') === 0) {
                                $userId = (int) $v;
                                break;
                            }
                            // Some apps store user.id or user_id in session
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
                }

                $user = $userId ? User::find($userId) : null;

                $sessions[] = [
                    'id' => $row->id,
                    'user_id' => $userId,
                    'user' => $user,
                    'last_activity' => $row->last_activity ? date('Y-m-d H:i:s', $row->last_activity) : null,
                    'size' => isset($row->payload) ? strlen($row->payload) : null,
                    'driver' => 'database',
                ];
            }
        } else {
            // Other drivers (redis, cookie) are not supported yet
        }

        return view('admin.sessions.index', compact('sessions', 'driver'));
    }

    public function destroy(Request $request, $sessionId)
    {
        $driver = config('session.driver');

        if ($driver === 'file') {
            $path = storage_path('framework/sessions') . DIRECTORY_SEPARATOR . $sessionId;
            if (File::exists($path)) {
                File::delete($path);
                return redirect()->back()->with('success', 'Session terminated successfully.');
            }

            return redirect()->back()->with('error', 'Session not found.');
        }

        if ($driver === 'database' || $driver === 'db') {
            $deleted = DB::table(config('session.table', 'sessions'))->where('id', $sessionId)->delete();
            if ($deleted) {
                return redirect()->back()->with('success', 'Session terminated successfully.');
            }

            return redirect()->back()->with('error', 'Session not found.');
        }

        return redirect()->back()->with('error', 'Session driver not supported for manual termination.');
    }

    /**
     * Parse PHP session file content into an associative array.
     */
    private function parsePhpSession(string $contents): array
    {
        $result = [];
        $offset = 0;
        while ($offset < strlen($contents)) {
            if (!strstr(substr($contents, $offset), "|")) {
                break;
            }
            $pos = strpos($contents, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($contents, $offset, $num);
            $offset += $num + 1;
            $data = unserialize(substr($contents, $offset));
            $result[$varname] = $data;
            $offset += strlen(serialize($data));
        }

        return $result;
    }
}
