<?php

namespace App\Http\Helpers\Api;

use App\Exceptions\URLParameterException;
class ApiFilter {

    public function buildFilter($query, $fields) {

        if($query) {
            $sqlQuery = '';
            $queryParams = [];
            $offset = 0;
            $openParentheses = 0;
            $lastClosedParenthesesIndex = null;
            $prevOperator = null;
            $nextPartOfQuery = QueryParts::FIELD . QueryParts::OPEN_PARENTHESES;

            while($offset < strlen($query)) {

                switch($nextPartOfQuery) {

                    case QueryParts::FIELD . QueryParts::OPEN_PARENTHESES:
                        {
                            if($query[$offset] === '(') {
                                $sqlQuery .= '( ';
                                $offset++;
                                $openParentheses++;
                                $nextPartOfQuery = QueryParts::FIELD . QueryParts::OPEN_PARENTHESES;
                            }
                            else {
                                $index = strpos($query, ':', $offset);

                                if($index != false) {
                                    $filedName = trim(substr($query, $offset, ($index - $offset)));

                                    if(in_array($filedName, array_keys($fields))) {
                                        $sqlQuery .= "$fields[$filedName] ";
                                        $offset = $index + 1;
                                        $nextPartOfQuery = QueryParts::OPERATOR;
                                    }
                                    else {
                                        $message = ($index-strlen($filedName)) . " :: Filed Name `$filedName` Not Found => " . substr($query, 0, $index);
                                        throw new URLParameterException($message);
                                    }
                                }
                                else {
                                    $message = $offset . " :: Field Name Not Correct => " . substr($query, 0, $offset) . " ...";
                                    throw new URLParameterException($message);
                                }
                            }
                        }
                        break;

                    case QueryParts::OPERATOR:
                        {                            
                            $operators = array_keys(ComparisonOperator::COMPARISON);

                            // we add opn praket after the operator such eq[, in[ .. 
                            // to make check on operator and open pracket once
                            $operatorsWithOpenPracket = array_map(function($item){
                                return $item.'[';
                            }, $operators);

                            // like, nbtw operators
                            if(in_array(substr($query, $offset, 5), $operatorsWithOpenPracket))
                            {
                                $operator = ComparisonOperator::COMPARISON[substr($query, $offset, 4)];
                                $sqlQuery .= "$operator ";
                                $prevOperator = substr($query, $offset, 4);
                                $offset+=5;
                                $nextPartOfQuery = QueryParts::VALUE;
                            }

                            // btw, nin, neq, lte, gte
                            elseif(in_array(substr($query, $offset, 4), $operatorsWithOpenPracket))
                            {
                                $operator = ComparisonOperator::COMPARISON[substr($query, $offset, 3)];
                                $sqlQuery .= $operator." ";
                                $prevOperator = substr($query, $offset, 3);
                                $offset+=4;
                                $nextPartOfQuery = QueryParts::VALUE;
                            }

                            // gt, lt, eq, in
                            elseif(in_array(substr($query, $offset, 3), $operatorsWithOpenPracket))
                            {
                                $operator = ComparisonOperator::COMPARISON[substr($query, $offset, 2)];
                                $sqlQuery .= $operator." ";
                                $prevOperator = substr($query, $offset, 2);
                                $offset+=3;
                                $nextPartOfQuery = QueryParts::VALUE;
                            }
                            else {
                                $message = $offset . " :: Operator Not Correct at => " . substr($query, 0, $offset+4) . " ...";
                                throw new URLParameterException($message);
                            }
                        }
                        break;

                    case QueryParts::VALUE:
                        {
                            if(in_array($prevOperator, ['in', 'nin'])) {
                                $index = strpos($query, ']', $offset);
                                if($index != false) {
                                    $valueAsArray = explode(',', substr($query, $offset, $index-$offset));
                                    if(count($valueAsArray) > 0 && $valueAsArray[0] !== '') {
                                        $subQuery = "(";
                                        foreach($valueAsArray as $item) {
                                            $parameterName = count($queryParams) . $prevOperator;
                                            $queryParams[$parameterName] = $item;
                                            $subQuery .= ":$parameterName,";
                                        }

                                        // remove the trailer ,
                                        $subQuery = substr($subQuery, 0, strlen($subQuery)-1);
                                        $subQuery .= ")";
                                        $sqlQuery .= $subQuery." ";
                                        $offset = $index+1;
                                        $nextPartOfQuery = QueryParts::CLOSE_PARENTHESES . QueryParts::AND . QueryParts::OR;
                                    }
                                    else {
                                        $message = $offset . " :: Values Inside `$prevOperator` Prackets Not Correct => " . substr($query, 0, $offset) . " ...";
                                        throw new URLParameterException($message);
                                    }
                                }
                            }

                            elseif(in_array($prevOperator, ['btw', 'nbtw'])) {
                                $index = strpos($query, ']', $offset);
                                if($index != false) {
                                    $valueAsArray = explode(',', substr($query, $offset, $index-$offset));
                                    if(count($valueAsArray) == 2) {
                                        $paramIndex0 = count($queryParams) . $prevOperator;
                                        $queryParams[$paramIndex0] = $valueAsArray[0];
                                        $paramIndex1 = count($queryParams) . $prevOperator;
                                        $queryParams[$paramIndex1] = $valueAsArray[1];
                                        $subQuery =  ":$paramIndex0 AND  :$paramIndex1";
                                        $sqlQuery .= $subQuery.' ';
                                        $offset = $index+1;
                                        $nextPartOfQuery = QueryParts::CLOSE_PARENTHESES . QueryParts::AND . QueryParts::OR;
                                    }
                                    else {
                                        $message = "$offset :: Values Inside `$prevOperator` Prackets Not Correct => " . substr($query, 0, $offset) . " ...";
                                        throw new URLParameterException($message);
                                    }
                                }
                            }

                            elseif($prevOperator === 'like') {
                                $index = strpos($query, ']', $offset);
                                if($index != false) {
                                    $value = substr($query, $offset, $index-$offset);
                                    $paramIndex = count($queryParams) . 'l';
                                    if($value[0] === '%' && $value[strlen($value)-1] === '%') {
                                        $queryParams[$paramIndex] = substr($value, 1, strlen($value)-2);
                                        $subQuery = "CONCAT('%',:$paramIndex,'%')";
                                    }
                                    elseif($value[0] === '%') {
                                        $queryParams[$paramIndex] = substr($value, 1, strlen($value)-1);
                                        $subQuery = "CONCAT('%',:$paramIndex)";
                                    }
                                    elseif($value[strlen($value)-1] === '%') {
                                        $queryParams[$paramIndex] = substr($value, 0, strlen($value)-1);
                                        $subQuery = "CONCAT(:$paramIndex,'%')";
                                    }
                                    else {
                                        $queryParams[$paramIndex] = substr($value, 0, strlen($value));
                                        $subQuery = ":$paramIndex";
                                    }
                                    $sqlQuery .= $subQuery.' ';
                                    $offset = $index+1;
                                    $nextPartOfQuery = QueryParts::CLOSE_PARENTHESES . QueryParts::AND . QueryParts::OR;
                                }
                                else {
                                    $message = "$offset :: Values Inside `$prevOperator` Prackets Not Correct => " . substr($query, 0, $offset) . " ...";
                                    throw new URLParameterException($message);
                                }
                            }

                            else {
                                $index = strpos($query, ']', $offset);
                                if($index != false) {
                                    $paramIndex = count($queryParams) . $prevOperator;
                                    $queryParams[$paramIndex] = substr($query, $offset, $index-$offset);
                                    $subQuery = ":$paramIndex";
                                    $sqlQuery .= $subQuery.' ';
                                    $offset = $index+1;
                                    $nextPartOfQuery = QueryParts::CLOSE_PARENTHESES . QueryParts::AND . QueryParts::OR;
                                }
                                else {
                                    $message = $offset . " :: Syntax Error => " . substr($query, 0, $offset) . " ...";
                                    throw new URLParameterException($message);
                                }
                            }
                        }
                        break;

                    case QueryParts::CLOSE_PARENTHESES . QueryParts::AND . QueryParts::OR:
                        {

                            if($query[$offset] === ')') {
                                $openParentheses--;
                                if($openParentheses < 0) {
                                    $message = $offset . " :: Syntax Error => " . substr($query, 0, $offset+1) . "=> Missing Close Parentheses";
                                    throw new URLParameterException($message);
                                }
                                $lastClosedParenthesesIndex =  $offset; // Info for debug only.
                                $sqlQuery .= ") ";
                                $offset++;
                                $nextPartOfQuery = QueryParts::CLOSE_PARENTHESES . QueryParts::AND . QueryParts::OR;
                            }
                            elseif($query[$offset] === ';') {
                                $sqlQuery .= "AND ";
                                $offset++;
                                $nextPartOfQuery = QueryParts::FIELD . QueryParts::OPEN_PARENTHESES;
                            }
                            elseif($query[$offset] === ',') {
                                $sqlQuery .= "OR ";
                                $offset++;
                                $nextPartOfQuery = QueryParts::FIELD . QueryParts::OPEN_PARENTHESES;
                            }
                            else {
                                $message = $offset . " :: Syntax Error => " . substr($query, 0, $offset+1) . " ...";
                                throw new URLParameterException($message);
                            }
                        }
                        break;
                }

                
            }

            if($openParentheses > 0) {
                $message = $offset . " :: Syntax Error => " . substr($query, 0, $lastClosedParenthesesIndex+1) . "=> Missing Close Parentheses";
                throw new URLParameterException($message);
            }
            return [$sqlQuery, $queryParams];
        }
        return '';
    }
}

