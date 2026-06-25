@props(['variant' => 'desktop'])

@php
    $cardClass = ($variant === 'desktop') ? 'draft-panel card shadow-sm mb-3' : 'card border-danger mx-3 mt-4 mb-3';
    $headerClass = ($variant === 'desktop') ? 'card-header d-flex justify-content-between align-items-center py-2' : 'card-header bg-danger text-white fw-bold py-2 d-flex justify-content-between';
    $headerBg = ($variant === 'desktop') ? '' : 'bg-danger text-white';
@endphp

<div x-data="draftSessionsComponent">
    <div class="{{ $cardClass }}">
        <div class="{{ $headerClass }}">
            <span class="fw-bold"><i class="bi bi-file-earmark-text"></i> Draft Sessions</span>
            <button class="btn btn-sm btn-light" @click="$store.pos.openNewSessionModal()"
                title="Create New Order">
                <i class="bi bi-plus-circle"></i> New
            </button>
        </div>
        <div class="card-body p-2 style-scrollbar" style="max-height: 320px; overflow-y: auto;">
            <template x-if="!$store.pos.sessions || $store.pos.sessions.length === 0">
                <div class="text-center text-muted py-3" style="font-size: 13px;">
                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                    There are no order drafts yet.
                </div>
            </template>
            <template x-for="session in $store.pos.sessions" :key="session.id">
                <div class="session-item border-bottom pb-2 mb-2"
                    :class="{ 'active-session': session.id === $store.pos.activeSessionId }">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="fw-bold" x-text="session.name"></span>
                            <span class="badge bg-secondary ms-1" x-text="session.typeLabel"></span>
                            <div class="small text-muted" x-text="session.items.length + ' item(s)'"></div>
                        </div>
                        <div class="d-flex gap-1" @click.stop>
                            <button class="btn btn-sm btn-outline-primary py-0 px-2"
                                @click="$store.pos.setActiveSession(session.id)" title="Set active">
                                <i class="bi bi-check2-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info py-0 px-2"
                                @click="$store.pos.openSessionDetailModal(session.id)" title="See Detail">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-success py-0 px-2"
                                @click="$store.pos.confirmSessionToCart(session.id)" title="Go To Cart">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                            <button class="btn btn-sm btn-link text-danger p-0 ms-1"
                                @click="$store.pos.removeSession(session.id)" title="Delete Session">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="small fw-bold text-end mt-1"
                        x-text="'Subtotal: Rp ' + $store.pos.formatRupiah($store.pos.getSessionTotal(session.id))">
                    </div>
                </div>
            </template>
            <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2" style="font-size: 0.95rem;">
                <span><i class="bi bi-cash-stack"></i> Grand Total All Sessions</span>
                <span style="color: var(--pos-primary);"
                    x-text="'Rp ' + $store.pos.formatRupiah($store.pos.getTotalSessionsTotal())"></span>
            </div>
        </div>
    </div>
</div>