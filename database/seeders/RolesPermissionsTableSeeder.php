<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // //permissions concernant l'admin
        //  Permission::create(['name' => "Ajouter une structure"]);
        //  Permission::create(['name' => "Modifier une structure"]);
        //  Permission::create(['name' => "Supprimer une structure"]);
        //  Permission::create(['name' => "Voir la liste des structures"]);
        //  Permission::create(['name' => "Voir les donneurs d'une structure"]);
        //  Permission::create(['name' => "Voir les annonces des structures"]);
        //  Permission::create(['name' => "Voir les stocks des structures"]);
        //  Permission::create(['name' => "Voir les utilisateus inscrits dans la plateforme"]);

        // //permissions concernant les structures
        // Permission::create(['name' => "Ajouter une annonce"]);
        // Permission::create(['name' => "Modifier une annonce"]);
        // Permission::create(['name' => "Supprimer une annonce"]);
        // Permission::create(['name' => "Voir la liste des annonces"]);
        // Permission::create(['name' => "Voir details d'une annonce"]);
        // Permission::create(['name' => "Voir son stock"]);
        // Permission::create(['name' => 'valider un don']);
        // Permission::create(['name' => 'ne pas valider un don']);

        // //permissions concernant les utilisateurs simple
        // Permission::create(['name' => "Voir les annonces des structures"]);
        // Permission::create(['name' => "Inscrire à une annonce"]);
        // Permission::create(['name' => "Se désinscrire d'une annonce"]);
        // Permission::create(['name' => "voir le nombre d'inscrit à une annonce"]);
        // Permission::create(['name' => "mettre à jour son profil"]);
        

        // Attributions des permissions aux roles
       $role = Role::create(['name' => "admin"]);
        // $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => "structure"]);
        // $role->givePermissionTo(
        //                         "Ajouter une annonce",
        //                         "Modifier une annonce",
        //                         "Supprimer une annonce",
        //                         "Voir la liste des annonces",
        //                         "Voir details d'une annonce",
        //                         "Voir son stock",
        //                         "valider un don",
        //                         "ne pas valider un don"
        // );

        $role = Role::create(['name' => "utilisateur_simple"]);
        // $role->givePermissionTo(
        //                         "Voir les annonces des structures",
        //                         "Inscrire à une annonce",
        //                         "Se désinscrire d'une annonce",
        //                         "voir le nombre d'inscrit à une annonce",
        //                         "mettre à jour son profil"
        // );
    }

}
