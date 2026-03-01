<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Notifications\ForumReplyNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Admin Forum Moderation Controller
 * Section 5 – Admin Flow: Forum Moderation
 *
 * Handles:
 *  - Viewing all threads (flagged, open, closed)
 *  - Approving / flagging / closing / pinning threads
 *  - Soft-deleting threads and spam replies
 *  - Managing forum categories
 */
class AdminForumController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // Threads
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Dashboard / overview: recent flagged + total counts.
     */
    public function index(): View
    {
        $stats = [
            'total'   => ForumThread::count(),
            'open'    => ForumThread::where('status', 'open')->count(),
            'closed'  => ForumThread::where('status', 'closed')->count(),
            'flagged' => ForumThread::where('status', 'flagged')->count(),
        ];

        $flagged = ForumThread::with(['user', 'category'])
            ->where('status', 'flagged')
            ->latest()
            ->take(20)
            ->get();

        return view('admin.forum.index', compact('stats', 'flagged'));
    }

    /**
     * Paginated list of all threads with filter by status/category.
     */
    public function threads(Request $request): View
    {
        $query = ForumThread::with(['user', 'category'])
            ->withCount('replies');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('body', 'like', $search);
            });
        }

        $threads    = $query->latest()->paginate(20)->withQueryString();
        $categories = ForumCategory::orderBy('name')->get();

        return view('admin.forum.threads', compact('threads', 'categories'));
    }

    /**
     * Show single thread with all replies for admin review.
     */
    public function showThread(int $id): View
    {
        $thread = ForumThread::with([
            'user',
            'category',
            'replies.user',
            'replies.expertResponse.expert.user',
        ])->findOrFail($id);

        return view('admin.forum.thread-show', compact('thread'));
    }

    /**
     * Moderate a thread: change status (open | closed | flagged).
     * Optionally pin/unpin.
     *
     * PUT /admin/forum/threads/{id}/moderate
     * body: { action: approve|remove|flag|close, notes? }
     */
    public function moderateThread(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:approve,remove,flag,close',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $thread = ForumThread::findOrFail($id);

        $statusMap = [
            'approve' => 'open',
            'remove'  => 'closed',
            'flag'    => 'flagged',
            'close'   => 'closed',
        ];

        $thread->update([
            'status'    => $statusMap[$request->action],
            'is_pinned' => $request->action === 'approve' ? $thread->is_pinned : false,
        ]);

        return redirect()
            ->route('admin.forum.threads')
            ->with('success', "Thread #{$thread->id} has been {$request->action}d.");
    }

    /**
     * Soft-delete (or hard-delete) a thread.
     * After deletion, if the thread had an owner, optionally notify.
     */
    public function destroyThread(int $id): RedirectResponse
    {
        $thread = ForumThread::findOrFail($id);
        $thread->update(['status' => 'closed']);
        $thread->delete(); // SoftDelete

        return redirect()
            ->route('admin.forum.threads')
            ->with('success', 'Thread removed successfully.');
    }

    /**
     * Toggle pin status on a thread.
     * POST /admin/forum/threads/{id}/pin
     */
    public function pinThread(int $id): RedirectResponse
    {
        $thread = ForumThread::findOrFail($id);
        $thread->update(['is_pinned' => ! $thread->is_pinned]);

        $msg = $thread->is_pinned ? 'Thread pinned.' : 'Thread unpinned.';

        return back()->with('success', $msg);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Replies
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Approve a pending reply (flip is_approved flag).
     * PUT /admin/forum/replies/{id}/approve
     */
    public function approveReply(int $id): RedirectResponse
    {
        $reply = ForumReply::findOrFail($id);
        $reply->update(['is_approved' => true]);

        // Notify thread owner that a reply was approved and is now visible
        $thread = $reply->thread;
        if ($thread && $thread->user && $thread->user_id !== $reply->user_id) {
            try {
                $thread->user->notify(new ForumReplyNotification($reply, $thread));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning(
                    'Forum reply notification failed: ' . $e->getMessage()
                );
            }
        }

        return back()->with('success', 'Reply approved and visible to users.');
    }

    /**
     * Hard-delete a spam reply.
     * DELETE /admin/forum/replies/{id}
     */
    public function destroyReply(int $id): RedirectResponse
    {
        $reply = ForumReply::findOrFail($id);
        $reply->delete();

        return back()->with('success', 'Reply deleted.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Categories
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * List forum categories with thread counts.
     */
    public function categories(): View
    {
        $categories = ForumCategory::withCount('threads')->orderBy('sort_order')->get();

        return view('admin.forum.categories', compact('categories'));
    }

    /**
     * Create a new forum category.
     * POST /admin/forum/categories
     */
    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:forum_categories,name',
            'description' => 'nullable|string|max:500',
            'icon'        => 'nullable|string|max:50',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);

        ForumCategory::create($data);

        return redirect()
            ->route('admin.forum.categories.index')
            ->with('success', 'Category created.');
    }

    /**
     * Update an existing forum category.
     * PUT /admin/forum/categories/{id}
     */
    public function updateCategory(Request $request, int $id): RedirectResponse
    {
        $category = ForumCategory::findOrFail($id);

        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:forum_categories,name,' . $id,
            'description' => 'nullable|string|max:500',
            'icon'        => 'nullable|string|max:50',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        $category->update($data);

        return redirect()
            ->route('admin.forum.categories.index')
            ->with('success', 'Category updated.');
    }

    /**
     * Delete a forum category (only if no threads attached).
     * DELETE /admin/forum/categories/{id}
     */
    public function destroyCategory(int $id): RedirectResponse
    {
        $category = ForumCategory::withCount('threads')->findOrFail($id);

        if ($category->threads_count > 0) {
            return back()->withErrors([
                'category' => "Cannot delete: {$category->threads_count} thread(s) are assigned to this category.",
            ]);
        }

        $category->delete();

        return redirect()
            ->route('admin.forum.categories.index')
            ->with('success', 'Category deleted.');
    }
}
