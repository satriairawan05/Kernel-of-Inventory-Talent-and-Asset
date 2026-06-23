<div class="navbar-vertical-content">
    <ul class="navbar-nav flex-column" id="navbarVerticalNav">
        <li class="nav-item">
            <!-- parent pages-->
            <div class="nav-item-wrapper"><a class="nav-link label-1"
                    href="{{ route('home') }}" role="button" data-bs-toggle="" aria-expanded="false">
                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                data-feather="home"></span></span><span class="nav-link-text-wrapper"><span
                                class="nav-link-text">Home</span></span></div>
                </a>
            </div>
        </li>
        @if (request()->routeIs('hr.*'))
            <li class="nav-item">
                <!-- label-->
                <p class="navbar-vertical-label">Human Resources</p>
                <hr class="navbar-vertical-line" /><!-- parent pages-->
                <a class="nav-link label-1" href="{{ route('hr.home') }}" role="button" data-bs-toggle="" aria-expanded="false">
                    <div class="d-flex align-items-center"><span class="nav-link-icon">
                        <i class="fas fa-users"></i>
                    </span><span class="nav-link-text-wrapper"><span class="nav-link-text">Home (HR)</span></span></div>
                </a>
                <!-- parent pages-->
                <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-fingerprint"></i></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Presences</span></span></div>
                    </a>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-chart-line"></i></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Reports</span></span></div>
                    </a>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-file-alt"></i></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">SOP</span></span></div>
                    </a>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-coins"></i></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Payroll</span></span></div>
                    </a>
                </div><!-- parent pages--><!-- parent pages-->
                <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-recruitmens"
                        role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-recruitmens">
                        <div class="d-flex align-items-center">
                            <div class="dropdown-indicator-icon-wrapper"><span
                                    class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                class="nav-link-icon"><i class="fas fa-user-plus"></i></span><span
                                class="nav-link-text">Recruitmens</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-recruitmens">
                            <li class="collapsed-nav-item-title d-none">Recruitmens</li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Career</span></div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Interview</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Employee</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                        </ul>
                    </div>
                </div><!-- parent pages--><!-- parent pages-->
            </li>
        @endif
        @if (request()->routeIs('pos.*'))
            <li class="nav-item">
                <!-- label-->
                <p class="navbar-vertical-label">Sales</p>
                <hr class="navbar-vertical-line" /><!-- parent pages-->
                <a class="nav-link label-1" href="{{ route('pos.home') }}" role="button" data-bs-toggle="" aria-expanded="false">
                    <div class="d-flex align-items-center"><span class="nav-link-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </span><span class="nav-link-text-wrapper"><span class="nav-link-text">Home (POS)</span></span></div>
                </a>
                <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-cash-register"></i></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Point of Sales</span></span>
                        </div>
                    </a>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-file-invoice-dollar"></i></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Cash Summary</span></span>
                        </div>
                    </a>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-pos-reports"
                        role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-pos-reports">
                        <div class="d-flex align-items-center">
                            <div class="dropdown-indicator-icon-wrapper"><span
                                    class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                class="nav-link-icon"><i class="fas fa-chart-bar"></i></span><span
                                class="nav-link-text">Sales Reports (POS)</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-pos-reports">
                            <li class="collapsed-nav-item-title d-none">Reports</li>
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('pos.report.*') }}" href="{{ route('pos.report.daily') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Daily</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('pos.report.*') }}" href="{{ route('pos.report.weekly') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Weekly</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('pos.report.*') }}" href="{{ route('pos.report.monthly') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Monthly</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                        </ul>
                    </div>
                </div>
                <hr>
                <!-- parent pages-->
                {{-- <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="gift"></span></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Products</span></span></div>
                    </a>
                </div> --}}
                <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-reports"
                        role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-reports">
                        <div class="d-flex align-items-center">
                            <div class="dropdown-indicator-icon-wrapper"><span
                                    class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                class="nav-link-icon"><i class="fas fa-chart-line"></i></span><span
                                class="nav-link-text">Sales Reports</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-reports">
                            <li class="collapsed-nav-item-title d-none">Reports</li>
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('pos.report.*') ? 'active' : '' }}" href="{{ route('pos.report.daily') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Daily</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('pos.report.*') ? 'active' : ''}}" href="{{ route('pos.report.weekly') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Weekly</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('pos.report.*') ? 'active' : '' }}" href="{{ route('pos.report.monthly') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Monthly</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
        @endif
        @if (request()->routeIs('inventory.*'))
            <li class="nav-item">
                <!-- label-->
                <p class="navbar-vertical-label">Inventories</p>
                <hr class="navbar-vertical-line" />
                <a class="nav-link label-1" href="{{ route('inventory.home') }}" role="button" data-bs-toggle="" aria-expanded="false">
                    <div class="d-flex align-items-center"><span class="nav-link-icon">
                        <i class="fas fa-archive"></i>
                    </span><span class="nav-link-text-wrapper"><span class="nav-link-text">Home (Inventory)</span></span></div>
                </a>
                <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-masters"
                        role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-masters">
                        <div class="d-flex align-items-center">
                            <div class="dropdown-indicator-icon-wrapper"><span
                                    class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                class="nav-link-icon"><i class="fas fa-database"></i></span><span
                                class="nav-link-text">Master Data</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-masters">
                            <li class="collapsed-nav-item-title d-none">Master Data</li>
                            {{-- <li class="nav-item"><a class="nav-link {{ Request::routeIs('inventory.category.*') ? 'active' : '' }}" href="{{ route('inventory.category.index') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Category</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li> --}}
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('inventory.product.*') ? 'active' : '' }}" href="{{ route('inventory.product.index') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Product</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('inventory.stock.*') ? 'active' : '' }}" href="{{ route('inventory.stock.index') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Stock</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nav-ingoods"
                        role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nav-ingoods">
                        <div class="d-flex align-items-center">
                            <div class="dropdown-indicator-icon-wrapper"><span
                                    class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                class="nav-link-icon"><i class="fas fa-download"></i></span><span
                                class="nav-link-text">Incoming Good</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nav-ingoods">
                            <li class="collapsed-nav-item-title d-none">Incoming Good</li>
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('inventory.stock.create') ? 'active' : '' }}" href="{{ route('inventory.stock-in.create') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">New Transaction</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('inventory.stock.*') ? 'active' : '' }}" href="{{ route('inventory.stock-in.index') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Transaction</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nav-exititems"
                        role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nav-exititems">
                        <div class="d-flex align-items-center">
                            <div class="dropdown-indicator-icon-wrapper"><span
                                    class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                class="nav-link-icon"><i class="fas fa-upload"></i></span><span
                                class="nav-link-text">Exit Items</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nav-exititems">
                            <li class="collapsed-nav-item-title d-none">Exit Items</li>
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('inventory.stock-out.create') ? 'active' : '' }}" href="{{ route('inventory.stock-out.create') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">New Transaction</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('inventory.stock-out.*') ? 'active' : '' }}" href="{{ route('inventory.stock-out.index') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Transaction</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nav-returnitems"
                        role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nav-returnitems">
                        <div class="d-flex align-items-center">
                            <div class="dropdown-indicator-icon-wrapper"><span
                                    class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                class="nav-link-icon"><i class="fas fa-undo-alt"></i></span><span
                                class="nav-link-text">Return Items</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nav-returnitems">
                            <li class="collapsed-nav-item-title d-none">Return Items</li>
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('inventory.return-stock.create') ? 'active' : '' }}" href="{{ route('inventory.return-stock.create') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">New Transaction</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('inventory.return-stock.create') ? 'active' : '' }}" href="{{ route('inventory.return-stock.index') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Transaction</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nav-stockopnames"
                        role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nav-stockopnames">
                        <div class="d-flex align-items-center">
                            <div class="dropdown-indicator-icon-wrapper"><span
                                    class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                class="nav-link-icon"><i class="fas fa-clipboard-list"></i></span><span
                                class="nav-link-text">Stock Opnames</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nav-stockopnames">
                            <li class="collapsed-nav-item-title d-none">Stock Opnames</li>
                            {{-- <li class="nav-item"><a class="nav-link" href="{{ route('inventory.stock-opname.create') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">New Report</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li> --}}
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('inventory.stock-opname.*') ? 'active' : '' }}" href="{{ route('inventory.stock-opname.index') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Reports</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                        </ul>
                    </div>
                </div><!-- parent pages-->
                <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-reports"
                        role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-reports">
                        <div class="d-flex align-items-center">
                            <div class="dropdown-indicator-icon-wrapper"><span
                                    class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                class="nav-link-icon"><i class="fas fa-chart-pie"></i></span><span
                                class="nav-link-text">Reports</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-reports">
                            <li class="collapsed-nav-item-title d-none">Reports</li>
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('inventory.report.*') ? 'active' : '' }}" href="{{ route('inventory.report.index') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Report</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            {{-- <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Weekly</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Monthly</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li> --}}
                        </ul>
                    </div>
                </div>
                <a class="nav-link label-1" href="{{ route('inventory.stock.logs') }}" role="button" data-bs-toggle="" aria-expanded="false">
                    <div class="d-flex align-items-center"><span class="nav-link-icon">
                        <i class="fas fa-file-archive"></i>
                    </span><span class="nav-link-text-wrapper"><span class="nav-link-text">Logs</span></span></div>
                </a>
            </li>
        @endif
        @if (request()->routeIs('setting.*'))
            <li class="nav-item"><!-- parent pages-->
                <p class="navbar-vertical-label">System Settings</p>
                <hr class="navbar-vertical-line" />
                <a class="nav-link label-1" href="{{ route('setting.home') }}" role="button" data-bs-toggle="" aria-expanded="false">
                    <div class="d-flex align-items-center"><span class="nav-link-icon">
                        <i class="fas fa-cogs"></i>
                    </span><span class="nav-link-text-wrapper"><span class="nav-link-text">Home (System Setting)</span></span></div>
                </a>
                <div class="nav-item-wrapper"><a class="nav-link {{ Request::routeIs('setting.company.*') ? 'active' : '' }} label-1" href="{{ route('setting.company.index') }}" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-store"></i></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Outlet</span></span></div>
                    </a>
                </div>
                <!-- label-->
                <div class="nav-item-wrapper"><a class="nav-link {{ Request::routeIs('setting.shift.*') ? 'active' : '' }} label-1" href="{{ route('setting.shift.index') }}" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-clock"></i></span></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Shift</span></span></div>
                    </a>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link {{ Request::routeIs('setting.unit.*') ? 'active' : '' }} label-1" href="{{ route('setting.unit.index') }}" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-ruler-combined"></i></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Unit</span></span></div>
                    </a>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link {{ Request::routeIs('setting.role.*') ? 'active' : '' }} label-1" href="{{ route('setting.role.index') }}" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    class="fas fa-user-cog"></span></span><span
                                class="nav-link-text-wrapper"><span class="nav-link-text">Role and Permission</span></span></div>
                    </a>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link {{ Request::routeIs('setting.account.*') ? 'active' : '' }} label-1" href="{{ route('setting.account.index') }}" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-user-circle"></i></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Accounts</span></span></div>
                    </a>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-cog"></i></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">System Setting</span></span></div>
                    </a>
                </div>
                <!-- parent pages-->
            </li>
        @endif
    </ul>
</div>
