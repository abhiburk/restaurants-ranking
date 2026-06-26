<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
</script>

<template>
    <GuestLayout>
        <!-- LIVE BANNER -->
        <div
            class="flex flex-wrap items-center justify-center gap-x-3 gap-y-1 bg-stone-900 px-4 py-2.5 text-center text-xs font-medium text-white"
        >
            <Badge class="bg-green-600">
                <span class="animate-pulse inline-block h-1.5 w-1.5 rounded-full bg-white"></span>
                Live
            </Badge>
            <span class="text-stone-400">Rankings reset in</span>
            <span
                id="countdown"
                class="rounded bg-white/10 px-2.5 py-0.5 font-bold tracking-wider text-amber-400 tabular-nums"
                >08:24:17</span
            >
            <span class="hidden text-white sm:inline">·</span>
            <span class="hidden text-stone-400 sm:inline"
                ><strong class="text-white">2,841</strong> votes cast today in
                Mumbai</span
            >
        </div>

        <!-- NAVBAR -->
        <nav
            class="sticky top-0 z-50 border-b border-stone-200 bg-stone-50/90 backdrop-blur-md"
        >
            <div
                class="mx-auto flex h-16 max-w-7xl items-center gap-4 px-4 sm:px-6 lg:px-8"
            >
                <a
                    href="/"
                    class="shrink-0 font-display text-xl font-black tracking-tight text-stone-900 italic"
                >
                    Food<span class="text-amber-600">Rank</span>
                </a>

                <div class="hidden items-center gap-1 lg:flex">
                    <Link
                        class="rounded-lg px-4 py-2 text-sm font-semibold text-stone-600 transition-colors hover:bg-amber-50 hover:text-amber-700"
                    >
                        Rankings
                    </Link>
                    <Link
                        class="rounded-lg px-4 py-2 text-sm font-semibold text-stone-600 transition-colors hover:bg-amber-50 hover:text-amber-700"
                    >
                        For Restaurants
                    </Link>
                    <Link
                        class="rounded-lg px-4 py-2 text-sm font-semibold text-stone-600 transition-colors hover:bg-amber-50 hover:text-amber-700"
                    >
                        How It Works
                    </Link>
                </div>

                <div class="ml-auto flex items-center gap-2">
                    <Avatar >
                        <AvatarImage src="https://github.com/shadcn.png" alt="@shadcn" />
                        <AvatarFallback>CN</AvatarFallback>
                    </Avatar>
                </div>
            </div>
        </nav>

        <!-- ══ HERO ══ -->
        <section
            class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8 lg:py-10"
        >
            <div
                class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2 lg:gap-16"
            >
                <div>
                    <div
                        class="mb-5 inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-[11px] font-bold tracking-widest text-amber-700 uppercase"
                    >
                        🏆 Mumbai's #1 Food Discovery Platform
                    </div>
                    <h1
                        class="mb-5 font-display text-5xl leading-tight font-black tracking-tight text-stone-900 sm:text-6xl lg:text-7xl"
                    >
                        Discover the best food in
                        <span class="text-amber-600 italic">your city.</span>
                    </h1>
                    <p
                        class="mb-8 max-w-md text-base leading-relaxed text-stone-500 sm:text-lg"
                    >
                        Real people. Real votes. Rankings reset every midnight —
                        no fake reviews, no paid promotions. Just honest diners
                        deciding where to eat.
                    </p>
                    <div class="mb-10 flex flex-col gap-3 sm:flex-row">
                        <button
                            class="flex items-center justify-center gap-2 rounded-xl bg-amber-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-amber-200 transition-all hover:-translate-y-0.5 hover:bg-amber-700"
                        >
                            Explore Rankings →
                        </button>
                        <button
                            class="flex items-center justify-center gap-2 rounded-xl border border-stone-200 bg-white px-6 py-3.5 text-sm font-semibold text-stone-700 transition-colors hover:border-amber-400 hover:text-amber-600"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <rect x="3" y="3" width="7" height="7" rx="1" />
                                <rect
                                    x="14"
                                    y="3"
                                    width="7"
                                    height="7"
                                    rx="1"
                                />
                                <rect
                                    x="3"
                                    y="14"
                                    width="7"
                                    height="7"
                                    rx="1"
                                />
                                <circle cx="17.5" cy="17.5" r="3" />
                            </svg>
                            How It Works
                        </button>
                    </div>
                    <div class="flex flex-wrap items-center gap-5">
                        <div class="text-center">
                            <span
                                class="block font-display text-2xl font-black text-stone-900"
                                >2.8K</span
                            >
                            <span class="text-xs font-medium text-stone-400"
                                >Votes Today</span
                            >
                        </div>
                        <div class="h-8 w-px bg-stone-200"></div>
                        <div class="text-center">
                            <span
                                class="block font-display text-2xl font-black text-stone-900"
                                >340+</span
                            >
                            <span class="text-xs font-medium text-stone-400"
                                >Restaurants</span
                            >
                        </div>
                        <div class="h-8 w-px bg-stone-200"></div>
                        <div class="text-center">
                            <span
                                class="block font-display text-2xl font-black text-stone-900"
                                >18</span
                            >
                            <span class="text-xs font-medium text-stone-400"
                                >Neighbourhoods</span
                            >
                        </div>
                        <div class="h-8 w-px bg-stone-200"></div>
                        <div class="text-center">
                            <span
                                class="block font-display text-2xl font-black text-emerald-600"
                                >0</span
                            >
                            <span class="text-xs font-medium text-stone-400"
                                >Paid Boosts</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Hero leaderboard card -->
                <!-- lg:rotate-[0.03rad] -->
                <div
                    class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-xl"
                >
                    <div
                        class="flex items-center justify-between border-b border-amber-100 bg-amber-50 px-5 py-3.5"
                    >
                        <span
                            class="text-xs font-bold tracking-widest text-stone-500 uppercase"
                            >🔥 Today's Top Picks</span
                        >
                        <span class="text-xs font-bold text-amber-600"
                            >Live ↻</span
                        >
                    </div>
                    <div id="hero-list"></div>
                </div>
            </div>
        </section>

        <!-- ══ FEATURES STRIP ══ -->
        <div class="border-y border-stone-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    class="grid grid-cols-2 divide-x divide-y divide-stone-100 lg:grid-cols-4 lg:divide-y-0"
                >
                    <div class="flex flex-col gap-3 p-6 sm:p-8">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-lg"
                        >
                            👥
                        </div>
                        <div>
                            <div
                                class="text-sm font-semibold text-stone-900 sm:text-base"
                            >
                                Real People Only
                            </div>
                            <div
                                class="mt-1 text-xs leading-relaxed text-stone-400 sm:text-sm"
                            >
                                Votes from dine-in customers only — no bots, no
                                proxies.
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 p-6 sm:p-8">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-lg"
                        >
                            📈
                        </div>
                        <div>
                            <div
                                class="text-sm font-semibold text-stone-900 sm:text-base"
                            >
                                Live Rankings
                            </div>
                            <div
                                class="mt-1 text-xs leading-relaxed text-stone-400 sm:text-sm"
                            >
                                Updates in real time as votes roll in throughout
                                the day.
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 p-6 sm:p-8">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-lg"
                        >
                            🛡️
                        </div>
                        <div>
                            <div
                                class="text-sm font-semibold text-stone-900 sm:text-base"
                            >
                                No Sign Up
                            </div>
                            <div
                                class="mt-1 text-xs leading-relaxed text-stone-400 sm:text-sm"
                            >
                                Scan QR at your table and vote instantly —
                                nothing to install.
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 p-6 sm:p-8">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-lg"
                        >
                            ⭐
                        </div>
                        <div>
                            <div
                                class="text-sm font-semibold text-stone-900 sm:text-base"
                            >
                                Fair &amp; Honest
                            </div>
                            <div
                                class="mt-1 text-xs leading-relaxed text-stone-400 sm:text-sm"
                            >
                                Zero paid boosts. Every restaurant on equal
                                footing.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ RANKINGS + SIDEBAR ══ -->
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
            <div class="flex flex-col items-start gap-8 lg:flex-row lg:gap-10">
                <!-- Rankings column -->
                <div class="w-full min-w-0 lg:flex-1">
                    <!-- Filter chips -->
                    <div
                        class="mb-6 flex items-center gap-2 overflow-x-auto pb-2"
                    >
                        <button
                            onclick="setFilter(this)"
                            class="filter-chip shrink-0 rounded-full border border-amber-600 bg-amber-600 px-4 py-2 text-xs font-semibold whitespace-nowrap text-white transition-all"
                        >
                            🔥 All
                        </button>
                        <button
                            onclick="setFilter(this)"
                            class="filter-chip shrink-0 rounded-full border border-stone-200 bg-white px-4 py-2 text-xs font-medium whitespace-nowrap text-stone-500 transition-all hover:border-amber-400 hover:text-amber-600"
                        >
                            ☕ Cafes
                        </button>
                        <button
                            onclick="setFilter(this)"
                            class="filter-chip shrink-0 rounded-full border border-stone-200 bg-white px-4 py-2 text-xs font-medium whitespace-nowrap text-stone-500 transition-all hover:border-amber-400 hover:text-amber-600"
                        >
                            🍕 Pizza
                        </button>
                        <button
                            onclick="setFilter(this)"
                            class="filter-chip shrink-0 rounded-full border border-stone-200 bg-white px-4 py-2 text-xs font-medium whitespace-nowrap text-stone-500 transition-all hover:border-amber-400 hover:text-amber-600"
                        >
                            🍜 Asian
                        </button>
                        <button
                            onclick="setFilter(this)"
                            class="filter-chip shrink-0 rounded-full border border-stone-200 bg-white px-4 py-2 text-xs font-medium whitespace-nowrap text-stone-500 transition-all hover:border-amber-400 hover:text-amber-600"
                        >
                            🥗 Healthy
                        </button>
                        <button
                            onclick="setFilter(this)"
                            class="filter-chip shrink-0 rounded-full border border-stone-200 bg-white px-4 py-2 text-xs font-medium whitespace-nowrap text-stone-500 transition-all hover:border-amber-400 hover:text-amber-600"
                        >
                            🍔 Burgers
                        </button>
                        <button
                            onclick="setFilter(this)"
                            class="filter-chip shrink-0 rounded-full border border-stone-200 bg-white px-4 py-2 text-xs font-medium whitespace-nowrap text-stone-500 transition-all hover:border-amber-400 hover:text-amber-600"
                        >
                            🍣 Sushi
                        </button>
                        <select
                            class="ml-auto shrink-0 cursor-pointer rounded-xl border border-stone-200 bg-white px-3 py-2 text-xs font-medium text-stone-700 outline-none focus:border-amber-400"
                        >
                            <option>Top Votes</option>
                            <option>Rising Fast</option>
                            <option>Nearest</option>
                        </select>
                    </div>

                    <!-- Section heading -->
                    <div class="mb-4 flex items-baseline gap-3">
                        <h2
                            class="font-display text-2xl font-black text-stone-900 sm:text-3xl"
                        >
                            Today's Full Rankings
                        </h2>
                        <span class="hidden text-sm text-stone-400 sm:inline"
                            >340 restaurants · Mumbai</span
                        >
                    </div>

                    <!-- Rankings table card -->
                    <div
                        class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm"
                    >
                        <!-- Desktop header -->
                        <div
                            class="hidden items-center gap-4 border-b border-amber-100 bg-amber-50 px-5 py-3 text-[11px] font-bold tracking-widest text-stone-400 uppercase sm:flex"
                        >
                            <div class="w-12 shrink-0 text-center">Rank</div>
                            <div class="flex-1">Restaurant</div>
                            <div class="w-28 shrink-0 text-right">Votes</div>
                        </div>
                        <!-- Rows -->
                        <div id="ranking-list"></div>
                    </div>

                    <div class="mt-6 flex justify-center">
                        <button
                            class="w-full rounded-xl border border-stone-200 bg-white px-8 py-3 text-sm font-semibold text-stone-600 transition-colors hover:border-amber-400 hover:text-amber-600 sm:w-auto"
                        >
                            Load More Restaurants ↓
                        </button>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="flex w-full shrink-0 flex-col gap-5 lg:w-80">
                    <!-- QR Vote -->
                    <div
                        class="relative overflow-hidden rounded-2xl bg-stone-900 p-6 text-white"
                    >
                        <div
                            class="pointer-events-none absolute -top-8 -right-8 h-32 w-32 rounded-full bg-amber-500/10"
                        ></div>
                        <div class="mb-3 text-3xl">📱</div>
                        <h3 class="mb-2 font-display text-lg font-black">
                            At a restaurant right now?
                        </h3>
                        <p class="mb-5 text-sm leading-relaxed text-stone-400">
                            Scan the QR at your table and cast your vote
                            instantly. No account needed.
                        </p>
                        <button
                            class="flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-amber-500"
                        >
                            <svg
                                class="h-3.5 w-3.5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2.2"
                                viewBox="0 0 24 24"
                            >
                                <rect x="3" y="3" width="7" height="7" rx="1" />
                                <rect
                                    x="14"
                                    y="3"
                                    width="7"
                                    height="7"
                                    rx="1"
                                />
                                <rect
                                    x="3"
                                    y="14"
                                    width="7"
                                    height="7"
                                    rx="1"
                                />
                                <circle cx="17.5" cy="17.5" r="3" />
                            </svg>
                            Scan QR Code
                        </button>
                    </div>

                    <!-- Today's stats -->
                    <div
                        class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm"
                    >
                        <div
                            class="border-b border-amber-100 bg-amber-50 px-5 py-3 text-[11px] font-bold tracking-widest text-stone-400 uppercase"
                        >
                            📊 Today in Mumbai
                        </div>
                        <div class="divide-y divide-stone-100">
                            <div
                                class="flex items-center justify-between px-5 py-3 text-sm"
                            >
                                <span class="text-stone-500">Total Votes</span
                                ><span class="font-bold text-amber-600"
                                    >2,841</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between px-5 py-3 text-sm"
                            >
                                <span class="text-stone-500"
                                    >Active Restaurants</span
                                ><span class="font-bold text-stone-800"
                                    >340</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between px-5 py-3 text-sm"
                            >
                                <span class="text-stone-500"
                                    >Top Neighbourhood</span
                                ><span class="font-bold text-stone-800"
                                    >Bandra West</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between px-5 py-3 text-sm"
                            >
                                <span class="text-stone-500">Reset In</span
                                ><span
                                    id="sidebar-timer"
                                    class="font-bold text-amber-600 tabular-nums"
                                    >08:24:17</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between px-5 py-3 text-sm"
                            >
                                <span class="text-stone-500"
                                    >Yesterday's Winner</span
                                ><span class="font-bold text-stone-800"
                                    >Chai Bar ☕</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Biggest movers -->
                    <div
                        class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm"
                    >
                        <div
                            class="border-b border-amber-100 bg-amber-50 px-5 py-3 text-[11px] font-bold tracking-widest text-stone-400 uppercase"
                        >
                            🚀 Biggest Movers Today
                        </div>
                        <div
                            id="movers-list"
                            class="divide-y divide-stone-100 px-5"
                        ></div>
                    </div>

                    <!-- Owner CTA -->
                    <div
                        class="rounded-2xl border-2 border-dashed border-amber-200 bg-amber-50 p-6 text-center"
                    >
                        <div class="mb-3 text-3xl">🏪</div>
                        <div class="mb-1 text-base font-bold text-stone-900">
                            Own a restaurant?
                        </div>
                        <p class="mb-4 text-sm leading-relaxed text-stone-500">
                            Get discovered by thousands of food lovers in your
                            city.
                        </p>
                        <button
                            class="rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-amber-700"
                        >
                            Get Listed Free →
                        </button>
                    </div>
                </aside>
            </div>
        </div>

        <!-- ══ FOOTER ══ -->
        <footer class="bg-stone-900 text-sm text-stone-400">
            <div
                class="mx-auto flex max-w-7xl flex-col flex-wrap items-center justify-between gap-4 px-4 py-10 sm:flex-row sm:px-6 lg:px-8"
            >
                <div class="font-display text-lg font-black text-white italic">
                    Food<span class="text-amber-400">Rank</span>
                </div>
                <div class="flex flex-wrap justify-center gap-6">
                    <a href="#" class="transition-colors hover:text-amber-400"
                        >About</a
                    >
                    <a href="#" class="transition-colors hover:text-amber-400"
                        >For Restaurants</a
                    >
                    <a href="#" class="transition-colors hover:text-amber-400"
                        >How It Works</a
                    >
                    <a href="#" class="transition-colors hover:text-amber-400"
                        >Privacy</a
                    >
                </div>
                <div class="text-xs text-stone-500">
                    © 2026 FoodRank. All rights reserved.
                </div>
            </div>
        </footer>

        <!-- ══ MOBILE BOTTOM NAV ══ -->
        <nav
            class="fixed right-0 bottom-0 left-0 z-50 border-t border-stone-200 bg-stone-50/95 backdrop-blur-md lg:hidden"
        >
            <div class="flex items-center justify-around px-2 pt-2 pb-4">
                <button
                    class="mnav flex flex-col items-center gap-1 px-4 text-amber-600"
                >
                    <svg
                        class="h-5 w-5"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                    </svg>
                    <span class="text-[10px] font-semibold">Home</span>
                </button>
                <button
                    class="mnav flex flex-col items-center gap-1 px-4 text-stone-400"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                        />
                    </svg>
                    <span class="text-[10px] font-semibold">Rankings</span>
                </button>
                <button
                    class="mnav flex flex-col items-center gap-1 px-4 text-stone-400"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        viewBox="0 0 24 24"
                    >
                        <rect x="3" y="3" width="7" height="7" rx="1" />
                        <rect x="14" y="3" width="7" height="7" rx="1" />
                        <rect x="3" y="14" width="7" height="7" rx="1" />
                        <circle cx="17.5" cy="17.5" r="3" />
                    </svg>
                    <span class="text-[10px] font-semibold">Vote</span>
                </button>
                <button
                    class="mnav flex flex-col items-center gap-1 px-4 text-stone-400"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                    </svg>
                    <span class="text-[10px] font-semibold">Nearby</span>
                </button>
                <button
                    class="mnav flex flex-col items-center gap-1 px-4 text-stone-400"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                    <span class="text-[10px] font-semibold">More</span>
                </button>
            </div>
        </nav>
        <div class="h-20 lg:hidden"></div>
    </GuestLayout>
</template>
