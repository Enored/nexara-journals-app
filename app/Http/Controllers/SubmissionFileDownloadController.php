<?php

namespace App\Http\Controllers;

use App\Models\SubmissionFile;
use App\Support\SubmissionFileAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionFileDownloadController extends Controller
{
    public function show(Request $request, SubmissionFile $file): StreamedResponse
    {
        $file->load('submission.journal');

        abort_unless(SubmissionFileAccess::canDownload($request->user(), $file), 403);

        $disk = Storage::disk(SubmissionFile::DISK);

        abort_unless($disk->exists($file->storage_path), 404);

        return $disk->download($file->storage_path, $file->original_name);
    }
}
