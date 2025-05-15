<?php

namespace App\Http\Controllers;

use App\Http\Requests\Complaint\StoreComplaintRequest;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query();
        $limit = $query['limit'] ?? 0;
        $search = $query['search'] ?? '';
        $sort_by = $query['sort_by'] ?? 'created_at';
        $order = $query['order'] ?? 'desc';
        $trashed = $query['trashed'] ?? false;

        $query = Complaint::when(
            $search,
            function (Builder $query, string $search) {
                $query->where('name', 'like', '%' . strtolower($search) . '%');
            }
        )
            ->when(
                $trashed,
                function (Builder $query) {
                    $query->onlyTrashed();
                }
            )
            ->with('deletedBy')
            ->orderByRaw($sort_by . ' ' . $order);

        $result = collect();
        if ($limit > 0) {
            $result = $query->paginate($limit);
        } else {
            $result = $query->get();
        }

        return [
            'data' => $result
        ];
    }

    public function store(StoreComplaintRequest $request)
    {
        $complaint = Complaint::create($request->validated());
        return [
            'data' => $complaint,
        ];
    }

    public function show(Complaint $complaint)
    {
        return [
            'data' => $complaint,
        ];
    }

    public function update(StoreComplaintRequest $request, Complaint $complaint)
    {
        $complaint->update($request->validated());
        return [
            'data' => $complaint,
        ];
    }

    public function destroy(Complaint $complaint)
    {
        $complaint->delete();
        return [
            'data' => $complaint,
        ];
    }
}
