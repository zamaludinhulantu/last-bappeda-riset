<?php

namespace App\Mail;

use App\Models\Research;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResearchRecommendationReady extends Mailable
{
    use Queueable, SerializesModels;

    public Research $research;

    /**
    * Create a new message instance.
    */
    public function __construct(Research $research)
    {
        $this->research = $research;
    }

    /**
    * Build the message.
    */
    public function build(): self
    {
        $title = $this->research->judul ?? 'Penelitian';
        $subject = 'Surat rekomendasi tersedia - ' . $title;

        return $this->subject($subject)
            ->view('emails.researches.recommendation_ready');
    }
}
