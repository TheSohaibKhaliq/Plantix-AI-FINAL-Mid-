<?php

namespace App\Http\Controllers\Api;

use App\Models\VendorDocument;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class VendorDocumentController extends Controller
{
    public function index($vendorId): JsonResponse
    {
        $documents = VendorDocument::where('vendor_id', $vendorId)->get();
        return response()->json($documents);
    }

    public function store(Request $request, $vendorId): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx',
            'document_type' => 'required|string',
            'document_name' => 'required|string'
        ]);

        try {
            $file = $request->file('file');
            $filePath = Storage::disk('public')->put(
                "vendor-documents/{$vendorId}",
                $file
            );

            $document = VendorDocument::create([
                'vendor_id' => $vendorId,
                'document_name' => $request->document_name,
                'document_type' => $request->document_type,
                'document_url' => Storage::url($filePath),
                'file_path' => $filePath,
                'status' => 'pending'
            ]);

            return response()->json($document, 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'File upload failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, VendorDocument $document): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string'
        ]);

        $document->update($validated);
        
        return response()->json($document);
    }

    public function destroy(VendorDocument $document): JsonResponse
    {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();
        return response()->json(null, 204);
    }

    public function download(VendorDocument $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return response()->json(['error' => 'File not found'], 404);
        }
        
        return Storage::disk('public')->download($document->file_path);
    }
}
