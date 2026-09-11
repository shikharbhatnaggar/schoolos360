<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        // 1. School Scoping
        if (session()->has('active_school_id')) {
            $builder->where($model->getTable() . '.school_id', session('active_school_id'));
        } elseif (session()->has('impersonate_school_id')) {
            $builder->where($model->getTable() . '.school_id', session('impersonate_school_id'));
        } elseif (Auth::check()) {
            $user = Auth::user();
            if (!$user->isSaasAdmin() && $user->school_id) {
                $builder->where($model->getTable() . '.school_id', $user->school_id);
            }
        }

        // 2. Branch Scoping
        if (session()->has('active_branch_id') && in_array('branch_id', $model->getFillable())) {
            $branchId = session('active_branch_id');
            $builder->where(function($query) use ($model, $branchId) {
                $query->where($model->getTable() . '.branch_id', $branchId)
                      ->orWhereNull($model->getTable() . '.branch_id');
            });
        }
    }
}
