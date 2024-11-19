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
 *     path="/api/annonces/{id}",
 *     summary="Detail d'une annonce",
 *     description="",
 *         security={
 *    {       "BearerAuth": {}}
 *         },
 * @OA\Response(response="200", description="OK"),
 * @OA\Response(response="404", description="Not Found"),
 * @OA\Response(response="500", description="Internal Server Error"),
 *     @OA\Parameter(in="header", name="User-Agent", required=false, @OA\Schema(type="string")
 * ),
 *     tags={"Gestion Annonce"},
*),


 * @OA\DELETE(
 *     path="/api/annonces/{id}",
 *     summary="Supprimer une annonce",
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
 *     tags={"Gestion Annonce"},
*),


 * @OA\PUT(
 *     path="/api/annonces/{id}",
 *     summary="Modifier une annonce",
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
 *                     @OA\Property(property="titre", type="string"),
 *                     @OA\Property(property="type_annonce", type="string"),
 *                     @OA\Property(property="nom_lieu", type="string"),
 *                     @OA\Property(property="adresse_lieu", type="string"),
 *                     @OA\Property(property="date_debut", type="string"),
 *                     @OA\Property(property="date_fin", type="string"),
 *                     @OA\Property(property="heure_debut", type="string"),
 *                     @OA\Property(property="heure_fin", type="string"),
 *                     @OA\Property(property="groupe_sanguin_requis", type="string"),
 *                     @OA\Property(property="nombre_poches_vise", type="integer"),
 *                     @OA\Property(property="description", type="string"),
 *                     @OA\Property(property="contact_responsable", type="string"),
 *                 },
 *             ),
 *         ),
 *     ),
 *     tags={"Gestion Annonce"},
*),


 * @OA\POST(
 *     path="/api/annonces",
 *     summary="Ajouter une annonce",
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
 *                     @OA\Property(property="titre", type="string"),
 *                     @OA\Property(property="type_annonce", type="string"),
 *                     @OA\Property(property="nom_lieu", type="string"),
 *                     @OA\Property(property="adresse_lieu", type="string"),
 *                     @OA\Property(property="date_debut", type="string"),
 *                     @OA\Property(property="date_fin", type="string"),
 *                     @OA\Property(property="heure_debut", type="string"),
 *                     @OA\Property(property="heure_fin", type="string"),
 *                     @OA\Property(property="groupe_sanguin_requis", type="string"),
 *                     @OA\Property(property="nombre_poches_vise", type="integer"),
 *                     @OA\Property(property="description", type="string"),
 *                     @OA\Property(property="contact_responsable", type="string"),
 *                 },
 *             ),
 *         ),
 *     ),
 *     tags={"Gestion Annonce"},
*),


 * @OA\GET(
 *     path="/api/annonces",
 *     summary="Liste des annonces",
 *     description="",
 *         security={
 *    {       "BearerAuth": {}}
 *         },
 * @OA\Response(response="200", description="OK"),
 * @OA\Response(response="404", description="Not Found"),
 * @OA\Response(response="500", description="Internal Server Error"),
 *     @OA\Parameter(in="header", name="User-Agent", required=false, @OA\Schema(type="string")
 * ),
 *     tags={"Gestion Annonce"},
*),


*/

 class GestionAnnonceAnnotationController {}
