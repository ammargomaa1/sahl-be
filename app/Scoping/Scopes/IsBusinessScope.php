<?php

namespace App\Scoping\Scopes;
use App\Scoping\Contracts\Scope;
use Illuminate\Database\Eloquent\Builder;

class IsBusinessScope implements Scope
{
    public function apply(Builder $builder,$value)
    {
        return $builder->where('is_business',$value);
    }
}
