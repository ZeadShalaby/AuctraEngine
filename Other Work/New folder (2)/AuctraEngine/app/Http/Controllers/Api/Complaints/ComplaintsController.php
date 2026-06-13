<?php

namespace App\Http\Controllers\Api\Complaints;

use App\Http\Controllers\Controller;
use App\Http\Requests\Complaint\CreateRequest;
use App\Repositories\Eloquent\ComplaintRepository;
use Illuminate\Http\Request;

class ComplaintsController extends Controller
{
    //

    public function __construct(protected ComplaintRepository $complaintRepository){}


    public function index(Request $request)
    {
        $perPage = $request->input('paginate', 10);
        return successResponse(__('messages.retrieved_successfully'), $this->complaintRepository->MyComplaints($perPage),200);
    }

    public function store(CreateRequest $request)
    {
        $data = $request->validated();$data['user_id'] = auth()->id();
        return successResponse(__('messages.created_successfully'), $this->complaintRepository->create($data), 200);
    }

}
