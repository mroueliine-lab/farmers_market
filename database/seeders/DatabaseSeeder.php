<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Product;
use App\Models\Farmer;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Debt;
use App\Models\Repayment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Settings ─────────────────────────────────────────────
        Setting::create(['key' => 'interest_rate',  'value' => '30']);
        Setting::create(['key' => 'commodity_rate', 'value' => '1000']);

        // ── Users ─────────────────────────────────────────────────
        User::create([
            'name'     => 'Administrateur',
            'email'    => 'admin@marche.ci',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Superviseur Koné',
            'email'    => 'superviseur@marche.ci',
            'password' => Hash::make('password'),
            'role'     => 'supervisor',
        ]);

        $op1 = User::create([
            'name'     => 'Opérateur Touré',
            'email'    => 'operateur1@marche.ci',
            'password' => Hash::make('password'),
            'role'     => 'operator',
        ]);

        $op2 = User::create([
            'name'     => 'Opérateur Bamba',
            'email'    => 'operateur2@marche.ci',
            'password' => Hash::make('password'),
            'role'     => 'operator',
        ]);

        // ── Categories (2 levels) ─────────────────────────────────
        $pesticides  = Category::create(['name' => 'Pesticides',  'parent_id' => null]);
        $engrais     = Category::create(['name' => 'Engrais',     'parent_id' => null]);
        $semences    = Category::create(['name' => 'Semences',    'parent_id' => null]);

        $herbicides   = Category::create(['name' => 'Herbicides',          'parent_id' => $pesticides->id]);
        $insecticides = Category::create(['name' => 'Insecticides',        'parent_id' => $pesticides->id]);
        $npk          = Category::create(['name' => 'Engrais NPK',         'parent_id' => $engrais->id]);
        $organique    = Category::create(['name' => 'Engrais Organiques',  'parent_id' => $engrais->id]);
        $semMais      = Category::create(['name' => 'Semences Maïs',       'parent_id' => $semences->id]);
        $semRiz       = Category::create(['name' => 'Semences Riz',        'parent_id' => $semences->id]);

        // ── Products ──────────────────────────────────────────────
        $p1 = Product::create(['name' => 'Kalach 360 SL',     'description' => 'Herbicide systémique à base de glyphosate.',         'price_fcfa' => 3500,  'category_id' => $herbicides->id]);
        $p2 = Product::create(['name' => 'Primextra Gold',    'description' => 'Herbicide sélectif pour maïs et sorgho.',            'price_fcfa' => 4200,  'category_id' => $herbicides->id]);
        $p3 = Product::create(['name' => 'Lambdacal 36 EC',   'description' => 'Insecticide contre les ravageurs du coton.',         'price_fcfa' => 2800,  'category_id' => $insecticides->id]);
        $p4 = Product::create(['name' => 'Deltamethrine 25',  'description' => 'Insecticide à large spectre pour toutes cultures.',  'price_fcfa' => 3200,  'category_id' => $insecticides->id]);
        $p5 = Product::create(['name' => 'NPK 15-15-15',      'description' => 'Engrais équilibré pour toutes cultures.',           'price_fcfa' => 18000, 'category_id' => $npk->id]);
        $p6 = Product::create(['name' => 'Urée 46%',          'description' => 'Engrais azoté pour la croissance des céréales.',    'price_fcfa' => 15000, 'category_id' => $npk->id]);
        $p7 = Product::create(['name' => 'Compost Naturel',   'description' => 'Compost organique enrichi en micro-éléments.',     'price_fcfa' => 8000,  'category_id' => $organique->id]);
        $p8 = Product::create(['name' => 'Maïs EVDT 99 STR',  'description' => 'Semence améliorée résistante à la sécheresse.',    'price_fcfa' => 6500,  'category_id' => $semMais->id]);
        $p9 = Product::create(['name' => 'Riz WITA 9',        'description' => 'Variété haut rendement pour bas-fonds.',           'price_fcfa' => 5500,  'category_id' => $semRiz->id]);

        // ── Farmers ───────────────────────────────────────────────
        $f1 = Farmer::create([
            'identifier'   => 'FM-001',
            'firstname'    => 'Kouassi',
            'lastname'     => 'Yao',
            'email'        => 'kouassi.yao@mail.ci',
            'phone_number' => '0701234567',
            'credit_limit' => 150000,
        ]);

        $f2 = Farmer::create([
            'identifier'   => 'FM-002',
            'firstname'    => 'Aminata',
            'lastname'     => 'Coulibaly',
            'email'        => 'aminata.c@mail.ci',
            'phone_number' => '0702345678',
            'credit_limit' => 200000,
        ]);

        $f3 = Farmer::create([
            'identifier'   => 'FM-003',
            'firstname'    => 'Sékou',
            'lastname'     => 'Traoré',
            'email'        => null,
            'phone_number' => '0703456789',
            'credit_limit' => 100000,
        ]);

        // ── Transactions ──────────────────────────────────────────

        // T1 — cash — f1
        $t1 = Transaction::create([
            'farmer_id' => $f1->id, 'operator_id' => $op1->id,
            'total_price_fcfa' => 21500, 'payment_method' => 'cash',
            'interest_rate' => 0, 'credited_amount_fcfa' => 0,
        ]);
        TransactionItem::create(['transaction_id' => $t1->id, 'product_id' => $p5->id, 'quantity' => 1, 'unit_price_fcfa' => 18000]);
        TransactionItem::create(['transaction_id' => $t1->id, 'product_id' => $p3->id, 'quantity' => 1, 'unit_price_fcfa' => 2800]);

        // T2 — credit — f1 (2 bags of maize seeds, 30% interest → 16 900 FCFA)
        $credited2 = 13000 * 1.30;
        $t2 = Transaction::create([
            'farmer_id' => $f1->id, 'operator_id' => $op1->id,
            'total_price_fcfa' => 13000, 'payment_method' => 'credit',
            'interest_rate' => 30, 'credited_amount_fcfa' => $credited2,
        ]);
        TransactionItem::create(['transaction_id' => $t2->id, 'product_id' => $p8->id, 'quantity' => 2, 'unit_price_fcfa' => 6500]);
        Debt::create([
            'transaction_id' => $t2->id, 'farmer_id' => $f1->id,
            'original_amount_fcfa' => $credited2, 'remaining_amount_fcfa' => $credited2, 'status' => 'pending',
        ]);

        // T3 — credit — f2 (NPK + Urée, 30% interest → 42 900 FCFA)
        $credited3 = 33000 * 1.30;
        $t3 = Transaction::create([
            'farmer_id' => $f2->id, 'operator_id' => $op2->id,
            'total_price_fcfa' => 33000, 'payment_method' => 'credit',
            'interest_rate' => 30, 'credited_amount_fcfa' => $credited3,
        ]);
        TransactionItem::create(['transaction_id' => $t3->id, 'product_id' => $p5->id, 'quantity' => 1, 'unit_price_fcfa' => 18000]);
        TransactionItem::create(['transaction_id' => $t3->id, 'product_id' => $p6->id, 'quantity' => 1, 'unit_price_fcfa' => 15000]);
        $debt3 = Debt::create([
            'transaction_id' => $t3->id, 'farmer_id' => $f2->id,
            'original_amount_fcfa' => $credited3, 'remaining_amount_fcfa' => $credited3, 'status' => 'pending',
        ]);

        // T4 — cash — f3
        $t4 = Transaction::create([
            'farmer_id' => $f3->id, 'operator_id' => $op1->id,
            'total_price_fcfa' => 8000, 'payment_method' => 'cash',
            'interest_rate' => 0, 'credited_amount_fcfa' => 0,
        ]);
        TransactionItem::create(['transaction_id' => $t4->id, 'product_id' => $p7->id, 'quantity' => 1, 'unit_price_fcfa' => 8000]);

        // ── Repayment — f2 pays 20 kg → 20 000 FCFA (partial) ────
        $repayment = Repayment::create([
            'farmer_id' => $f2->id, 'operator_id' => $op2->id,
            'kg_received' => 20, 'commodity_rate' => 1000, 'fcfa_value' => 20000,
        ]);
        $debt3->remaining_amount_fcfa -= 20000;
        $debt3->status = 'partial';
        $debt3->repayment_id = $repayment->id;
        $debt3->save();
    }
}
