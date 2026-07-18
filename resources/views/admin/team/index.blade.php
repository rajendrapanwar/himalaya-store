@extends('layouts.admin')
@section('title','Team access')
@section('content')
<div class="space-y-7">
 <div><p class="text-sm font-bold uppercase tracking-widest text-brand-700">Security</p><h2 class="mt-1 text-3xl font-black">Staff roles</h2><p class="mt-2 max-w-3xl text-stone-500">Give each teammate only the access needed for their work. Mutating admin actions are recorded in the audit log.</p></div>
 <div class="grid gap-7 xl:grid-cols-3">
  <form method="POST" action="{{ route('admin.team.store') }}" class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">@csrf
   <h3 class="text-xl font-black">Create staff account</h3><p class="mt-1 text-sm text-stone-500">Share the temporary password through a secure channel.</p>
   <div class="mt-5 space-y-4">
    <div><label class="mb-1 block text-sm font-bold">Name</label><input name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-stone-300 px-4 py-3"></div>
    <div><label class="mb-1 block text-sm font-bold">Email</label><input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-stone-300 px-4 py-3"></div>
    <div><label class="mb-1 block text-sm font-bold">Role</label><select name="role" class="w-full rounded-xl border border-stone-300 px-4 py-3">@foreach(['manager','catalogue','fulfillment','support','administrator'] as $role)<option value="{{ $role }}">{{ ucfirst($role) }}</option>@endforeach</select></div>
    <div><label class="mb-1 block text-sm font-bold">Temporary password</label><input type="password" name="password" required class="w-full rounded-xl border border-stone-300 px-4 py-3"><p class="mt-1 text-xs text-stone-500">12+ characters, upper/lowercase letters and a number.</p></div>
    <div><label class="mb-1 block text-sm font-bold">Confirm password</label><input type="password" name="password_confirmation" required class="w-full rounded-xl border border-stone-300 px-4 py-3"></div>
   </div>@if($errors->any())<div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
   <button class="mt-5 w-full rounded-xl bg-brand-800 px-4 py-3 font-bold text-white">Create staff account</button>
  </form>
  <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white shadow-sm xl:col-span-2"><table><thead><tr><th>Staff member</th><th>Role</th><th>Access</th></tr></thead><tbody>
   @forelse($members as $member)<tr><td><b>{{ $member->name }}</b><br><small class="text-stone-500">{{ $member->email }}</small></td><td>{{ str($member->role ?: 'administrator')->title() }}</td><td>@if($member->is(auth()->user()))<span class="text-sm text-stone-500">Current account</span>@else<form method="POST" action="{{ route('admin.team.update',$member) }}" class="flex gap-2">@csrf @method('PATCH')<select name="role" class="rounded-lg border border-stone-300 px-3 py-2">@foreach(['administrator','manager','catalogue','fulfillment','support','customer'] as $role)<option value="{{ $role }}" @selected($member->role===$role)>{{ ucfirst($role) }}</option>@endforeach</select><button class="rounded-lg bg-brand-800 px-3 py-2 font-bold text-white">Save</button></form>@endif</td></tr>@empty<tr><td colspan="3" class="p-10 text-center text-stone-500">No staff accounts found.</td></tr>@endforelse
  </tbody></table></div>
 </div>
</div>
@endsection
