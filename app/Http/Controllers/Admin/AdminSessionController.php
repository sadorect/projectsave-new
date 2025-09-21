<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminSessionController extends Controller
{
    public function index(Request $request)
    {
        $driver = config('session.driver');

    $sessions = [];
    $includeGuests = $request->boolean('show_guests');
    $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

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

                    // Include sessions only when they belong to authenticated users,
                    // unless the admin toggles "show guests".
                    if (($userId && $user) || $includeGuests) {
                        $sessions[] = [
                            'id' => $file->getFilename(),
                            'user_id' => $userId,
                            'user' => $user,
                            'last_activity' => date('Y-m-d H:i:s', $file->getMTime()),
                            'size' => $file->getSize(),
                            'driver' => 'file',
                        ];
                    }
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

                // Include sessions only when they belong to authenticated users,
                // unless the admin toggles "show guests".
                if (($userId && $user) || $includeGuests) {
                    $sessions[] = [
                        'id' => $row->id,
                        'user_id' => $userId,
                        'user' => $user,
                        'last_activity' => $row->last_activity ? date('Y-m-d H:i:s', $row->last_activity) : null,
                        'size' => isset($row->payload) ? strlen($row->payload) : null,
                        'driver' => 'database',
                    ];
                }
            }
        } else {
            // Other drivers (redis, cookie) are not supported yet
        }

        // Sort by last_activity
        usort($sessions, function ($a, $b) use ($direction) {
            $at = isset($a['last_activity']) ? strtotime($a['last_activity']) : 0;
            $bt = isset($b['last_activity']) ? strtotime($b['last_activity']) : 0;
            if ($at === $bt) return 0;
            return $direction === 'asc' ? ($at <=> $bt) : ($bt <=> $at);
        });

        // Paginate results (25 per page)
        $perPage = 25;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $total = count($sessions);
        $items = array_slice($sessions, ($page - 1) * $perPage, $perPage);
        $paginator = new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return view('admin.sessions.index', ['sessions' => $paginator, 'driver' => $driver]);
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
