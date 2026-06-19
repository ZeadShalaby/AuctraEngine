<?php

namespace App\Http\Resources;

use App\Contracts\HasTransactionSummary;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'type' => $this->type,
            'status' => $this->status,
            'description' => $this->description,
            'created_at' => $this->created_at,

            'source' => $this->source instanceof HasTransactionSummary
                ? $this->source->transactionSummary()
                : null,
        ];
    }
}
