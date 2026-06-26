<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
</script>

<template>
    <AppLayout>
        <section>
            <div class="text-center flex flex-col items-center gap-3">
                <p
                    class="text-sm font-semibold tracking-[0.18em] text-gold uppercase"
                >
                    HOW IT WORKS
                </p>

                <h2
                    class="text-2xl sm:text-4xl leading-tight font-bold tracking-tight"
                >
                    Built for real food lovers.
                </h2>

                <p
                    class="mx-auto text-sm leading-relaxed text-[#746b60]"
                >
                    No fake reviews. No influencers. Just real customers
                    deciding what deserves the top spot.
                </p>
            </div>

            <!-- Bento Grid -->
            <div class="mt-10 space-y-4">
                <!-- Large Hero Card -->
                <div
                    class="relative overflow-hidden rounded-[36px] bg-[#171717] p-6 text-white"
                >
                    <!-- Glow -->
                    <div
                        class="absolute -top-10 -right-10 h-44 w-44 rounded-full bg-[#caa24d]/20 blur-3xl"
                    ></div>

                    <div class="relative z-10">
                        <!-- Badge -->
                        <div
                            class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-2 text-[12px] font-medium"
                        >
                            <div
                                class="h-2 w-2 rounded-full bg-green-400"
                            ></div>

                            Live Voting System
                        </div>

                        <!-- Content -->
                        <h3
                            class="mt-6 max-w-[260px] text-[34px] leading-tight font-bold tracking-tight"
                        >
                            Visit. Scan. Vote
                        </h3>

                        <p
                            class="mt-4 max-w-[280px] text-[15px] leading-relaxed text-white/70"
                        >
                            Customers scan a QR code after dining and instantly
                            help restaurants climb the city leaderboard.
                        </p>

                        <!-- Stats -->
                        <div class="mt-8 grid grid-cols-3 gap-3">
                            <div
                                class="rounded-[22px] border border-white/10 bg-white/5 p-4"
                            >
                                <p class="text-[11px] text-white/50">
                                    QR Voting
                                </p>

                                <h4 class="mt-2 text-[18px] font-bold">
                                    Instant
                                </h4>
                            </div>

                            <div
                                class="rounded-[22px] border border-white/10 bg-white/5 p-4"
                            >
                                <p class="text-[11px] text-white/50">
                                    Accounts
                                </p>

                                <h4 class="mt-2 text-[18px] font-bold">Optional</h4>
                            </div>

                            <div
                                class="rounded-[22px] border border-white/10 bg-white/5 p-4"
                            >
                                <p class="text-[11px] text-white/50">Updates</p>

                                <h4
                                    class="mt-2 text-[18px] font-bold text-[#d7b66b]"
                                >
                                    Live
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Middle Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Card -->
                    <div
                        class="rounded-[30px] border border-[#ece4d8] bg-white p-5"
                    >
                        <!-- Icon -->
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-[18px] bg-[#f4ecdb] text-[22px]"
                        >
                            🔒
                        </div>

                        <h3
                            class="mt-5 text-[21px] font-bold tracking-tight text-[#171717]"
                        >
                            Anonymous voting
                        </h3>

                        <p
                            class="mt-3 text-[14px] leading-relaxed text-[#746b60]"
                        >
                            No accounts, profiles or public reviews required.
                        </p>

                        <!-- Pill -->
                        <div
                            class="mt-5 inline-flex rounded-full bg-[#f6f2ea] px-3 py-2 text-[12px] font-semibold text-[#8a6b2f]"
                        >
                            100% Private
                        </div>
                    </div>

                    <!-- Card -->
                    <div
                        class="rounded-[30px] border border-[#ece4d8] bg-[#faf8f3] p-5"
                    >
                        <!-- Icon -->
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-[18px] bg-[#171717] text-[22px] text-white"
                        >
                            ↺
                        </div>

                        <h3
                            class="mt-5 text-[21px] font-bold tracking-tight text-[#171717]"
                        >
                            Daily reset system
                        </h3>

                        <p
                            class="mt-3 text-[14px] leading-relaxed text-[#746b60]"
                        >
                            Rankings restart every midnight for fair
                            competition.
                        </p>

                        <!-- Pill -->
                        <div
                            class="mt-5 inline-flex rounded-full border border-[#ece4d8] bg-white px-3 py-2 text-[12px] font-semibold text-[#171717]"
                        >
                            Fresh Everyday
                        </div>
                    </div>
                </div>

                <!-- Bottom Horizontal Card -->
                <div
                    class="rounded-[34px] border border-[#ece4d8] bg-white p-6"
                >
                    <div class="flex items-start justify-between gap-4">
                        <!-- Left -->
                        <div>
                            <p
                                class="text-[12px] font-semibold tracking-[0.16em] text-[#9b7732] uppercase"
                            >
                                TRUST-BASED RANKINGS
                            </p>

                            <h3
                                class="mt-3 text-[30px] leading-tight font-bold tracking-tight text-[#171717]"
                            >
                                No fake reviews.<br />
                                No paid promotions.
                            </h3>

                            <p
                                class="mt-4 max-w-[280px] text-[15px] leading-relaxed text-[#746b60]"
                            >
                                Rankings are influenced only by real customer
                                visits and live dining experiences.
                            </p>
                        </div>

                        <!-- Right Icon -->
                        <div
                            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-[24px] bg-[#171717] text-[28px] text-white"
                        >
                            🏆
                        </div>
                    </div>

                    <!-- Trust Badges -->
                    <div class="mt-6 flex flex-wrap gap-2">
                        <div
                            class="rounded-full border border-[#ece4d8] bg-[#faf8f3] px-4 py-2 text-[13px] font-semibold text-[#171717]"
                        >
                            Real Customers
                        </div>

                        <div
                            class="rounded-full border border-[#ece4d8] bg-[#faf8f3] px-4 py-2 text-[13px] font-semibold text-[#171717]"
                        >
                            Fair Rankings
                        </div>

                        <div
                            class="rounded-full border border-[#ece4d8] bg-[#faf8f3] px-4 py-2 text-[13px] font-semibold text-[#171717]"
                        >
                            Live Competition
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
