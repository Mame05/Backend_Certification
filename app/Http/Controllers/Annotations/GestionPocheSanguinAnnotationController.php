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
 *     path="/api/poche-sanguins/{id}",
 *     summary="Detail d'une poche sanguin",
 *     description="",
 *         security={
 *    {       "BearerAuth": {}}
 *         },
 * @OA\Response(response="200", description="OK"),
 * @OA\Response(response="404", description="Not Found"),
 * @OA\Response(response="500", description="Internal Server Error"),
 *     @OA\Parameter(in="header", name="User-Agent", required=false, @OA\Schema(type="string")
 * ),
 *     tags={"Gestion Poche Sanguin"},
*),


 * @OA\DELETE(
 *     path="/api/poche-sanguins/{id}",
 *     summary="Supprimer une poche sanguin",
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
 *     tags={"Gestion Poche Sanguin"},
*),


 * @OA\PUT(
 *     path="/api/poche-sanguins/{id}",
 *     summary="Modifier  poche pour donneur externe",
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
 *                     @OA\Property(property="nom", type="string"),
 *                     @OA\Property(property="prenom", type="string"),
 *                     @OA\Property(property="telephone", type="string"),
 *                     @OA\Property(property="adresse", type="string"),
 *                     @OA\Property(property="donneur_externe_id", type="integer"),
 *                     @OA\Property(property="sexe", type="string"),
 *                     @OA\Property(property="date_naiss", type="string"),
 *                     @OA\Property(property="profession", type="string"),
 *                     @OA\Property(property="groupe_sanguin", type="string"),
 *                     @OA\Property(property="date_prelevement", type="string"),
 *                     @OA\Property(property="banque_sang_id", type="integer"),
 *                     @OA\Property(property="rendez_vouse_id", type="string", format="binary"),
 *                 },
 *             ),
 *         ),
 *     ),
 *     tags={"Gestion Poche Sanguin"},
*),


 * @OA\GET(
 *     path="/api/poches-par-mois",
 *     summary="Nombre de poche ajouté par mois",
 *     description="",
 *         security={
 *    {       "BearerAuth": {}}
 *         },
 * @OA\Response(response="200", description="OK"),
 * @OA\Response(response="404", description="Not Found"),
 * @OA\Response(response="500", description="Internal Server Error"),
 *     @OA\Parameter(in="header", name="User-Agent", required=false, @OA\Schema(type="string")
 * ),
 *     tags={"Gestion Poche Sanguin"},
*),


 * @OA\POST(
 *     path="/api/poche-sanguins",
 *     summary="Ajouter poche pour donneur externe",
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
 *                     @OA\Property(property="nom", type="string"),
 *                     @OA\Property(property="prenom", type="string"),
 *                     @OA\Property(property="telephone", type="string"),
 *                     @OA\Property(property="adresse", type="string"),
 *                     @OA\Property(property="sexe", type="string"),
 *                     @OA\Property(property="date_naiss", type="string"),
 *                     @OA\Property(property="profession", type="string"),
 *                     @OA\Property(property="groupe_sanguin", type="string"),
 *                     @OA\Property(property="date_prelevement", type="string"),
 *                     @OA\Property(property="banque_sang_id", type="integer"),
 *                     @OA\Property(property="rendez_vouse_id", type="string", format="binary"),
 *                 },
 *             ),
 *         ),
 *     ),
 *     tags={"Gestion Poche Sanguin"},
*),


 * @OA\GET(
 *     path="/api/poche-sanguins",
 *     summary="Liste des poches sanguins",
 *     description="",
 *         security={
 *    {       "BearerAuth": {}}
 *         },
 * @OA\Response(response="200", description="OK"),
 * @OA\Response(response="404", description="Not Found"),
 * @OA\Response(response="500", description="Internal Server Error"),
 *     @OA\Parameter(in="header", name="User-Agent", required=false, @OA\Schema(type="string")
 * ),
 *     tags={"Gestion Poche Sanguin"},
*),


*/

 class GestionPocheSanguinAnnotationController {}
