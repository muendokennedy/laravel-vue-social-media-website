<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReactionAddedonPostComment extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Reaction $reaction, public Comment $comment, public User $user, public string $userName)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Reaction Added on Comment')
                    ->greeting('Hello ' .$this->user->name.'.')
                    ->line('' .$this->userName. ' has placed a ' . $this->reaction->type . ' on your Comment: ')
                    ->line('"'.$this->comment->comment.'"')
                    ->action('View post', url(route('post.show', $this->comment->post)))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
