<?php
    
    namespace App\Mail;
    
    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Mail\Mailable;
    use Illuminate\Queue\SerializesModels;
    use Psy\Util\Str;
    
    class CronTab extends Mailable
    {
        use Queueable, SerializesModels;
        
        public $name;
        public $title;
        
        /**
         * Create a new message instance.
         *
         * @param string $name
         */
        public function __construct (string $name,string $title)
        {
            //
            $this->name = $name;
            $this->title = $title;
            
        }
        
        /**
         * Build the message.
         *
         * @return $this
         */
        public function build ()
        {
            return $this->view('emails.message')
                ->subject('DAC系統排程通知');
        }
    }
