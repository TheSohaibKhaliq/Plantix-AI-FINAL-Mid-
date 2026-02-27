<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expert\PostExpertReplyRequest;
use App\Models\ForumThread;
use App\Services\Expert\ExpertForumService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ExpertForumController
 *
 * Allows experts to browse forum threads and post expert-tagged replies.
 * Expert replies are visually distinguished in the frontend via is_expert_reply flag.
 */
class ExpertForumController extends Controller
{
    public function __construct(
        private readonly ExpertForumService $service
    ) {}

    private function currentExpert(): \App\Models\Expert
    {
        return auth('expert')->user()->expert;
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['category_id', 'search']);
        $threads = $this->service->listThreads($filters);
        $myReplies = $this->service->getExpertReplies($this->currentExpert());

        return view('expert.forum.index', compact('threads', 'myReplies', 'filters'));
    }

    public function show(ForumThread $thread): View
    {
        abort_unless($thread->is_approved, 404);

        $thread->incrementViews();
        $thread->load(['user', 'category', 'replies' => function ($q) {
            $q->with(['user', 'expert.user', 'expertResponse'])->orderBy('created_at', 'asc');
        }]);

        return view('expert.forum.show', compact('thread'));
    }

    public function reply(PostExpertReplyRequest $request, ForumThread $thread): RedirectResponse
    {
        try {
            $this->service->postExpertReply(
                $this->currentExpert(),
                $thread,
                $request->input('body'),
                $request->input('recommendation')
            );

            return redirect()->route('expert.forum.show', $thread)
                ->with('success', 'Your expert reply has been posted.');
        } catch (\DomainException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
