<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;

class GroupController extends Controller
{
    /**
     * 📃 عرض كل المجموعات الخاصة بالمستخدم الحالي
     */
    public function index()
    {
        $groups = Group::where('owner_id', Auth::id())->latest()->get();

        return response()->json([
            'groups' => $groups,
        ]);
    }

    /**
     * 📥 إنشاء مجموعة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'privacy'     => 'required|in:public,private,hidden',
        ]);

        $group = Group::create([
            'name'        => $request->name,
            'description' => $request->description,
            'privacy'     => $request->privacy,
            'owner_id'    => Auth::id(),
        ]);

        return response()->json([
            'message' => '✅ تم إنشاء المجموعة بنجاح',
            'group'   => $group,
        ]);
    }

    /**
     * 🔍 عرض مجموعة واحدة
     */
    public function show($id)
    {
        $group = Group::where('owner_id', Auth::id())->findOrFail($id);

        return response()->json([
            'group' => $group,
        ]);
    }

    /**
     * ✏️ تعديل مجموعة
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'privacy'     => 'in:public,private,hidden',
        ]);

        $group = Group::where('owner_id', Auth::id())->findOrFail($id);

        $group->update([
            'name'        => $request->name ?? $group->name,
            'description' => $request->description ?? $group->description,
            'privacy'     => $request->privacy ?? $group->privacy,
        ]);

        return response()->json([
            'message' => '✏️ تم تحديث بيانات المجموعة بنجاح',
            'group'   => $group,
        ]);
    }

    /**
     * 🗑️ حذف مجموعة
     */
    public function destroy($id)
    {
        $group = Group::where('owner_id', Auth::id())->findOrFail($id);
        $group->delete();

        return response()->json([
            'message' => '🗑️ تم حذف المجموعة بنجاح',
        ]);
    }
}
