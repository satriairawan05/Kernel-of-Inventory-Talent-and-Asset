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
                <!-- parent pages-->
                <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="user-check"></span></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Presences</span></span></div>
                    </a>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="file-plus"></span></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Reports</span></span></div>
                    </a>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="archive"></span></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">SOP</span></span></div>
                    </a>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="credit-card"></span></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Payroll</span></span></div>
                    </a>
                </div><!-- parent pages--><!-- parent pages-->
                <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-recruitmens"
                        role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-recruitmens">
                        <div class="d-flex align-items-center">
                            <div class="dropdown-indicator-icon-wrapper"><span
                                    class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                class="nav-link-icon"><span data-feather="user-plus"></span></span><span
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
                <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="dollar-sign"></span></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Point of Sales</span></span>
                        </div>
                    </a>
                </div><!-- parent pages-->
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
                                class="nav-link-icon"><span data-feather="file-text"></span></span><span
                                class="nav-link-text">Reports</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-reports">
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
            </li>
        @endif
        @if (request()->routeIs('inventory.*'))
            <li class="nav-item">
                <!-- label-->
                <p class="navbar-vertical-label">Inventories</p>
                <hr class="navbar-vertical-line" />
                <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-masters"
                        role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-masters">
                        <div class="d-flex align-items-center">
                            <div class="dropdown-indicator-icon-wrapper"><span
                                    class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                class="nav-link-icon"><span data-feather="box"></span></span><span
                                class="nav-link-text">Masted Data</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-masters">
                            <li class="collapsed-nav-item-title d-none">Master Data</li>
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('inventory.category.*') ? 'active' : '' }}" href="{{ route('inventory.category.index') }}">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Category</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Product</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Stock</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-ingoods"
                        role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-ingoods">
                        <div class="d-flex align-items-center">
                            <div class="dropdown-indicator-icon-wrapper"><span
                                    class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                class="nav-link-icon"><span data-feather="box"></span></span><span
                                class="nav-link-text">Incoming Goods</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-ingoods">
                            <li class="collapsed-nav-item-title d-none">Incoming Goods</li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">New Transaction</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Transaction</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-returnitems"
                        role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-returnitems">
                        <div class="d-flex align-items-center">
                            <div class="dropdown-indicator-icon-wrapper"><span
                                    class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                class="nav-link-icon"><span data-feather="box"></span></span><span
                                class="nav-link-text">Exit Items</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-returnitems">
                            <li class="collapsed-nav-item-title d-none">Exit Items</li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">New Transaction</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Transaction</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-exititems"
                        role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-exititems">
                        <div class="d-flex align-items-center">
                            <div class="dropdown-indicator-icon-wrapper"><span
                                    class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                class="nav-link-icon"><span data-feather="box"></span></span><span
                                class="nav-link-text">Return Items</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-exititems">
                            <li class="collapsed-nav-item-title d-none">Return Items</li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">New Transaction</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Transaction</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-stockopnames"
                        role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-stockopnames">
                        <div class="d-flex align-items-center">
                            <div class="dropdown-indicator-icon-wrapper"><span
                                    class="fas fa-caret-right dropdown-indicator-icon"></span></div><span
                                class="nav-link-icon"><span data-feather="archive"></span></span><span
                                class="nav-link-text">Stock Opnames</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-stockopnames">
                            <li class="collapsed-nav-item-title d-none">Stock Opnames</li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">New Report</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#">
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
                                class="nav-link-icon"><span data-feather="file-text"></span></span><span
                                class="nav-link-text">Reports</span>
                        </div>
                    </a>
                    <div class="parent-wrapper label-1">
                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-reports">
                            <li class="collapsed-nav-item-title d-none">Reports</li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Daily</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Weekly</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#">
                                    <div class="d-flex align-items-center"><span class="nav-link-text">Monthly</span>
                                    </div>
                                </a><!-- more inner pages-->
                            </li>
                        </ul>
                    </div>
                </div
            </li>
        @endif
        @if (request()->routeIs('setting.*'))
            <li class="nav-item"><!-- parent pages-->
                <p class="navbar-vertical-label">System Settings</p>
                <hr class="navbar-vertical-line" />
                <div class="nav-item-wrapper"><a class="nav-link {{ Request::routeIs('setting.company.*') ? 'active' : '' }} label-1" href="{{ route('setting.company.index') }}" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="map"></span></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Companies</span></span></div>
                    </a>
                </div>
                <!-- label-->
                <div class="nav-item-wrapper"><a class="nav-link {{ Request::routeIs('setting.shift.*') ? 'active' : '' }} label-1" href="{{ route('setting.shift.index') }}" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="clock"></span></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Shift</span></span></div>
                    </a>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link {{ Request::routeIs('setting.unit.*') ? 'active' : '' }} label-1" href="{{ route('setting.unit.index') }}" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="server"></span></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Unit</span></span></div>
                    </a>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    class="fas fa-tools ms-1 me-1 fa-lg"></span></span><span
                                class="nav-link-text-wrapper"><span class="nav-link-text">Roles</span></span></div>
                    </a>
                </div>
                <div class="nav-item-wrapper"><a class="nav-link {{ Request::routeIs('setting.account.*') ? 'active' : '' }} label-1" href="{{ route('setting.account.index') }}" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="user-check"></span></span><span class="nav-link-text-wrapper"><span
                                    class="nav-link-text">Accounts</span></span></div>
                    </a>
                </div>
                <!-- parent pages-->
            </li>
        @endif
    </ul>
</div>
