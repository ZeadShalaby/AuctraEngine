<?php


namespace App\Repositories\Interfaces;


interface ComplaintRepositoryInterface
{

    public function MyComplaints($perPage);
    public function create(array $data);
    //!admin
    public function all();
    //!admin
    public function find(int $id);
    // !admin
    public function changeStatus(int $id, array $data);
    // !admin
    public function delete(int $id);


}