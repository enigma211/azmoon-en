<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Question;
use App\Models\QuestionVote as VoteModel;

class QuestionVote extends Component
{
    public Question $question;
    public $likesCount = 0;
    public $dislikesCount = 0;
    public $userVote = null; // 1 for like, -1 for dislike, null for none

    public function mount(Question $question)
    {
        $this->question = $question;
        $this->updateCounts();
        $this->checkUserVote();
    }

    public function updateCounts()
    {
        $this->likesCount = $this->question->likesCount;
        $this->dislikesCount = $this->question->dislikesCount;
    }

    public function checkUserVote()
    {
        $ip = request()->ip();
        $vote = VoteModel::where('question_id', $this->question->id)
            ->where('ip_address', $ip)
            ->first();

        if ($vote) {
            $this->userVote = $vote->vote_type;
        } else {
            $this->userVote = null;
        }
    }

    public function vote($type)
    {
        // $type should be 1 (like) or -1 (dislike)
        if (!in_array($type, [1, -1])) {
            return;
        }

        $ip = request()->ip();

        // Check if user already voted
        $existingVote = VoteModel::where('question_id', $this->question->id)
            ->where('ip_address', $ip)
            ->first();

        if ($existingVote) {
            // If clicking the same vote type again, we might want to remove the vote
            // But requirement says: "each IP can only vote once per question"
            // Let's allow them to change their vote or remove it if clicked again
            if ($existingVote->vote_type === $type) {
                $existingVote->delete();
                $this->userVote = null;
            } else {
                $existingVote->update(['vote_type' => $type]);
                $this->userVote = $type;
            }
        } else {
            VoteModel::create([
                'question_id' => $this->question->id,
                'ip_address' => $ip,
                'vote_type' => $type,
            ]);
            $this->userVote = $type;
        }

        $this->updateCounts();
    }

    public function render()
    {
        return view('livewire.question-vote');
    }
}
