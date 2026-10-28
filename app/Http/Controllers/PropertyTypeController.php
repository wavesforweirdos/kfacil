<?php

namespace App\Http\Controllers;

use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PropertyTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage_property_types');
    }

    public function index()
    {
        $types = PropertyType::orderBy('sort_order')->paginate(10);

        return view('property-types.index', compact('types'));
    }

    public function create()
    {
        return view('property-types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:property_types,name',
            'label' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        PropertyType::create($data);

        return redirect()->route('property-types.index')
            ->with('success', __('messages.create_success', ['model' => 'Tipo de propiedad']));
    }

    public function edit(PropertyType $propertyType)
    {
        return view('property-types.edit', compact('propertyType'));
    }

    public function update(Request $request, PropertyType $propertyType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:property_types,name,'.$propertyType->id,
            'label' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $propertyType->update($data);

        return redirect()->route('property-types.index')
            ->with('success', __('messages.update_success', ['model' => 'Tipo de propiedad']));
    }

    public function destroy(PropertyType $propertyType)
    {
        $count = $propertyType->properties()->count();

        if ($count > 0) {
            throw ValidationException::withMessages([
                'name' => "No se puede eliminar este tipo de propiedad porque está en uso en $count propiedad(es).",
            ]);
        }

        $propertyType->delete();

        return redirect()->route('property-types.index')
            ->with('success', __('messages.delete_success', ['model' => 'Tipo de propiedad']));
    }
}
