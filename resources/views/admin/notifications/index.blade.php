@extends('admin.layout')

@section('admin-content')
<div class="space-y-6">
    <section class="admin-content-card overflow-hidden">
        <div class="flex flex-col gap-4 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[.18em] text-[#0B6E4F]">Centre d’activité</p>
                <h2 class="mt-1 text-xl font-black text-slate-900">Notifications</h2>
                <p class="mt-1 text-sm text-slate-500">Suivez les événements importants de la plateforme.</p>
            </div>
            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('admin.notifications.read-all') }}" onsubmit="return confirm('Marquer toutes les notifications comme lues ?')">
                    @csrf
                    <button type="submit" class="admin-btn inline-flex items-center justify-center gap-2 rounded-xl bg-[#0B6E4F] px-4 py-2.5 text-sm font-bold text-white">
                        <i class="fa-solid fa-check-double" aria-hidden="true"></i> Tout marquer comme lu
                    </button>
                </form>
            @endif
        </div>
        <div class="flex flex-wrap gap-3 border-b border-slate-100 bg-slate-50/70 px-5 py-3 sm:px-6">
            <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1.5 text-xs font-bold text-slate-600 ring-1 ring-slate-200"><i class="fa-regular fa-bell text-[#0B6E4F]" aria-hidden="true"></i> {{ $notifications->total() }} notifications</span>
            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800 ring-1 ring-emerald-100"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> {{ $unreadCount }} non lues</span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($notifications as $notification)
                @php
                    $payload = $notification->data ?? [];
                    $title = $payload['title'] ?? 'Notification MonSiège';
                    $message = $payload['message'] ?? 'Un événement a été enregistré.';
                    $icon = $payload['icon'] ?? 'fa-bell';
                    $target = $payload['url'] ?? route('admin.notifications.index');
                    $isRead = $notification->read_at !== null;
                @endphp
                <article class="flex gap-3 px-5 py-5 transition hover:bg-slate-50/80 sm:gap-4 sm:px-6 {{ $isRead ? '' : 'bg-emerald-50/35' }}">
                    <div class="flex h-11 w-11 flex-none items-center justify-center rounded-2xl {{ $isRead ? 'bg-slate-100 text-slate-500' : 'bg-emerald-100 text-[#0B6E4F]' }}">
                        <i class="fa-solid {{ $icon }} text-lg" aria-hidden="true"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-extrabold text-slate-900">{{ $title }}</h3>
                            @unless ($isRead)
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-emerald-800">Nouveau</span>
                            @endunless
                        </div>
                        <p class="mt-1 text-sm leading-6 text-slate-600">{{ $message }}</p>
                        <p class="mt-2 text-xs font-medium text-slate-400"><i class="fa-regular fa-clock mr-1" aria-hidden="true"></i>{{ $notification->created_at?->timezone(config('app.timezone'))->format('d/m/Y à H:i') }}</p>
                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            <a href="{{ $target }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-[#0B6E4F] hover:text-[#073b2e]">Ouvrir <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                            @unless ($isRead)
                                <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-slate-500 underline decoration-slate-300 underline-offset-4 hover:text-slate-900">Marquer comme lue</button>
                                </form>
                            @endunless
                            <form method="POST" action="{{ route('admin.notifications.destroy', $notification->id) }}" onsubmit="return confirm('Supprimer cette notification ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-red-600 underline decoration-red-200 underline-offset-4 hover:text-red-800">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="px-6 py-16 text-center">
                    <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-emerald-50 text-[#0B6E4F]"><i class="fa-regular fa-bell-slash text-2xl" aria-hidden="true"></i></span>
                    <h3 class="mt-4 font-extrabold text-slate-900">Aucune notification pour le moment</h3>
                    <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">Les événements créés depuis le site apparaîtront ici lorsque les notifications seront activées.</p>
                </div>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div class="border-t border-slate-100 px-5 py-4 sm:px-6">{{ $notifications->links() }}</div>
        @endif
    </section>
</div>
@endsection