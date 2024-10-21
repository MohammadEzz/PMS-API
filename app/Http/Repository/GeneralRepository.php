<?php
namespace App\Http\Repository;

use Illuminate\Http\Request;

interface GeneralRepository {

    public function fetchListOfItems(Request $request, array $fields): array;
    
}
?>