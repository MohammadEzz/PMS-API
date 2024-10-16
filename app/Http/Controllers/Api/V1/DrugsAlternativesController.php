<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Requests\DrugsAlternativesRequest;
use App\Models\Drug;
use App\Models\DrugAlternative;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class DrugsAlternativesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($drugId)
    {
        $drug = Drug::find($drugId);
        if($drug) {
            $drugAlternative = $drug->alternatives()
            ->join('drugs', 'drugs.id', '=', 'drugalternatives.alternative_id')
            ->select('drugalternatives.id', 'alternative_id', 'name', 'drugalternatives.order')
            ->get();

            return ApiMessagesTemplate::createResponse(true, 200, "Drug Alternatives Readed Successfully", $drugAlternative);
        }
        return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Exist");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DrugsAlternativesRequest $request, $drugId)
    {
        $drug = Drug::find($drugId);
        $alternativeDrug = Drug::find($request->input('alternative_id'));
        if(!$drug) {
            return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Exist");
        }
        elseif(!$alternativeDrug) {
            return ApiMessagesTemplate::createResponse(false, 404, "Drug Alternative Not Exist");
        }
        elseif($drug && $alternativeDrug) {
            $data = $request->only(["alternative_id", "order"]);
            $data["drug_id"] = $drugId;
            $isCreated = DrugAlternative::create($data);
            if($isCreated)
                $alternative = Drug::find($drugId)->alternatives()
                ->join('drugs', 'drugs.id', '=', 'drugalternatives.alternative_id')
                ->where('drugalternatives.alternative_id', $isCreated->alternative_id)
                ->select('drugalternatives.id', 'alternative_id', 'name', 'drugalternatives.order')
                ->first();
                return ApiMessagesTemplate::createResponse(true, 200, "Drug Alternative Added Successfully", $alternative);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($drugId, $alternativeId)
    {
        $drug = Drug::find($drugId);
        $alternative = Drug::find($alternativeId);

        if(!$drug) {
            return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Exist");
        }
        elseif(!$alternative) {
            return ApiMessagesTemplate::createResponse(false, 404, "Drug Alternative Not Exist");
        }
        elseif($drug && $alternative) {
            $drugAlternative = $drug->alternatives()->where("alternative_id", $alternativeId)->get();
            if($drugAlternative->isNotEmpty())
                return ApiMessagesTemplate::createResponse(true, 200, "Drug Alternative Readed Successfully", $drugAlternative[0]);
            else
                return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Link With This Alternative");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DrugsAlternativesRequest $request, $drugId, $alternativeDrugId)
    {
        $drug = Drug::find($drugId);
        $alternative = Drug::find($alternativeDrugId);

        if(!$drug) {
            return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Exist");
        }
        elseif(!$alternative) {
            return ApiMessagesTemplate::createResponse(false, 404, "Alternative Drug Not Exist");
        }
        elseif($drug && $alternative) {
            $alternative = $drug->alternatives()
            ->where("alternative_id", $alternativeDrugId)
            ->get();

            if($alternative->isNotEmpty()){
                $data = $request->only(["alternative_id", "order"]);
                $isUpdated = $alternative[0]->update($data);
                if($isUpdated) {
                    $alternative = Drug::find($drugId)->alternatives()
                    ->join('drugs', 'drugs.id', '=', 'drugalternatives.alternative_id')
                    ->where('drugalternatives.alternative_id', $request->input('alternative_id'))
                    ->select('drugalternatives.id', 'alternative_id', 'name', 'drugalternatives.order')
                    ->first();
                    return ApiMessagesTemplate::createResponse(true, 200, "Drug Alternative Updated Successfully", $alternative);
                }
                return ApiMessagesTemplate::createResponse(false, 500, "Drug Alternative Updated Failed");
            }
            else
                return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Link With This Alternative");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($drugId, $alternativeDrugId)
    {
        $drug = Drug::find($drugId);
        $alternativeDrug = Drug::find($alternativeDrugId);
        if(!$drug) {
            return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Exist");
        }
        elseif(!$alternativeDrug) {
            return ApiMessagesTemplate::createResponse(false, 404, "Alternative Drug Not Exist");
        }
        elseif($drug && $alternativeDrug) {
            $alternative = $drug->alternatives()->where("alternative_id", $alternativeDrugId)->get();
            if($alternative->isNotEmpty()){
                $isDeleted = $alternative[0]->delete();

                if($isDeleted)
                    return ApiMessagesTemplate::createResponse(true, 200, "Drug Alternative Deleted Successfully");
            }
            return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Link With This Alternative");
        }
    }
}
