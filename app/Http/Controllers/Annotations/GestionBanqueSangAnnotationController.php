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

 * @OA\GET(
 *     path="/api/banque-sangs/{id}",
 *     summary="Detail d'une banque de sang",
 *     description="",
 *         security={
 *    {       "BearerAuth": {}}
 *         },
 * @OA\Response(response="200", description="OK"),
 * @OA\Response(response="404", description="Not Found"),
 * @OA\Response(response="500", description="Internal Server Error"),
 *     @OA\Parameter(in="header", name="User-Agent", required=false, @OA\Schema(type="string")
 * ),
 *     tags={"Gestion Banque Sang"},
*),


 * @OA\DELETE(
 *     path="/api/banque-sangs/{id}",
 *     summary="Supprimer une banque de sang",
 *     description="",
 *         security={
 *    {       "BearerAuth": {}}
 *         },
 * @OA\Response(response="204", description="Deleted successfully"),
 * @OA\Response(response="401", description="Unauthorized"),
 * @OA\Response(response="403", description="Forbidden"),
 * @OA\Response(response="404", description="Not Found"),
 *     @OA\Parameter(in="header", name="User-Agent", required=false, @OA\Schema(type="string")
 * ),
 *     tags={"Gestion Banque Sang"},
*),


 * @OA\PUT(
 *     path="/api/banque-sangs/{id}",
 *     summary="Modifier une banque de sang",
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
 *                     @OA\Property(property="matricule", type="string"),
 *                     @OA\Property(property="stock_actuelle", type="integer"),
 *                     @OA\Property(property="date_mise_a_jour", type="string"),
 *                     @OA\Property(property="structure_id", type="integer"),
 *                 },
 *             ),
 *         ),
 *     ),
 *     tags={"Gestion Banque Sang"},
*),


 * @OA\POST(
 *     path="/api/banque-sangs",
 *     summary="Ajouter une banque de sang",
 *     description="",
 *         security={
 *    {       "BearerAuth": {}}
 *         },
 * @OA\Response(response="201", description="Created successfully"),
 * @OA\Response(response="400", description="Bad Request"),
 * @OA\Response(response="401", description="Unauthorized"),
 * @OA\Response(response="403", description="Forbidden"),
 *     @OA\Parameter(in="header", name="User-Agent", required=false, @OA\Schema(type="string")
 * ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 properties={
 *                     @OA\Property(property="matricule", type="string"),
 *                     @OA\Property(property="stock_actuelle", type="integer"),
 *                     @OA\Property(property="date_mise_a_jour", type="string"),
 *                     @OA\Property(property="structure_id", type="integer"),
 *                 },
 *             ),
 *         ),
 *     ),
 *     tags={"Gestion Banque Sang"},
*),


 * @OA\GET(
 *     path="/api/banque-sangs",
 *     summary="Liste des banques de sang",
 *     description="",
 *         security={
 *    {       "BearerAuth": {}}
 *         },
 * @OA\Response(response="200", description="OK"),
 * @OA\Response(response="404", description="Not Found"),
 * @OA\Response(response="500", description="Internal Server Error"),
 *     @OA\Parameter(in="header", name="User-Agent", required=false, @OA\Schema(type="string")
 * ),
 *     tags={"Gestion Banque Sang"},
*),


*/

 class GestionBanqueSangAnnotationController {}
