<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Personnel;
use App\Models\Postulant;
use App\Models\Examinateur;
use App\Models\PresidentSousCommission;
use App\Models\MembreConseil;
use App\Models\PresidentConseil;
use App\Models\SousCommission;
use App\Models\Demande;
use App\Models\Publication;
use App\Models\Journal;
use App\Models\Avis;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $password = Hash::make('123456789');

        // --- 1. ADMIN ---
        Personnel::create([
            'nom' => 'Admin', 'prenom' => 'System', 'email' => 'admin@univ.dz',
            'password' => $password, 'role' => 'admin', 'departement' => 'Informatique'
        ]);

        // --- 2. PRÉSIDENT CONSEIL ---
        $pres_council = Personnel::create([
            'nom' => 'Khelifi', 'prenom' => 'Mohamed Larbi', 'email' => 'president@univ.dz',
            'password' => $password, 'role' => 'president-council', 'grade' => 'Professeur'
        ]);
        PresidentConseil::create(['id_personnel' => $pres_council->id, 'mandat' => '2024-2026', 'date_debut' => '2024-01-01']);

        // Créer le Conseil
        $conseil = DB::table('conseil_scientifiques')->insertGetId([
            'nom_conseil' => 'Conseil Scientifique Central', 'annee_creation' => 2024,
            'institution' => 'Université des Sciences', 'id_president' => $pres_council->id
        ]);

        // --- 3. PRÉSIDENT SOUS-COMMISSION ---
        $pres_sub = Personnel::create([
            'nom' => 'Bouziane', 'prenom' => 'Karim', 'email' => 'subcomm@univ.dz',
            'password' => $password, 'role' => 'president-sub', 'grade' => 'Professeur'
        ]);
        PresidentSousCommission::create(['id_personnel' => $pres_sub->id, 'date_nomination' => '2024-02-01']);

        // Créer la Sous-Commission
        $sc = SousCommission::create([
            'nom' => 'Informatique & IA', 'domaine' => 'Informatique', 'id_conseil' => $conseil,
            'id_president_sc' => $pres_sub->id, 'date_creation' => '2024-02-01'
        ]);

        // --- 4. EXAMINATEURS ---
        $exam_data = [
            ['nom' => 'El Moussaoui', 'prenom' => 'Hamid', 'email' => 'exam1@univ.dz'],
            ['nom' => 'Benbouzid', 'prenom' => 'Fatima', 'email' => 'exam2@univ.dz'],
            ['nom' => 'Boudour', 'prenom' => 'Rachid', 'email' => 'exam3@univ.dz'],
        ];

        foreach ($exam_data as $ex) {
            $p = Personnel::create(array_merge($ex, ['password' => $password, 'role' => 'examiner', 'grade' => 'Professeur']));
            Examinateur::create(['id_personnel' => $p->id, 'num_these' => 'TH-'.rand(100,999)]);
        }

        // --- 5. POSTULANTS ---
        $postulant1 = Personnel::create([
            'nom' => 'Boudiaf', 'prenom' => 'Mehdi', 'email' => 'postulant@univ.dz',
            'password' => $password, 'role' => 'postulant', 'grade' => 'MCA'
        ]);
        Postulant::create(['id_personnel' => $postulant1->id, 'laboratoire' => 'LRIA', 'grade_recherche' => 'Chercheur']);

        // --- 6. WORKFLOW : UNE DEMANDE COMPLÈTE ---
        $demande = Demande::create([
            'id_postulant' => $postulant1->id, 'id_sous_comm' => $sc->id,
            'date_demande' => now(), 'statut' => 'proposition'
        ]);

        $pub = Publication::create([
            'id_demande' => $demande->id, 'titre' => 'Deep Learning in Medical Imaging',
            'auteur_principal' => 'M. Boudiaf', 'date_publication' => '2023-11-20',
            'resume' => 'An analysis of neural networks applied to MRI scans.'
        ]);

        Journal::create([
            'id_publication' => $pub->id, 'nom_journal' => 'IEEE Medical Imaging',
            'issn' => '0278-0062', 'facteur_impact' => '10.6'
        ]);

        // Ajouter des Avis fictifs
        $examiners = Personnel::where('role', 'examiner')->get();
        foreach($examiners as $ex) {
            Avis::create([
                'id_demande' => $demande->id, 'id_examinateur' => $ex->id,
                'resultat' => 'favorable', 'commentaire' => 'Excellent travail scientifique.',
                'date_avis' => now(), 'recommandation' => 'Recommandé sans réserve'
            ]);
        }
    }
}
