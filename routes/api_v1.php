<?php

use App\Http\Controllers\Api\V1\ActiveIngredientController;
use App\Http\Controllers\Api\V1\ActiveIngredientInteractionController;
use App\Http\Controllers\Api\V1\CityController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\ContraindicationController;
use App\Http\Controllers\Api\V1\CountryController;
use App\Http\Controllers\Api\V1\DealerController;
use App\Http\Controllers\Api\V1\DebitController;
use App\Http\Controllers\Api\V1\DiseaseActiveIngredientController;
use App\Http\Controllers\Api\V1\DiseaseController;
use App\Http\Controllers\Api\V1\DrugAlternativeController;
use App\Http\Controllers\Api\V1\DrugController;
use App\Http\Controllers\Api\V1\DrugInteractionController;
use App\Http\Controllers\Api\V1\DrugActiveIngredientController;
use App\Http\Controllers\Api\V1\DrugContraindicationController;
use App\Http\Controllers\Api\V1\DrugsAlternativesController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\OptionController;
use App\Http\Controllers\Api\V1\PriceController;
use App\Http\Controllers\Api\V1\PurchaseBillController;
use App\Http\Controllers\Api\V1\PurchaseItemController;
use App\Http\Controllers\Api\V1\PurchaseReturnBillController;
use App\Http\Controllers\api\v1\PurchaseReturnItemController;
use App\Http\Controllers\Api\V1\SalesBillController;
use App\Http\Controllers\Api\V1\SalesItemController;
use App\Http\Controllers\Api\V1\SalesReturnBillController;
use App\Http\Controllers\Api\V1\SalesReturnItemController;
use App\Http\Controllers\Api\V1\SupplierController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

    Route::get('/create-token', function() {
        $user = User::find(1);
        return $user->createToken('token-name')->plainTextToken;
    });

    Route::get('/revoke-token', function() {
        $user = User::find(1);
        return $user->tokens()->delete();
    });

    // Route::post('/users', [RegisteredUserController::class, 'store']);

    // Route::middleware('auth:sanctum')->group( function () {
        Route::get('/drugs/search', [DrugController::class, 'search']);
        Route::apiResource('drugs', DrugController::class)
        ->whereNumber('drug');
    // });

    Route::apiResource('drugs.activeingredients', DrugActiveIngredientController::class)
    ->whereNumber(['drug', 'activeingredient']);

    Route::apiResource('drugs.contraindications', DrugContraindicationController::class)
    ->whereNumber(['drug', 'contraindication']);

    Route::apiResource('drugs.alternatives', DrugsAlternativesController::class)
    ->whereNumber(['drug', 'alternative']);
    Route::apiResource('activeingredients', ActiveIngredientController::class);

    Route::apiResource('activeingredients.interactions', ActiveIngredientInteractionController::class)
    ->whereNumber(['activeingredient', 'interaction'])
    ->only(['index', 'show']);

    Route::apiResource('diseases', DiseaseController::class)
    ->whereNumber('disease');

    Route::apiResource('diseases.activeingredients', DiseaseActiveIngredientController::class)
    ->whereNumber(['disease', 'activeingredient']);

    Route::apiResource('druginteractions', DrugInteractionController::class)
    ->parameters(['druginteractions' => 'id'])
    ->whereNumber('id');
    Route::apiResource('drugalternatives', DrugAlternativeController::class)
    ->parameters(['drugalternatives' => 'id'])
    ->whereNumber('id');
    Route::apiResource('contraindications', ContraindicationController::class)
    ->parameters(['contraindications' => 'id'])
    ->whereNumber('id');

    Route::apiResource('options', OptionController::class)->only(['index']);
    Route::apiResource('countries', CountryController::class)->only(['index']);
    Route::apiResource('cities', CityController::class)->only(['index']);
    Route::apiResource('suppliers', SupplierController::class)->only(['index']);
    Route::apiResource('dealers', DealerController::class)->only(['index']);
    Route::apiResource('clients', ClientController::class)->only(['index']);

    Route::put('/users/{id}/username', [UserController::class, 'updateUserName'])
    ->whereNumber('id')
    ->name('users.update.username');
    Route::put('/users/{id}/email', [UserController::class, 'updateEmail'])
    ->whereNumber('id')
    ->name('users.update.email');
    Route::put('/users/{id}/password', [UserController::class, 'updatePassword'])
    ->whereNumber('id')
    ->name('users.update.password');
    Route::apiResource('users', UserController::class);

    Route::post('/purchases/{id}/approve', [PurchaseBillController::class, "approveBill"])
    ->whereNumber('id')
    ->name('purchases.approve');
    Route::apiResource('purchases', PurchaseBillController::class)
    ->parameters(['purchases' => 'id'])
    ->whereNumber('id');

    Route::apiResource('purchases/items', PurchaseItemController::class)
    ->names('purchases.items')
    ->parameters(['items' => 'id'])
    ->whereNumber(['id']);
    
    Route::post('/purchases/returns/{id}/approve', [PurchaseReturnBillController::class, "approveBill"])
    ->whereNumber('id')
    ->name('purchases.returns.approve');
    Route::apiResource('purchases/returns', PurchaseReturnBillController::class)
    ->names('purchases.returns')
    ->parameters(['returns' => 'id'])
    ->whereNumber('id');
    
    Route::apiResource('/purchases/returns/items', PurchaseReturnItemController::class)
    ->names('purchases.returns.items')
    ->parameters(['items' => 'id'])
    ->whereNumber('id');

    Route::apiResource('sales', SalesBillController::class)
    ->parameters(['sales' => 'id'])
    ->whereNumber('id');
    Route::apiResource('sales/items', SalesItemController::class)
    ->names('sales.items')
    ->parameters(['items' => 'id'])
    ->whereNumber('id');
    Route::apiResource('sales/returns', SalesReturnBillController::class)
    ->names('sales.returns')
    ->parameters(['returns' => 'id'])
    ->whereNumber('id');
    Route::apiResource('sales/returns/items', SalesReturnItemController::class)
    ->names('sales.returns.items')
    ->parameters(['items' => 'id'])
    ->whereNumber('id');

    Route::get('inventory', [InventoryController::class, 'index'])
    ->name('inventory.index');
    Route::get('inventory/quantity', [InventoryController::class, 'quantity'])
    ->name('inventory.quantity');

    Route::get('prices', [PriceController::class, 'index'])
    ->name('prices.index');

    Route::get('debits/supplier', [DebitController::class, 'index'])
    ->name('supplier.index');

    Route::fallback(function(){
        return response()->json(['message' => "Page not found"], 404);
    });

