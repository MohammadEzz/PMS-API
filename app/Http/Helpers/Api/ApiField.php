<?php

namespace App\Http\Helpers\Api;

use App\Exceptions\URLParameterException;

class ApiField {

    public function buildFields($urlFields, $fields) {
        if($this->checkFields($urlFields, $fields)) {
            $urlFields = explode(',', $urlFields);
            $selectedFields= [];
            foreach($fields as $field_key=>$field_value) {
                if(in_array($field_key, $urlFields)) {
                    $selectedFields[] = $field_value . " as " . $field_key;
                }
            }
            return $selectedFields;
        }
    }

    public function aliasFieldsName($fields, array $cutomeAlies = []) {
        $selectedFields = [];
        $fields = array_merge($fields, $cutomeAlies);
        foreach($fields as $field_key=>$field_value ) {
            $selectedFields[] = $field_value . " as " . $field_key;
        }
        return $selectedFields;
    }

    private function checkFields($urlFields, $fields) {
            $urlFields = explode(',', $urlFields);
            foreach($urlFields as $field) {
                if(!in_array($field, array_keys($fields))){
                    throw new URLParameterException("Field Name `$field` Not Found");
                }
            }
        
        return true;
    }
}
