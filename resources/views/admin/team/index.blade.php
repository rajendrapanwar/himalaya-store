@extends('layouts.admin')
@section('title','Team access')
@section('content')
<div class="space-y-7">
 <div class="admin-page-intro"><div><p class="eyebrow">Security</p><h2>Staff roles</h2><p class="max-w-3xl">Give each teammate only the access needed for their work. Every change they make is recorded in the activity log.</p></div></div>
 <div class="grid gap-7 xl:grid-cols-3">
  <form method="POST" action="{{ route('admin.team.store') }}" class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">@csrf
   <h3 class="text-xl font-black">Create staff account</h3><p class="mt-1 text-sm text-stone-500">Share the temporary password through a secure channel.</p>
   <div class="mt-5 space-y-4">
    <div><label for="staffName" class="mb-1 block text-sm font-bold">Name</label><input id="staffName" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-stone-300 px-4 py-3"></div>
    <div><label for="staffEmail" class="mb-1 block text-sm font-bold">Email</label><input id="staffEmail" type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-stone-300 px-4 py-3"></div>
    <div><label for="staffRole" class="mb-1 block text-sm font-bold">Role</label><select id="staffRole" name="role" class="w-full rounded-xl border border-stone-300 px-4 py-3">@foreach(['manager','catalogue','fulfillment','support','administrator'] as $role)<option value="{{ $role }}">{{ ucfirst($role) }}</option>@endforeach</select></div>
    <div><label for="staffPassword" class="mb-1 block text-sm font-bold">Temporary password</label><input id="staffPassword" type="password" name="password" required autocomplete="new-password" class="w-full rounded-xl border border-stone-300 px-4 py-3"><p class="mt-1 text-xs text-stone-500">12+ characters, upper/lowercase letters and a number.</p></div>
    <div><label for="staffPasswordConfirmation" class="mb-1 block text-sm font-bold">Confirm password</label><input id="staffPasswordConfirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-xl border border-stone-300 px-4 py-3"></div>
   </div>@if($errors->any())<div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
   <button class="admin-action admin-action-primary mt-5 w-full">Create staff account</button>
  </form>
  <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white shadow-sm xl:col-span-2"><table><thead><tr><th>Staff member</th><th>Role</th><th>Access</th></tr></thead><tbody>
   @forelse($members as $member)<tr><td><b>{{ $member->name }}</b><br><small class="text-stone-500">{{ $member->email }}</small></td><td>{{ str($member->role ?: 'administrator')->title() }}</td><td>@if($member->is(auth()->user()))<span class="text-sm text-stone-500">Current account</span>@else<form method="POST" action="{{ route('admin.team.update',$member) }}" class="flex gap-2">@csrf @method('PATCH')<select name="role" aria-label="Role for {{ $member->name }}" class="rounded-lg border border-stone-300 px-3 py-2">@foreach(['administrator','manager','catalogue','fulfillment','support','customer'] as $role)<option value="{{ $role }}" @selected($member->role===$role)>{{ ucfirst($role) }}</option>@endforeach</select><button class="admin-action admin-action-primary admin-action-sm">Save</button></form>@endif</td></tr>@empty<tr><td colspan="3" class="p-10 text-center text-stone-500">No staff accounts found.</td></tr>@endforelse
  </tbody></table></div>
 </div>
</div>
@endsection
