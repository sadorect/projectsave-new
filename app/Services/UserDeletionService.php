<?php

namespace App\Services;

use App\Models\User;
use App\Models\DeletionRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class UserDeletionService
{
    public function deleteUser(User $user, DeletionRequest $deletionRequest, int $processedBy, ?string $notes = null): void
    {
        DB::transaction(function () use ($user, $deletionRequest, $processedBy, $notes) {
            foreach ($user->files()->get() as $file) {
                if (Storage::disk('private')->exists($file->path)) {
                    Storage::disk('private')->delete($file->path);
                }
            }

            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->partnerships()->delete();
            $user->activities()->delete();
            $user->files()->delete();
            $user->notifications()->delete();

            if (Schema::hasTable('personal_access_tokens')) {
                $user->tokens()->delete();
            }

            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->where('user_id', $user->getKey())->delete();
            }

            if (Schema::hasTable('model_has_roles')) {
                DB::table('model_has_roles')
                    ->where('model_type', $user::class)
                    ->where('model_id', $user->getKey())
                    ->delete();
            }

            if (Schema::hasTable('model_has_permissions')) {
                DB::table('model_has_permissions')
                    ->where('model_type', $user::class)
                    ->where('model_id', $user->getKey())
                    ->delete();
            }

            $deletionRequest->forceFill([
                'user_id' => null,
                'status' => 'completed',
                'processed_by' => $processedBy,
                'processed_notes' => $notes,
                'processed_at' => now(),
            ])->save();

            $user->delete();
        });
    }
}
