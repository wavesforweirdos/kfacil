<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\PropertyOperation;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    private const ALLOWED_SORTS = ['reference', 'title', 'type', 'operation', 'price', 'city', 'status', 'created_at'];

    public function index()
    {
        $this->authorize('viewAny', Property::class);

        $request = request();
        $sort = in_array($request->get('sort'), self::ALLOWED_SORTS, true)
            ? $request->get('sort') : 'created_at';
        $dir = $request->get('dir') === 'asc' ? 'asc' : 'desc';

        $properties = Property::query()
            ->select('properties.*')
            ->with(['user', 'propertyType', 'propertyOperation', 'propertyStatus'])
            ->when($request->filled('reference'), fn($q) => $q->where('reference', 'like', '%' . $request->reference . '%'))
            ->when($request->filled('title'), fn($q) => $q->where('title', 'like', '%' . $request->title . '%'))
            ->when($request->filled('city'), fn($q) => $q->where('city', 'like', '%' . $request->city . '%'))
            ->when($request->filled('type'), fn($q) => $q->whereHas('propertyType', fn($sq) => $sq->where('name', $request->type)))
            ->when($request->filled('operation'), fn($q) => $q->whereHas('propertyOperation', fn($sq) => $sq->where('name', $request->operation)))
            ->when($request->filled('status'), fn($q) => $q->whereHas('propertyStatus', fn($sq) => $sq->where('name', $request->status)))
            ->when($request->filled('price_min'), fn($q) => $q->where('price', '>=', $request->price_min))
            ->when($request->filled('price_max'), fn($q) => $q->where('price', '<=', $request->price_max))
            ->tap(fn($q) => $this->applySort($q, $sort, $dir))
            ->paginate(10)
            ->withQueryString();

        $types = PropertyType::active()->get(['id', 'name', 'label']);
        $operations = PropertyOperation::active()->get(['id', 'name', 'label']);
        $statuses = PropertyStatus::active()->get(['id', 'name', 'label']);

        return view('admin.properties.index', compact('properties', 'sort', 'dir', 'types', 'operations', 'statuses'));
    }

    /**
     * type, operation y status se ordenan por su tabla de catálogo (nombre), no por las
     * columnas de texto antiguas de properties, que los inmuebles nuevos no rellenan.
     */
    private function applySort($query, string $sort, string $dir): void
    {
        $catalogues = [
            'type' => ['property_types', 'type_id'],
            'operation' => ['property_operations', 'operation_id'],
            'status' => ['property_statuses', 'status_id'],
        ];

        if (isset($catalogues[$sort])) {
            [$table, $foreign] = $catalogues[$sort];
            $query->leftJoin($table, "{$table}.id", '=', "properties.{$foreign}")
                ->orderBy("{$table}.name", $dir);
        } else {
            $query->orderBy("properties.{$sort}", $dir);
        }

        $query->orderBy('properties.id', $dir);
    }

    public function show(Property $property)
    {
        $this->authorize('view', $property);

        $property->load('images', 'propertyType', 'propertyOperation', 'propertyStatus');

        return response()->json([
            'id'          => $property->id,
            'title'       => $property->title,
            'description' => $property->description,
            'type_id'     => $property->type_id,
            'operation_id' => $property->operation_id,
            'status_id'   => $property->status_id,
            'type'        => $property->propertyType?->name ?? $property->type,
            'operation'   => $property->propertyOperation?->name ?? $property->operation,
            'status'      => $property->propertyStatus?->name ?? $property->status,
            'price'       => $property->price,
            'surface'     => $property->surface,
            'rooms'       => $property->rooms,
            'bathrooms'   => $property->bathrooms,
            'address'     => $property->address,
            'city'        => $property->city,
            'locality'    => $property->locality,
            'province'    => $property->province,
            'country'     => $property->country,
            'latitude'    => $property->latitude,
            'longitude'   => $property->longitude,
            'main_image'  => $property->main_image,
            'main_image_url' => $property->mainImageUrl(),
            'images'      => $property->images->map(fn($img) => [
                'id'   => $img->id,
                'url'  => $img->url(),
                'type' => $img->type,
            ]),
            'user'        => $property->user ? ['id' => $property->user->id, 'name' => $property->user->name] : null,
        ]);
    }

    public function store(StorePropertyRequest $request)
    {
        $this->authorize('create', Property::class);

        $data = $request->validated();
        $data['user_id'] = auth()->id();

        // Resolve reference
        $data['reference'] = match ($data['reference_mode']) {
            'prefix' => Property::generateReference($data['reference_prefix'] ?? null),
            'manual' => strtoupper(trim($data['reference_manual'] ?? '')),
            default  => Property::generateReference(),
        };

        unset($data['reference_mode'], $data['reference_prefix'], $data['reference_manual']);

        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('properties', 'public');
        }

        $property = Property::create($data);

        foreach ($request->file('secondary_images', []) as $i => $file) {
            $property->images()->create([
                'path'  => $file->store('properties', 'public'),
                'type'  => 'secondary',
                'order' => $i,
            ]);
        }

        foreach ($request->file('floor_plans', []) as $i => $file) {
            $property->images()->create([
                'path'  => $file->store('properties', 'public'),
                'type'  => 'floor_plan',
                'order' => $i,
            ]);
        }

        return redirect()->route(auth()->user()->panelPrefix() . '.properties.index')
            ->with('success', __('admin_properties.success_create'));
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        $this->authorize('update', $property);

        $data = $request->validated();

        // Resolve reference
        $data['reference'] = match ($data['reference_mode']) {
            'auto'   => Property::generateReference(),
            'prefix' => Property::generateReference($data['reference_prefix'] ?? null),
            'manual' => strtoupper(trim($data['reference_manual'] ?? '')),
            default  => $property->reference, // keep existing
        };

        unset($data['reference_mode'], $data['reference_prefix'], $data['reference_manual']);

        if (!empty($data['remove_image'])) {
            if ($property->main_image) {
                Storage::disk('public')->delete($property->main_image);
            }
            $data['main_image'] = null;
        }

        if ($request->hasFile('main_image')) {
            if ($property->main_image) {
                Storage::disk('public')->delete($property->main_image);
            }
            $data['main_image'] = $request->file('main_image')->store('properties', 'public');
        }

        unset($data['remove_image']);
        $property->update($data);

        // Delete individually removed images
        if ($request->filled('delete_images')) {
            $toDelete = PropertyImage::whereIn('id', $request->input('delete_images'))
                ->where('property_id', $property->id)
                ->get();
            foreach ($toDelete as $img) {
                if (!str_starts_with($img->path, 'http')) {
                    Storage::disk('public')->delete($img->path);
                }
                $img->delete();
            }
        }

        // Add new secondary images
        foreach ($request->file('secondary_images', []) as $i => $file) {
            $order = ($property->secondaryImages()->max('order') ?? -1) + $i + 1;
            $property->images()->create([
                'path'  => $file->store('properties', 'public'),
                'type'  => 'secondary',
                'order' => $order,
            ]);
        }

        // Add new floor plans
        foreach ($request->file('floor_plans', []) as $i => $file) {
            $order = ($property->floorPlans()->max('order') ?? -1) + $i + 1;
            $property->images()->create([
                'path'  => $file->store('properties', 'public'),
                'type'  => 'floor_plan',
                'order' => $order,
            ]);
        }

        return redirect()->route(auth()->user()->panelPrefix() . '.properties.index')
            ->with('success', __('admin_properties.success_update'));
    }

    public function destroy(Property $property)
    {
        $this->authorize('delete', $property);

        if ($property->main_image && !str_starts_with($property->main_image, 'http')) {
            Storage::disk('public')->delete($property->main_image);
        }

        foreach ($property->images as $img) {
            if (!str_starts_with($img->path, 'http')) {
                Storage::disk('public')->delete($img->path);
            }
        }

        $property->delete();

        return redirect()->route(auth()->user()->panelPrefix() . '.properties.index')
            ->with('success', __('admin_properties.success_delete'));
    }

    public function clone(Property $property)
    {
        $this->authorize('update', $property);

        // Strip any existing clone suffix (-1, -2, ...) to get the true base reference
        $baseRef = preg_replace('/-\d+$/', '', $property->reference);
        $suffix  = 1;
        $newRef  = '';

        do {
            $newRef = $baseRef . '-' . $suffix++;
        } while (Property::where('reference', $newRef)->exists());

        $clone = $property->replicate();
        $clone->reference  = $newRef;
        $clone->status_id  = PropertyStatus::where('name', 'disponible')->first()?->id;
        $clone->user_id    = auth()->id();

        if ($clone->main_image && !str_starts_with($clone->main_image, 'http')) {
            $copy = 'properties/' . Str::uuid() . '.' . pathinfo($clone->main_image, PATHINFO_EXTENSION);

            if (Storage::disk('public')->exists($clone->main_image) && Storage::disk('public')->copy($clone->main_image, $copy)) {
                $clone->main_image = $copy;
            }
        }

        $clone->save();

        $message = __('admin_properties.success_clone', ['ref' => $newRef]);

        if (request()->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()->route(auth()->user()->panelPrefix() . '.properties.index')
            ->with('success', $message);
    }
}
