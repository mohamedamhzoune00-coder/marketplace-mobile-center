<?php

namespace App\Policies;

use App\Models\JournalAudit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JournalAuditPolicy
{
    use HandlesAuthorization;

    // عرض جميع السجلات
    public function viewAny(User $user)
    {
        return $user->role === 'super_admin';
    }

    // عرض سجل واحد
    public function view(User $user, JournalAudit $journalAudit)
    {
        return $user->role === 'super_admin';
    }

    // إنشاء سجل
    public function create(User $user)
    {
        // النظام هو اللي كيخلق السجلات، ماشي المستخدم
        return false;
    }

    // تعديل سجل
    public function update(User $user, JournalAudit $journalAudit)
    {
        return false;
    }

    // حذف سجل
    public function delete(User $user, JournalAudit $journalAudit)
    {
        return false;
    }

    // استرجاع سجل
    public function restore(User $user, JournalAudit $journalAudit)
    {
        return false;
    }

    // حذف نهائي
    public function forceDelete(User $user, JournalAudit $journalAudit)
    {
        return false;
    }
}