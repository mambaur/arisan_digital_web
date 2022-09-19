<?php

namespace App\Mail;

use App\Models\Group;
use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Remainder extends Mailable
{
    use Queueable, SerializesModels;

    private Group $group;
    private Member $member;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Group $group, Member $member)
    {
        $this->group = $group;
        $this->member = $member;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Tagihan Arisan')->view('mails.remainder', ['group' => $this->group, 'member' => $this->member]);
    }
}
