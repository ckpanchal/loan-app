<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *    title="Loan API",
 *    version="1.0.0",
 *    description="API to interact with Loan Application",
 *    contact = {
 *       "name": "Chintan Panchal",
 *    }
 * )
 * 
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Dynamic host server"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      description="Enter your Bearer Token",
 *      type="http",
 *      scheme="bearer"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
