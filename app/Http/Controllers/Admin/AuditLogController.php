<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminAuditLog;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // Restrict access to admins only for now
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403);
        }

        $query = AdminAuditLog::query()->with('adminUser')->orderBy('created_at', 'desc');

        // Date range filters
        if ($from = $request->get('from')) {
            $query->where('created_at', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $query->where('created_at', '<=', $to);
        }

        // Action filter
        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        if ($admin = $request->get('admin_user_id')) {
            $query->where('admin_user_id', $admin);
        }

        // If export=csv requested, stream CSV response
        if ($request->get('export') === 'csv') {
            $filename = 'audit_logs_' . now()->format('Ymd_His') . '.csv';

            $response = new StreamedResponse(function() use ($query) {
                $handle = fopen('php://output', 'w');
                // header row
                fputcsv($handle, ['id','created_at','admin_user_id','admin_user_name','action','target_type','target_id','meta','ip_address']);

                $query->chunk(200, function($rows) use ($handle) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            $row->id,
                            $row->created_at,
                            $row->admin_user_id,
                            $row->adminUser?->name ?? '',
                            $row->action,
                            $row->target_type,
                            $row->target_id,
                            json_encode($row->meta),
                            $row->ip_address,
                        ]);
                    }
                });

                fclose($handle);
            });

            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

            return $response;
        }

        // For the action dropdown, fetch distinct actions
        $actions = AdminAuditLog::select('action')->distinct()->pluck('action')->toArray();

        $logs = $query->paginate(25);

        return view('admin.audit.index', compact('logs', 'actions'));
    }
}
