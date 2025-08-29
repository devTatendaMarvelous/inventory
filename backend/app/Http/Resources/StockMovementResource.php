<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
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
            'product' => $this->whenLoaded('product', fn() => new ProductResource($this->product)),
            'warehouse' => $this->whenLoaded('warehouse', fn() => $this->warehouse ? new WarehouseResource($this->warehouse) : null),
            'source' => $this->whenLoaded('source', fn() => $this->source ? new WarehouseResource($this->source) : null),
            'movement_type' => $this->movement_type,
            'quantity_in' => $this->quantity_in,
            'quantity_out' => $this->quantity_out,
            'unit_price' => $this->unit_price,
            'status' => $this->status,
            'notes' => $this->notes,
            'initiated_by' => $this->initiated_by,
            'validated_by' => $this->validated_by,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}
