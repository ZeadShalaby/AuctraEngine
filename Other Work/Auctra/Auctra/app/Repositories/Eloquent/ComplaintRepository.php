<?php
namespace App\Repositories\Eloquent;

use App\Models\Complaint;
use App\Repositories\Interfaces\ComplaintRepositoryInterface;

class ComplaintRepository implements ComplaintRepositoryInterface
{

    public function __construct(protected Complaint $complaint){}

    //? user complaints
    public function MyComplaints($perPage = 10)
    {
        return $this->complaint
            ->where('user_id', auth()->id())
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        return $this->complaint->create($data);
    }

    //! admin
    public function all()
    {
        return $this->complaint->all();
    }

    public function find(int $id)
    {
        return $this->complaint->findOrFail($id);
    }
    public function changeStatus(int $id, array $data)
    {
        $complaint = $this->complaint->findOrFail($id);
        $complaint->update($data);
        activityLog($complaint, 'complaint_status_changed', [
            'description' => $complaint->description,
            'status' => $complaint->status,
        ]);
        return $complaint;
    }

    public function delete(int $id)
    {
        $complaint = $this->complaint->findOrFail($id);
        activityLog($complaint, 'complaint_deleted');
        return $complaint->delete();
    }
}