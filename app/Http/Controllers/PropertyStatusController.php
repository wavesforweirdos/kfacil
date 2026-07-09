<?php

namespace App\Http\Controllers;

use App\Models\PropertyStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PropertyStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage_property_statuses');
    }

    public function index()
    {
        $statuses = PropertyStatus::orderBy('sort_order')->paginate(10);
        return view('property-statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('property-statuses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:property_statuses,name',
            'label' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        PropertyStatus::create($data);

        return redirect()->route('property-statuses.index')
            ->with('success', __('messages.create_success', ['model' => 'Estado de propiedad']));
    }

    public function edit(PropertyStatus $propertyStatus)
    {
        return view('property-statuses.edit', compact('propertyStatus'));
    }

    public function update(Request $request, PropertyStatus $propertyStatus)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:property_statuses,name,' . $propertyStatus->id,
            'label' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $propertyStatus->update($data);

        return redirect()->route('property-statuses.index')
            ->with('success', __('messages.update_success', ['model' => 'Estado de propiedad']));
    }

    public function destroy(PropertyStatus $propertyStatus)
    {
        $count = $propertyStatus->properties()->count();

        if ($count > 0) {
            throw ValidationException::withMessages([
                'name' => "No se puede eliminar este estado de propiedad porque está en uso en $count propiedad(es)."
            ]);
        }

        $propertyStatus->delete();

        return redirect()->route('property-statuses.index')
            ->with('success', __('messages.delete_success', ['model' => 'Estado de propiedad']));
    }
}
