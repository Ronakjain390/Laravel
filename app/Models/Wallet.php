<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function credit($amount)
    {
        $this->balance += $amount;
        $this->save();

        $this->transactions()->create([
            'user_id' => $this->user_id,
            'amount' => $amount,
            'type' => 'credit',
        ]);
    }

    public function debit($amount)
    {
        if ($this->balance >= $amount) {
            $this->balance -= $amount;
            $this->save();

            $this->transactions()->create([
                'user_id' => $this->user_id,
                'amount' => $amount,
                'type' => 'debit',
            ]);
        } else {
            throw new \Exception('Insufficient balance');
        }
    }
}
