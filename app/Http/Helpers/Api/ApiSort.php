<?php

namespace App\Http\Helpers\Api;

use App\Exceptions\SortURLQueryExceiption;

class ApiSort {

    public function buidlSort($urlSort) {

        $urlSort = strtolower($urlSort);
        $this->checkSortSyntax($urlSort) && $sortArray = explode(',', $urlSort);
        return $sortArray;
    }

    private function checkSortSyntax($sortQuery) {

        $sortArray = explode(',', $sortQuery);
        if(count($sortArray) < 1) {
            $message = "Syntax error on sort query $sortQuery ...";
            throw new SortURLQueryExceiption($message);
        }
        foreach($sortArray as $item) {
            $sortItem = explode('.', $item);
            if(count($sortItem) != 2 ){
                $message = "Syntax error on sort query $sortQuery ...";
                throw new SortURLQueryExceiption($message);
            }
            if($sortItem[1] != 'asc' && $sortItem[1] != 'desc') {
                $message = "Syntax error on $sortQuery ... Sorting only allowed by ASC or DESC ";
                throw new SortURLQueryExceiption($message);
            }
        }
    
        return true;
    }
}
