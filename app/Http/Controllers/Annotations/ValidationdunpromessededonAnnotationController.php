<?php

namespace App\Http\Controllers\Annotations ;

/**
 * @OA\Security(
 *     security={
 *         "BearerAuth": {}
 *     }),

 * @OA\SecurityScheme(
 *     securityScheme="BearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"),

 * @OA\Info(
 *     title="Your API Title",
 *     description="Your API Description",
 *     version="1.0.0"),

 * @OA\Consumes({
 *     "multipart/form-data"
 * }),

 *

 * @OA\PUT(
 *     path="/api/rendez-vous/{rendezVous}/etat",
 *     summary="Valider un don",
 *     description="",
 *         security={
 *    {       "BearerAuth": {}}
 *         },
 * @OA\Response(response="200", description="OK"),
 * @OA\Response(response="404", description="Not Found"),
 * @OA\Response(response="500", description="Internal Server Error"),
 *     @OA\Parameter(in="header", name="User-Agent", required=false, @OA\Schema(type="string")
 * ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 properties={
 *                     @OA\Property(property="etat", type="boolean"),
 *                     @OA\Property(property="rendez_vouse_id", type="integer"),
 *                     @OA\Property(property="groupe_sanguin", type="string"),
 *                     @OA\Property(property="date_prelevement", type="string"),
 *                     @OA\Property(property="banque_sang_id", type="integer"),
 *                     @OA\Property(property="donneur_externe_id", type="string", format="binary"),
 *                 },
 *             ),
 *         ),
 *     ),
 *     tags={"Validation d'un promesse de don"},
*),


 * @OA\GET(
 *     path="/api/rendezvous/{id}",
 *     summary="Détail rendez vous avec les date de l'annonce",
 *     description="",
 *         security={
 *    {       "BearerAuth": {}}
 *         },
 * @OA\Response(response="200", description="OK"),
 * @OA\Response(response="404", description="Not Found"),
 * @OA\Response(response="500", description="Internal Server Error"),
 *     @OA\Parameter(in="header", name="User-Agent", required=false, @OA\Schema(type="string")
 * ),
 *     tags={"Validation d'un promesse de don"},
*),


*/

 class ValidationdunpromessededonAnnotationController {}
