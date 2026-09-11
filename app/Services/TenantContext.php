<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\School;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TenantContext
{
    protected static ?School $cachedSchool = null;
    protected static ?Branch $cachedBranch = null;

    public static function getSchool(): ?School
    {
        if (self::$cachedSchool) {
            return self::$cachedSchool;
        }

        $user = Auth::user();
        if ($user && $user->school_id) {
            self::$cachedSchool = School::find($user->school_id);
            if (self::$cachedSchool) {
                return self::$cachedSchool;
            }
        }

        $sessionId = Session::get('active_school_id');
        if ($sessionId) {
            self::$cachedSchool = School::find($sessionId);
            if (self::$cachedSchool) {
                return self::$cachedSchool;
            }
        }

        self::$cachedSchool = School::where('status', 'active')->first() ?? School::first();
        return self::$cachedSchool;
    }

    public static function getBranch(): ?Branch
    {
        if (self::$cachedBranch) {
            return self::$cachedBranch;
        }

        $school = self::getSchool();
        if (!$school) {
            return null;
        }

        $sessionBranchId = Session::get('active_branch_id');
        if ($sessionBranchId) {
            $branch = Branch::where('school_id', $school->id)->find($sessionBranchId);
            if ($branch) {
                self::$cachedBranch = $branch;
                return self::$cachedBranch;
            }
        }

        $user = Auth::user();
        if ($user && $user->branch_id) {
            $branch = Branch::where('school_id', $school->id)->find($user->branch_id);
            if ($branch) {
                self::$cachedBranch = $branch;
                return self::$cachedBranch;
            }
        }

        // Default to main branch or first branch of school
        $branch = Branch::where('school_id', $school->id)
            ->where('is_main', true)
            ->first() 
            ?? Branch::where('school_id', $school->id)->first();

        if ($branch) {
            self::$cachedBranch = $branch;
            Session::put('active_branch_id', $branch->id);
        }

        return self::$cachedBranch;
    }

    public static function setBranch(int $branchId): bool
    {
        $school = self::getSchool();
        if (!$school) {
            return false;
        }

        $branch = Branch::where('school_id', $school->id)->find($branchId);
        if (!$branch) {
            return false;
        }

        Session::put('active_branch_id', $branch->id);
        self::$cachedBranch = $branch;
        return true;
    }

    public static function setSchool(int $schoolId): bool
    {
        $school = School::find($schoolId);
        if (!$school) {
            return false;
        }

        Session::put('active_school_id', $school->id);
        self::$cachedSchool = $school;

        // Reset branch to the new school's main branch
        $mainBranch = $school->mainBranch()->first() ?? $school->branches()->first();
        if ($mainBranch) {
            Session::put('active_branch_id', $mainBranch->id);
            self::$cachedBranch = $mainBranch;
        } else {
            Session::forget('active_branch_id');
            self::$cachedBranch = null;
        }

        return true;
    }

    public static function availableBranches()
    {
        $school = self::getSchool();
        return $school ? Branch::where('school_id', $school->id)->get() : collect();
    }

    public static function availableSchools()
    {
        return School::where('status', 'active')->get();
    }

    public static function currencySymbol(): string
    {
        $school = self::getSchool();
        return $school && $school->currency_symbol ? $school->currency_symbol : '₹';
    }
}