<?php

namespace App\Http\Controllers;

use App\Models\StudentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function show(StudentDocument $document)
    {
        // Simple Authorization: Only authenticated users (Admins/Staff) can view documents.
        // In a real app, strict policies (e.g. only specific Staff for specific Unit) should apply.
        // Since we have RBAC, let's allow any staff with permission.

        $user = auth()->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Optional: Check specific permission
        // if (!$user->can('view_student_documents')) { abort(403); }

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($document->file_path));
    }
}
