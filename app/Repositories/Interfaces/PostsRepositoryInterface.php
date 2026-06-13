<?php 

namespace App\Repositories\Interfaces;

interface PostsRepositoryInterface {


public function all($perPage = 15);
public function find(int $id);
public function create(array $data); 
public function update(int $id, array $data);
public function delete(int $id);
public function search(string $query , $perPage = 10);
public function userPosts(int $userId);
public function myPosts();


}