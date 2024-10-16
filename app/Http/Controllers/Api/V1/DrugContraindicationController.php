<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Requests\ContraindicationRequest;
use App\Http\Requests\DrugContraindicationRequest;
use App\Models\Contraindication;
use App\Models\Drug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class DrugContraindicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($drug_id)
    {
        $drug = Drug::find($drug_id);
        if($drug) {
            $drugsContraindications = $drug->contraindications()
            ->join('options as o1', 'o1.id', '=', 'contraindications.level')
            ->join('options as o2', 'o2.id', '=', 'contraindications.category')
            ->select('contraindications.id','o1.name as level', 'o1.id as levelid', 'o2.name as category', 'o2.id as categoryid', 'contraindications.description')
            ->get();

            return ApiMessagesTemplate::createResponse(true, 200, "Drugs Contraindications Readed Successfully", $drugsContraindications);
        }
        return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Exist");

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DrugContraindicationRequest $request, $drug_id)
    {
        $drug = Drug::find($drug_id);
        if($drug) {
            $data = $request->only(["category", "description", "level", "order"]);
            $data["drug_id"] = $drug_id;
            $isCreated = Contraindication::create($data);
            if($isCreated) {
                $contraindication = Drug::find($drug_id)
                                    ->contraindications()
                                    ->where('contraindications.id', $isCreated->id)
                                    ->join('options as o1', 'o1.id', '=', 'contraindications.level')
                                    ->join('options as o2', 'o2.id', '=', 'contraindications.category')
                                    ->select('contraindications.id','o1.name as level', 'o1.id as levelid', 'o2.name as category', 'o2.id as categoryid', 'contraindications.description')
                                    ->first();

                return ApiMessagesTemplate::createResponse(true, 200, "Drugs Contraindications Created Successfully", $contraindication);
            }
        }
        return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Exist");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($drugId, $contraindicationId)
    {
        $drug = Drug::find($drugId);
        $contraindication = Contraindication::find($contraindicationId);
        if(!$drug) {
            return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Exist");
        }
        elseif(!$contraindication){
            return ApiMessagesTemplate::createResponse(false, 404, "Contraindication Not Exist");
        }
        elseif($drug && $contraindication) {
            return ApiMessagesTemplate::createResponse(true, 200, "Drug Contraindication Readed Successfully", $contraindication);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DrugContraindicationRequest $request, $drugId, $contraindicationId)
    {
        $drug =Drug::find($drugId);
        $contraindication = Contraindication::find($contraindicationId);
        if(!$drug) {
            return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Exist");
        }
        elseif(!$contraindication) {
            return ApiMessagesTemplate::createResponse(false, 404, "Contraindication Not Exist");
        }
        elseif($drug && $contraindication) {
            $data = $request->only(["category", "description", "level", "order"]);
            $data["drug_id"] = $drugId;
            $isUpdated = $drug->contraindications()->where('contraindications.id', $contraindicationId)->update($data);
            if($isUpdated) {
                $contraindication = Drug::find($drugId)
                                    ->contraindications()
                                    ->where('contraindications.id', $contraindicationId)
                                    ->join('options as o1', 'o1.id', '=', 'contraindications.level')
                                    ->join('options as o2', 'o2.id', '=', 'contraindications.category')
                                    ->select('contraindications.id','o1.name as level', 'o1.id as levelid', 'o2.name as category', 'o2.id as categoryid', 'contraindications.description')
                                    ->first();
                return ApiMessagesTemplate::createResponse(true, 200, "Drug Contraindication Updated Successfully", $contraindication);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($drugId, $contraindicationId)
    {
        $drug = Drug::find($drugId);
        $contraindication = Contraindication::find($contraindicationId);
        if(!$drug) {
            return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Exist");
        }
        elseif(!$contraindication) {
            return ApiMessagesTemplate::createResponse(false, 404, "Contraindication Not Exist");
        }
        elseif($drug && $contraindication) {
            $isDeleted = $drug->contraindications()->where('id', $contraindicationId)->delete($contraindicationId);
            if($isDeleted) {
                return ApiMessagesTemplate::createResponse(true, 201, "Deleted Successfully");
            }
        }
    }
}
