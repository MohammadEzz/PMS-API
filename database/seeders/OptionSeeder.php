<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('options')->truncate();
        DB::table('options')->insert(
                [
                    ["name"=>"g", "type"=>"dose", "parent" => 0, "order"=>0, "editable"=>0],
                    ["name"=>"mg", "type"=>"dose", "parent" => 0, "order"=>1, "editable"=>0],
                    ["name"=>"mcg", "type"=>"dose", "parent" => 0, "order"=>2, "editable"=>0],
                    ["name"=>"g/ml", "type"=>"dose", "parent" => 0, "order"=>3, "editable"=>0],
                    ["name"=>"mg/ml", "type"=>"dose", "parent" => 0, "order"=>4, "editable"=>0],

                    ["name"=>"Capsules", "type"=>"dosage_forms", "parent" => 0, "order"=>0, "editable"=>0],
                    ["name"=>"Pills", "type"=>"dosage_forms", "parent" => 0, "order"=>1, "editable"=>0],
                    ["name"=>"Injection", "type"=>"dosage_forms", "parent" => 0, "order"=>2, "editable"=>0],
                    ["name"=>"Spray", "type"=>"dosage_forms", "parent" => 0, "order"=>3, "editable"=>0],
                    ["name"=>"Dragee", "type"=>"dosage_forms", "parent" => 0, "order"=>4, "editable"=>0],
                    ["name"=>"Syrup", "type"=>"dosage_forms", "parent" => 0, "order"=>5, "editable"=>0],
                    ["name"=>"Suspension", "type"=>"dosage_forms", "parent" => 0, "order"=>6, "editable"=>0],
                    ["name"=>"Emulsion", "type"=>"dosage_forms", "parent" => 0, "order"=>7, "editable"=>0],
                    ["name"=>"Granules", "type"=>"dosage_forms", "parent" => 0, "order"=>8, "editable"=>0],
                    ["name"=>"Ointment", "type"=>"dosage_forms", "parent" => 0, "order"=>9, "editable"=>0],
                    ["name"=>"Mixture", "type"=>"dosage_forms", "parent" => 0, "order"=>10, "editable"=>0],
                    ["name"=>"Aerosol Spray", "type"=>"dosage_forms", "parent" => 0, "order"=>11, "editable"=>0],
                    ["name"=>"Drops", "type"=>"dosage_forms", "parent" => 0, "order"=>12, "editable"=>0],
                    ["name"=>"Powder", "type"=>"dosage_forms", "parent" => 0, "order"=>13, "editable"=>0],
                    ["name"=>"Cream", "type"=>"dosage_forms", "parent" => 0, "order"=>14, "editable"=>0],
                    ["name"=>"Suppositqries", "type"=>"dosage_forms", "parent" => 0, "order"=>15, "editable"=>0],
                    ["name"=>"Gel", "type"=>"dosage_forms", "parent" => 0, "order"=>16, "editable"=>0],
                    ["name"=>"Paste", "type"=>"dosage_forms", "parent" => 0, "order"=>17, "editable"=>0],
                    ["name"=>"Tincture", "type"=>"dosage_forms", "parent" => 0, "order"=>18, "editable"=>0],
                    ["name"=>"Pastille", "type"=>"dosage_forms", "parent" => 0, "order"=>19, "editable"=>0],
                    ["name"=>"Medical Herbs", "type"=>"dosage_forms", "parent" => 0, "order"=>20, "editable"=>0],
                    ["name"=>"Herbal Tea", "type"=>"dosage_forms", "parent" => 0, "order"=>21, "editable"=>0],
                    ["name"=>"Patch", "type"=>"dosage_forms", "parent" => 0, "order"=>22, "editable"=>0],
                    ["name"=>"Accessories", "type"=>"dosage_forms", "parent" => 0, "order"=>23, "editable"=>0],
                    ["name"=>"I.V Infusion", "type"=>"dosage_forms", "parent" => 0, "order"=>24, "editable"=>0],
                    ["name"=>"Tablet", "type"=>"dosage_forms", "parent" => 0, "order"=>25, "editable"=>0],
                    ["name"=>"Effervescent", "type"=>"dosage_forms", "parent" => 0, "order"=>26, "editable"=>0],
                    ["name"=>"Eye Drops", "type"=>"dosage_forms", "parent" => 0, "order"=>27, "editable"=>0],

                    ["name"=>"A", "type"=>"contraindication_level", "parent" => 0, "order"=>0, "editable"=>0],
                    ["name"=>"B", "type"=>"contraindication_level", "parent" => 0, "order"=>1, "editable"=>0],
                    ["name"=>"C", "type"=>"contraindication_level", "parent" => 0, "order"=>2, "editable"=>0],
                    ["name"=>"X", "type"=>"contraindication_level", "parent" => 0, "order"=>3, "editable"=>0],

                    ["name"=>"A", "type"=>"interaction_level", "parent" => 0, "order"=>0, "editable"=>0],
                    ["name"=>"B", "type"=>"interaction_level", "parent" => 0, "order"=>1, "editable"=>0],
                    ["name"=>"C", "type"=>"interaction_level", "parent" => 0, "order"=>2, "editable"=>0],
                    ["name"=>"X", "type"=>"interaction_level", "parent" => 0, "order"=>3, "editable"=>0],

                    ["name"=>"Pregnant", "type"=>"contraindication_category", "parent" => 0, "order"=>0, "editable"=>0],
                    ["name"=>"Gender", "type"=>"contraindication_category", "parent" => 0, "order"=>1, "editable"=>0],
                    ["name"=>"Age", "type"=>"contraindication_category", "parent" => 0, "order"=>2, "editable"=>0],
                    ["name"=>"Active Ingredient", "type"=>"contraindication_category", "parent" => 0, "order"=>3, "editable"=>0],
                    ["name"=>"Other", "type"=>"contraindication_category", "parent" => 0, "order"=>4, "editable"=>0],

                    ["name"=>"Blood Diseases", "type"=>"disease_category", "parent" => 0, "order"=>0, "editable"=>0],
                    ["name"=>"Bone Diseases", "type"=>"disease_category", "parent" => 0, "order"=>1, "editable"=>0],
                    ["name"=>"Cardiovascular Diseases", "type"=>"disease_category", "parent" => 0, "order"=>2, "editable"=>0],
                    ["name"=>"Ear Diseases", "type"=>"disease_category", "parent" => 0, "order"=>3, "editable"=>0],
                    ["name"=>"Endocrine Diseases", "type"=>"disease_category", "parent" => 0, "order"=>4, "editable"=>0],
                    ["name"=>"Eye Diseases", "type"=>"disease_category", "parent" => 0, "order"=>5, "editable"=>0],
                    ["name"=>"Gastrointestinal Diseases", "type"=>"disease_category", "parent" => 0, "order"=>6, "editable"=>0],
                    ["name"=>"Immune Diseases", "type"=>"disease_category", "parent" => 0, "order"=>7, "editable"=>0],
                    ["name"=>"Liver Diseases", "type"=>"disease_category", "parent" => 0, "order"=>8, "editable"=>0],
                    ["name"=>"Mental Diseases", "type"=>"disease_category", "parent" => 0, "order"=>9, "editable"=>0],
                    ["name"=>"Nephrological Diseases", "type"=>"disease_category", "parent" => 0, "order"=>10, "editable"=>0],
                    ["name"=>"Neuronal Diseases", "type"=>"disease_category", "parent" => 0, "order"=>11, "editable"=>0],
                    ["name"=>"Oral Diseases", "type"=>"disease_category", "parent" => 0, "order"=>12, "editable"=>0],
                    ["name"=>"Reproductive Diseases", "type"=>"disease_category", "parent" => 0, "order"=>13, "editable"=>0],
                    ["name"=>"Respiratory Diseases", "type"=>"disease_category", "parent" => 0, "order"=>14, "editable"=>0],
                    ["name"=>"Skin Diseases", "type"=>"disease_category", "parent" => 0, "order"=>15, "editable"=>0],
                    ["name"=>"Smell/Taste Diseases", "type"=>"disease_category", "parent" => 0, "order"=>16, "editable"=>0],
                    ["name"=>"Cancer Diseases", "type"=>"disease_category", "parent" => 0, "order"=>17, "editable"=>0],
                    ["name"=>"Fetal Diseases", "type"=>"disease_category", "parent" => 0, "order"=>18, "editable"=>0],
                    ["name"=>"Genetic Diseases", "type"=>"disease_category", "parent" => 0, "order"=>19, "editable"=>0],
                    ["name"=>"Infectious Diseases", "type"=>"disease_category", "parent" => 0, "order"=>20, "editable"=>0],
                    ["name"=>"Metabolic Diseases", "type"=>"disease_category", "parent" => 0, "order"=>21, "editable"=>0],
                    ["name"=>"Rare Diseases", "type"=>"disease_category", "parent" => 0, "order"=>22, "editable"=>0],

                    ["name"=>"Pending", "type"=>"user_status", "parent" => 0, "order"=>1, "editable"=>0],
                    ["name"=>"Rejected", "type"=>"user_status", "parent" => 0, "order"=>2, "editable"=>0],
                    ["name"=>"Approved", "type"=>"user_status", "parent" => 0, "order"=>3, "editable"=>0],
                    ["name"=>"Suspend", "type"=>"user_status", "parent" => 0, "order"=>4, "editable"=>0],
                    ["name"=>"Archived", "type"=>"user_status", "parent" => 0, "order"=>5, "editable"=>0],
                    ["name"=>"Blocked", "type"=>"user_status", "parent" => 0, "order"=>6, "editable"=>0],
                ]
            );
    }
}
