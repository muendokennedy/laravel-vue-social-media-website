<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserFollowed extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $user, public bool $follow = true)
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
        if($this->follow){
            $subject = 'User Followed You';
        } else {
            $subject = 'User Unfollowed You';
        }
        return (new MailMessage)
                    ->subject($subject)
                    ->lineIf($this->follow, ''.$this->user->name. ' followed you.')
                    ->lineIf(!$this->follow, ''.$this->user->name. ' unfollowed you.')
                    ->action('View '.$this->user->name.' profile', url(route('profile.home', $this->user)))
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
