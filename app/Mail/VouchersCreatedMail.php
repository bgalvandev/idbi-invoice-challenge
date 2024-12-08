<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VouchersCreatedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public array $successfulVouchers;
    public array $failedVouchers;
    public User $user;

    public function __construct(array $successfulVouchers, array $failedVouchers, User $user)
    {
        $this->successfulVouchers = $successfulVouchers;
        $this->failedVouchers = $failedVouchers;
        $this->user = $user;
    }

    public function build(): self
    {
        return $this->view('emails.vouchers')
            ->subject('Resumen de Procesamiento de Comprobantes')
            ->with(['successfulVouchers' => $this->successfulVouchers, 'failedVouchers' => $this->failedVouchers, 'user' => $this->user]);
    }
}
