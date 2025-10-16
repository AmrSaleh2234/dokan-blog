<?php

declare(strict_types=1);

namespace Modules\Category\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Category\Entities\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tech = Category::create(['name' => 'Technology']);
        $mobile = Category::create(['name' => 'Mobile', 'parent_id' => $tech->id]);
        $android = Category::create(['name' => 'Android', 'parent_id' => $mobile->id]);
        $ios = Category::create(['name' => 'iOS', 'parent_id' => $mobile->id]);
        $web = Category::create(['name' => 'Web Development', 'parent_id' => $tech->id]);
        
        $lifestyle = Category::create(['name' => 'Lifestyle']);
        $travel = Category::create(['name' => 'Travel', 'parent_id' => $lifestyle->id]);
        $food = Category::create(['name' => 'Food', 'parent_id' => $lifestyle->id]);
    }
}
