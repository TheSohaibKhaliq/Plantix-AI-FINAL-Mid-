<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;

class AdminEmailTemplatesController extends Controller
{
    /**
     * Get all email templates
     */
    public function index(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 10);
            $search = $request->get('search', '');

            $query = EmailTemplate::query();

            if ($search) {
                $query->where('type', 'like', "%$search%")
                      ->orWhere('subject', 'like', "%$search%");
            }

            $total = $query->count();
            $templates = $query->orderBy('created_at', 'desc')
                              ->skip(($page - 1) * $limit)
                              ->take($limit)
                              ->get()
                              ->map(function ($template) {
                                  return [
                                      'id' => $template->id,
                                      'type' => $template->type,
                                      'subject' => $template->subject,
                                      'message' => substr($template->message, 0, 100) . '...',
                                      'is_send_to_admin' => $template->is_send_to_admin ?? false,
                                      'created_at' => $template->created_at,
                                  ];
                              });

            return response()->json([
                'success' => true,
                'data' => $templates,
                'meta' => [
                    'total' => $total,
                    'page' => $page,
                    'pages' => ceil($total / $limit)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching templates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single template
     */
    public function show($id)
    {
        try {
            $template = EmailTemplate::find($id);
            
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $template->id,
                    'type' => $template->type,
                    'subject' => $template->subject,
                    'message' => $template->message,
                    'is_send_to_admin' => $template->is_send_to_admin ?? false,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create template
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|string|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
                'is_send_to_admin' => 'nullable|boolean',
            ]);

            $template = EmailTemplate::create([
                'type' => $validated['type'],
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'is_send_to_admin' => $validated['is_send_to_admin'] ?? false,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $template->id,
                    'type' => $template->type,
                    'subject' => $template->subject,
                    'message' => $template->message,
                    'is_send_to_admin' => $template->is_send_to_admin,
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update template
     */
    public function update(Request $request, $id)
    {
        try {
            $template = EmailTemplate::find($id);
            
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found'
                ], 404);
            }

            $validated = $request->validate([
                'subject' => 'nullable|string|max:255',
                'message' => 'nullable|string',
                'is_send_to_admin' => 'nullable|boolean',
            ]);

            if (isset($validated['subject'])) {
                $template->subject = $validated['subject'];
            }
            if (isset($validated['message'])) {
                $template->message = $validated['message'];
            }
            if (isset($validated['is_send_to_admin'])) {
                $template->is_send_to_admin = $validated['is_send_to_admin'];
            }

            $template->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $template->id,
                    'type' => $template->type,
                    'subject' => $template->subject,
                    'message' => $template->message,
                    'is_send_to_admin' => $template->is_send_to_admin,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete template
     */
    public function destroy($id)
    {
        try {
            $template = EmailTemplate::find($id);
            
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found'
                ], 404);
            }

            $template->delete();

            return response()->json([
                'success' => true,
                'message' => 'Template deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting template: ' . $e->getMessage()
            ], 500);
        }
    }
}
