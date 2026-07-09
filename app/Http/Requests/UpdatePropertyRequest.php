<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'type_id'          => 'required|exists:property_types,id',
            'operation_id'     => 'required|exists:property_operations,id',
            'status_id'        => 'required|exists:property_statuses,id',
            'price'            => 'required|numeric|min:0',
            'surface'          => 'nullable|integer|min:1',
            'rooms'            => 'nullable|integer|min:0',
            'bathrooms'        => 'nullable|integer|min:0',
            'address'          => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:100',
            'locality'         => 'nullable|string|max:100',
            'province'         => 'nullable|string|max:100',
            'country'          => 'nullable|string|max:100',
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
            'main_image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'secondary_images'   => 'nullable|array|max:10',
            'secondary_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'floor_plans'        => 'nullable|array|max:5',
            'floor_plans.*'      => ['image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'reference_mode'   => 'required|in:auto,prefix,manual',
            'reference_prefix' => 'nullable|string|max:20|alpha',
            'reference_manual' => ['nullable', 'string', 'max:30', 'unique:properties,reference,' . $this->property->id],
        ];
    }
}
