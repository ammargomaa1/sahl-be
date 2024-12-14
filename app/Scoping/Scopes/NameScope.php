<?php

namespace App\Scoping\Scopes;

use App\Scoping\Contracts\Scope;
use Illuminate\Database\Eloquent\Builder;

class NameScope implements Scope
{
    public function apply(Builder $builder,$value)
    {
        return $builder->where('name_ar' , 'ILIKE' , '%' . $value . '%')
        ->orWhere('name_en','ILIKE', '%' . $value . '%');
    }
}
