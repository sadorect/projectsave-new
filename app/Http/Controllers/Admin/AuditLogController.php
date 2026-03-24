<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminAuditLog;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\AppSetting;


class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-audit-log,manage-audit-log,admin')->only('index');
        $this->middleware('permission:manage-audit-log,admin')->only(['destroy', 'bulkDestroy', 'toggleErrorAudit']);
    }

    public function index(Request $request)
    {
        $baseQuery = AdminAuditLog::query();
        $query = AdminAuditLog::query()->with('adminUser')->orderByDesc('created_at');

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

        $actions = AdminAuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action')
            ->toArray();

        $logs = $query->paginate(25);
        $summary = [
            'total_logs' => (clone $baseQuery)->count(),
            'today_logs' => (clone $baseQuery)->whereDate('created_at', today())->count(),
            'unique_admins' => (clone $baseQuery)->whereNotNull('admin_user_id')->distinct()->count('admin_user_id'),
            'destructive_actions' => (clone $baseQuery)
                ->where(function ($builder) {
                    $builder
                        ->where('action', 'like', '%delete%')
                        ->orWhere('action', 'like', '%destroy%')
                        ->orWhere('action', 'like', '%reject%')
                        ->orWhere('action', 'like', '%remove%');
                })
                ->count(),
        ];

        $errorAuditEnabled = AppSetting::get('error_audit_enabled', ['enabled' => false]);

        return view('admin.audit.index', compact('logs', 'actions', 'errorAuditEnabled', 'summary'));
    }

    /**
     * Delete a single audit log entry
     */
    public function destroy($id)
    {
        $log = AdminAuditLog::findOrFail($id);
        $log->delete();

        return redirect()->back()->with('success', 'Audit log deleted.');
    }

    /**
     * Bulk delete audit logs
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'No items selected.');
        }

        AdminAuditLog::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', 'Selected audit logs deleted.');
    }

    /**
     * Toggle error audit on/off
     */
    public function toggleErrorAudit(Request $request)
    {
        $enabled = $request->boolean('enabled');

        AppSetting::set('error_audit_enabled', ['enabled' => $enabled]);

        return redirect()->back()->with('success', 'Error audit setting updated.');
    }
}
