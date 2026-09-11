<?php

namespace App\Traits;

use App\Models\School;
use App\Scopes\TenantScope;
use Illuminate\Support\Facades\Auth;

trait BelongsToSchool
{
    public static function bootBelongsToSchool(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (empty($model->school_id)) {
                if (session()->has('active_school_id')) {
                    $model->school_id = session('active_school_id');
                } elseif (session()->has('impersonate_school_id')) {
                    $model->school_id = session('impersonate_school_id');
                } elseif (Auth::check() && Auth::user()->school_id) {
                    $model->school_id = Auth::user()->school_id;
                } elseif ($school = \App\Services\TenantContext::getSchool()) {
                    $model->school_id = $school->id;
                }
            }

            if (empty($model->branch_id) && in_array('branch_id', $model->getFillable())) {
                if (session()->has('active_branch_id')) {
                    $model->branch_id = session('active_branch_id');
                } elseif (Auth::check() && Auth::user()->branch_id) {
                    $model->branch_id = Auth::user()->branch_id;
                } elseif ($branch = \App\Services\TenantContext::getBranch()) {
                    $model->branch_id = $branch->id;
                }
            }
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
