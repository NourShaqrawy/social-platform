<?php

namespace App\Traits;

use App\Models\Feeling;

trait AssignsFeeling
{
    /**
     * ربط شعور بمنشور حسب الاسم أو إنشاءه إن لم يكن موجودًا
     *
     * @param string|null $name
     * @param string|null $emoji
     * @param string|null $description
     * @return Feeling
     */
    public function assignFeeling(?string $name=null, ?string $emoji = null, ?string $description = null): Feeling
    {
        return Feeling::firstOrCreate(
            ['name' => $name],
            ['emoji' => $emoji, 'description' => $description]
        );
    }
}
