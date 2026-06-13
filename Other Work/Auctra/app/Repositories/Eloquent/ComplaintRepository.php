<?php
namespace App\Repositories;

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

    public function find($id)
    {
        return $this->complaint->findOrFail($id);
    }

    public function changeStatus($id, array $data)
    {
        $complaint = $this->complaint->findOrFail($id);
        $complaint->update($data);
        return $complaint;
    }

    public function delete($id)
    {
        $complaint = $this->complaint->findOrFail($id);
        return $complaint->delete();
    }
}