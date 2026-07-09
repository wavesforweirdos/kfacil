<?php

namespace App\Http\Controllers;

use App\Models\PropertyOperation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PropertyOperationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage_property_operations');
    }

    public function index()
    {
        $operations = PropertyOperation::orderBy('sort_order')->paginate(10);
        return view('property-operations.index', compact('operations'));
    }

    public function create()
    {
        return view('property-operations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:property_operations,name',
            'label' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        PropertyOperation::create($data);

        return redirect()->route('property-operations.index')
            ->with('success', __('messages.create_success', ['model' => 'Tipo de operación']));
    }

    public function edit(PropertyOperation $propertyOperation)
    {
        return view('property-operations.edit', compact('propertyOperation'));
    }

    public function update(Request $request, PropertyOperation $propertyOperation)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:property_operations,name,' . $propertyOperation->id,
            'label' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $propertyOperation->update($data);

        return redirect()->route('property-operations.index')
            ->with('success', __('messages.update_success', ['model' => 'Tipo de operación']));
    }

    public function destroy(PropertyOperation $propertyOperation)
    {
        $count = $propertyOperation->properties()->count();

        if ($count > 0) {
            throw ValidationException::withMessages([
                'name' => "No se puede eliminar este tipo de operación porque está en uso en $count propiedad(es)."
            ]);
        }

        $propertyOperation->delete();

        return redirect()->route('property-operations.index')
            ->with('success', __('messages.delete_success', ['model' => 'Tipo de operación']));
    }
}
