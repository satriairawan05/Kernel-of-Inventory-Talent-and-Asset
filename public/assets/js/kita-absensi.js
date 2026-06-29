/**
 * KitaABSENSI - Main Application Script
 * Dependencies: Alpine.js 3.x, Bootstrap 5.x, jQuery 3.x
 */

var API_BASE_URL = '/api';

// ─── Helper Functions ──────────────────────────────────────────
function formatDate(dateStr) {
    var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    var date = new Date(dateStr);
    return days[date.getDay()] + ', ' + date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
}

function addMinutes(timeStr, minutes) {
    var parts = timeStr.split(':').map(Number);
    var total = parts[0] * 60 + parts[1] + minutes;
    var newH = String(Math.floor(total / 60) % 24).padStart(2, '0');
    var newM = String(total % 60).padStart(2, '0');
    return newH + ':' + newM;
}

// ─── API Helper (jQuery AJAX) ──────────────────────────────────
function apiAjax(url, method, data) {
    method = method || 'GET';
    return new Promise(function (resolve, reject) {
        $.ajax({
            url: API_BASE_URL + url,
            type: method,
            data: data || null,
            dataType: 'json',
            contentType: 'application/json',
            headers: { 'Accept': 'application/json' },
            success: function (response) {
                if (response && typeof response.success !== 'undefined') {
                    if (response.success) {
                        resolve(response.data);
                    } else {
                        reject(new Error(response.message || 'API Error'));
                    }
                } else {
                    resolve(response);
                }
            },
            error: function (xhr, status, error) {
                reject(new Error(error || 'Network error'));
            }
        });
    });
}

// ─── DUMMY DATA ─────────────────────────────────────────────────
function generateDummyHistory(lateMin) {
    var history = [];
    var today = new Date();
    var start = new Date(2026, 4, 20);
    var end = new Date(2026, 5, 27);
    if (end > today) end = today;
    var current = new Date(start);
    while (current <= end) {
        var dateStr = current.toISOString().split('T')[0];
        var status = 'on-time';
        if (lateMin > 15 && lateMin <= 30) status = 'late-5-15';
        else if (lateMin > 30) status = 'late-30-60';
        history.push({
            date: dateStr,
            date_formatted: formatDate(dateStr),
            shift: 'Sore',
            shift_start: '15:00',
            shift_end: '23:00',
            check_in: addMinutes('15:00', lateMin || 0),
            check_out: addMinutes('23:00', lateMin || 0),
            status: status
        });
        current.setDate(current.getDate() + 1);
    }
    history.sort(function (a, b) { return new Date(a.date) - new Date(b.date); });
    return history;
}

function getDummyEmployees() {
    return [
        // ===== My Fried Chicken (outlet_id: 1) =====
        // shift 1 = Pagi, shift 2 = Sore
        { id: 1, nama: 'Deuwi Satriya Irawan', outlet_id: 1, shift_id: 2, total_absen: 0, status: 'on-time', history: generateDummyHistory(0) },
        { id: 4, nama: 'Agus Wijaya', outlet_id: 1, shift_id: 1, total_absen: 0, status: 'on-time', history: generateDummyHistory(0) },
        { id: 7, nama: 'Dewi Lestari', outlet_id: 1, shift_id: 1, total_absen: 0, status: 'late-5-15', history: generateDummyHistory(10) },
        { id: 8, nama: 'Hendra Saputra', outlet_id: 1, shift_id: 2, total_absen: 0, status: 'on-time', history: generateDummyHistory(0) },
        // ===== Raja Kepiting (outlet_id: 2) =====
        // shift 3 = Pagi, shift 4 = Sore, shift 5 = Malam
        { id: 2, nama: 'Budi Santoso', outlet_id: 2, shift_id: 3, total_absen: 0, status: 'late-5-15', history: generateDummyHistory(15) },
        { id: 5, nama: 'Rina Andriani', outlet_id: 2, shift_id: 4, total_absen: 0, status: 'on-time', history: generateDummyHistory(0) },
        { id: 9, nama: 'Fajar Setiawan', outlet_id: 2, shift_id: 5, total_absen: 0, status: 'late-30-60', history: generateDummyHistory(45) },
        { id: 10, nama: 'Tuti Handayani', outlet_id: 2, shift_id: 3, total_absen: 0, status: 'on-time', history: generateDummyHistory(0) },
        // ===== Ayam Bebek Ganza (outlet_id: 3) =====
        // shift 6 = Pagi, shift 7 = Sore, shift 8 = Malam
        { id: 3, nama: 'Siti Rahayu', outlet_id: 3, shift_id: 7, total_absen: 0, status: 'late-30-60', history: generateDummyHistory(35) },
        { id: 6, nama: 'Joko Prasetyo', outlet_id: 3, shift_id: 6, total_absen: 0, status: 'on-time', history: generateDummyHistory(0) },
        { id: 11, nama: 'Maya Sari', outlet_id: 3, shift_id: 8, total_absen: 0, status: 'late-5-15', history: generateDummyHistory(20) },
        { id: 12, nama: 'Ahmad Fauzi', outlet_id: 3, shift_id: 6, total_absen: 0, status: 'on-time', history: generateDummyHistory(0) }
    ];
}

function getDummyEmployeeById(id) {
    var all = getDummyEmployees();
    return all.find(function (e) { return e.id === id; }) || null;
}

// ─── ALPINE COMPONENTS ─────────────────────────────────────────

document.addEventListener('alpine:init', function () {

    // ================================================================
    // 1. PRESENCE APP
    // ================================================================
    Alpine.data('presenceApp', function () {
        return {
            // ── State ──
            outlets: [],
            shifts: [],
            selectedOutletId: null,
            selectedShiftId: null,
            presenceType: 'masuk',
            name: window.KitaPOS?.user?.name || 'Guest',
            isSelf: true,
            isSubmitting: false,
            modalIcon: '✅',
            modalTitle: 'Presence Result',
            modalMessage: '',
            modalLate: false,
            modalLateMessage: '',
            modalPhoto: false,
            modalPhotoPreview: null,
            currentTime: '--:--:--',
            clockInterval: null,
            photoFile: null,
            photoPreview: null,
            loading: false,

            // ── Computed ──
            get shiftsForOutlet() {
                if (!this.selectedOutletId) return [];
                return this.shifts.filter(function (s) {
                    return s.company_id === parseInt(this.selectedOutletId);
                }.bind(this));
            },

            get isFormValid() {
                return this.selectedOutletId && this.selectedShiftId !== null && this.name.trim().length > 0;
            },

            // ── Lifecycle ──
            init: function () {
                this.updateClock();
                var self = this;
                this.clockInterval = setInterval(function () { self.updateClock(); }, 1000);
                this.loadOutlets();
            },

            updateClock: function () {
                var now = new Date();
                this.currentTime = String(now.getHours()).padStart(2, '0') + ':' +
                    String(now.getMinutes()).padStart(2, '0') + ':' +
                    String(now.getSeconds()).padStart(2, '0');
            },

            // ── API ──
            loadOutlets: function () {
                var self = this;
                this.loading = true;
                apiAjax('/companies')
                    .then(function (data) {
                        self.outlets = data.map(function (item) {
                            return { id: item.id, name: item.name };
                        });
                        if (self.outlets.length > 0) {
                            self.selectedOutletId = self.outlets[0].id;
                            self.loadShifts(self.outlets[0].id);
                        }
                        self.loading = false;
                    })
                    .catch(function (error) {
                        console.warn('Failed to load outlets:', error);
                        self._fallbackOutlets();
                        self.loading = false;
                    });
            },

            loadShifts: function (companyId) {
                var self = this;
                if (!companyId) return;
                this.loading = true;
                apiAjax('/shifts?company_id=' + companyId)
                    .then(function (data) {
                        self.shifts = data.map(function (item) {
                            return {
                                id: item.id,
                                company_id: item.company_id,
                                name: item.name,
                                start: item.start,
                                end: item.end,
                                code: item.code
                            };
                        });
                        self.selectedShiftId = null;
                        if (self.shifts.length > 0) {
                            self.selectedShiftId = self.shifts[0].id;
                        }
                        self.loading = false;
                    })
                    .catch(function (error) {
                        console.warn('Failed to load shifts:', error);
                        self._fallbackShifts(companyId);
                        self.loading = false;
                    });
            },

            _fallbackOutlets: function () {
                this.outlets = [
                    { id: 1, name: 'My Fried Chicken' },
                    { id: 2, name: 'Raja Kepiting' },
                    { id: 3, name: 'Ayam Bebek Ganza' }
                ];
                if (!this.selectedOutletId && this.outlets.length > 0) {
                    this.selectedOutletId = this.outlets[0].id;
                    this._fallbackShifts(this.selectedOutletId);
                }
            },

            _fallbackShifts: function (companyId) {
                var dummyMap = {
                    1: [
                        { id: 1, company_id: 1, name: 'Shift Pagi', start: '07:00', end: '15:00', code: 'PG' },
                        { id: 2, company_id: 1, name: 'Shift Sore', start: '15:00', end: '23:00', code: 'SR' }
                    ],
                    2: [
                        { id: 3, company_id: 2, name: 'Shift Pagi', start: '08:00', end: '16:00', code: 'PG' },
                        { id: 4, company_id: 2, name: 'Shift Sore', start: '16:00', end: '00:00', code: 'SR' },
                        { id: 5, company_id: 2, name: 'Shift Malam', start: '00:00', end: '08:00', code: 'ML' }
                    ],
                    3: [
                        { id: 6, company_id: 3, name: 'Shift Pagi', start: '08:00', end: '16:00', code: 'PG' },
                        { id: 7, company_id: 3, name: 'Shift Sore', start: '16:00', end: '00:00', code: 'SR' },
                        { id: 8, company_id: 3, name: 'Shift Malam', start: '00:00', end: '08:00', code: 'ML' }
                    ]
                };
                this.shifts = dummyMap[companyId] || [];
                this.selectedShiftId = null;
                if (this.shifts.length > 0) {
                    this.selectedShiftId = this.shifts[0].id;
                }
            },

            onOutletChange: function () {
                this.selectedShiftId = null;
                if (this.selectedOutletId) {
                    this.loadShifts(this.selectedOutletId);
                }
            },

            parseTimeToMinutes: function (timeStr) {
                var parts = timeStr.split(':').map(Number);
                return parts[0] * 60 + parts[1];
            },

            handlePhoto: function (event) {
                var file = event.target.files[0];
                if (!file) return;
                this.photoFile = file;
                var reader = new FileReader();
                var self = this;
                reader.onload = function (e) { self.photoPreview = e.target.result; };
                reader.readAsDataURL(file);
                event.target.value = '';
            },

            showPresenceModal: function (icon, title, message, late, lateMsg, photo) {
                this.modalIcon = icon || '✅';
                this.modalTitle = title || 'Presence Result';
                this.modalMessage = message || 'Thank you for your presence!';
                this.modalLate = late || false;
                this.modalLateMessage = lateMsg || '';
                this.modalPhoto = photo !== null && photo !== undefined;
                this.modalPhotoPreview = photo;

                var el = document.getElementById('presenceModal');
                if (el) {
                    try {
                        var existing = bootstrap.Modal.getInstance(el);
                        if (existing) existing.dispose();
                        var modal = new bootstrap.Modal(el);
                        modal.show();
                    } catch (e) {
                        console.warn('Modal error:', e);
                        alert(message);
                    }
                } else {
                    console.error('presenceModal not found');
                    alert(message);
                }
            },

            resetForm: function () {
                this.photoFile = null;
                this.photoPreview = null;
                if (this.$refs && this.$refs.photoInput) {
                    this.$refs.photoInput.value = '';
                }
                this.selectedShiftId = null;
                this.presenceType = 'masuk';
            },

            submitPresence: function () {
                if (!this.isFormValid || this.isSubmitting) return;
                this.isSubmitting = true;

                try {
                    var outlet = this.outlets.find(function (o) { return o.id === this.selectedOutletId; }.bind(this));
                    var shift = this.shifts.find(function (s) { return s.id === this.selectedShiftId; }.bind(this));
                    if (!outlet || !shift) {
                        this.showPresenceModal('❌', 'Error', 'Outlet or Shift not found.');
                        this.isSubmitting = false;
                        return;
                    }

                    var start = this.parseTimeToMinutes(shift.start);
                    var end = this.parseTimeToMinutes(shift.end);
                    var now = new Date();
                    var nowMin = now.getHours() * 60 + now.getMinutes();

                    var lateMin = 0, isLate = false;

                    if (this.presenceType === 'masuk') {
                        var diff = nowMin - start;
                        if (diff > 15) {
                            isLate = true;
                            lateMin = Math.floor(diff);
                        }
                    } else {
                        var diff = nowMin - end;
                        if (diff > 30) {
                            isLate = true;
                            lateMin = Math.floor(diff);
                        }
                    }

                    var label = this.presenceType === 'masuk' ? 'Check In' : 'Check Out';

                    var msg = 'Hello ' + this.name.trim() + ', your ' + label + ' has been successfully recorded! 🙏\n\n';
                    msg += '📍 Outlet: ' + outlet.name + '\n';
                    msg += '⏰ Shift: ' + shift.name + ' (' + shift.start + ' - ' + shift.end + ')\n\n';

                    if (isLate) {
                        msg += '⚠️ Status: Late by ' + lateMin + ' minute(s) for your scheduled shift.';
                    } else {
                        msg += '✅ Status: On Time. Great job, keep up the good work! 💪';
                    }

                    var lateMsg = isLate ?
                        '⏰ Late by ' + lateMin + ' minute(s) (Tolerance: ' + (this.presenceType === 'masuk' ? '15' : '30') + ' mins)' :
                        '';

                    this.showPresenceModal(
                        isLate ? '⚠️' : '✅',
                        isLate ? 'Presence Warning' : 'Presence Success',
                        msg,
                        isLate,
                        lateMsg,
                        this.photoPreview || null
                    );

                    this.resetForm();
                    this.isSubmitting = false;
                } catch (e) {
                    console.error(e);
                    this.showPresenceModal('❌', 'Error', 'A system error occurred. Please try again.');
                    this.isSubmitting = false;
                }
            },

            goHome: function () {
                window.location.assign('home');
            }
        };
    });

    // ================================================================
    // 2. REKAP APP (FIXED)
    // ================================================================
    Alpine.data('rekapApp', function () {
        return {
            // ── State ──
            currentTime: '--:--:--',
            clockInterval: null,
            outlets: [],
            shifts: [],
            selectedOutletId: null,
            selectedShiftId: null,
            filteredData: [],
            allData: [],
            loading: false,
            error: null,

            // ── Computed ──
            // Shift yang muncul di dropdown (hanya berdasarkan outlet yang dipilih)
            // Jika "All Outlets" dipilih, tampilkan semua shift
            get shiftForFilter() {
                // Jika belum ada outlet dipilih, tampilkan semua shift
                if (!this.selectedOutletId || this.selectedOutletId === '') {
                    return this.shifts;
                }
                var outletId = parseInt(this.selectedOutletId);
                var filtered = this.shifts.filter(function (s) {
                    return s.company_id === outletId;
                }.bind(this));
                return filtered;
            },

            // ── Lifecycle ──
            init: function () {
                this.updateClock();
                var self = this;
                this.clockInterval = setInterval(function () { self.updateClock(); }, 1000);

                // Urutan: load outlets -> setelah selesai, load shifts -> setelah selesai, load dummy data
                this.loadOutlets();
            },

            updateClock: function () {
                var now = new Date();
                this.currentTime = String(now.getHours()).padStart(2, '0') + ':' +
                    String(now.getMinutes()).padStart(2, '0') + ':' +
                    String(now.getSeconds()).padStart(2, '0');
            },

            // ── API ──
            loadOutlets: function () {
                var self = this;
                apiAjax('/companies')
                    .then(function (data) {
                        self.outlets = data.map(function (item) {
                            return { id: item.id, name: item.name };
                        });
                        if (self.outlets.length > 0 && !self.selectedOutletId) {
                            self.selectedOutletId = self.outlets[0].id;
                        }
                        self.loadShifts();
                    })
                    .catch(function (error) {
                        console.warn('Failed to load outlets:', error);
                        self.outlets = [
                            { id: 1, name: 'My Fried Chicken' },
                            { id: 2, name: 'Raja Kepiting' },
                            { id: 3, name: 'Ayam Bebek Ganza' }
                        ];
                        if (self.outlets.length > 0 && !self.selectedOutletId) {
                            self.selectedOutletId = self.outlets[0].id;
                        }
                        self.loadShifts();
                    });
            },

            loadShifts: function () {
                var self = this;
                apiAjax('/shifts')
                    .then(function (data) {
                        self.shifts = data.map(function (item) {
                            return {
                                id: item.id,
                                company_id: item.company_id,
                                name: item.name,
                                start: item.start,
                                end: item.end,
                                code: item.code
                            };
                        });
                        // Reset selected shift
                        self.selectedShiftId = null;
                        self.loadDummyData();
                    })
                    .catch(function (error) {
                        console.warn('Failed to load shifts:', error);
                        self.shifts = [
                            { id: 1, company_id: 1, name: 'Shift Pagi', start: '07:00', end: '15:00', code: 'PG' },
                            { id: 2, company_id: 1, name: 'Shift Sore', start: '15:00', end: '23:00', code: 'SR' },
                            { id: 3, company_id: 2, name: 'Shift Pagi', start: '08:00', end: '16:00', code: 'PG' },
                            { id: 4, company_id: 2, name: 'Shift Sore', start: '16:00', end: '00:00', code: 'SR' },
                            { id: 5, company_id: 2, name: 'Shift Malam', start: '00:00', end: '08:00', code: 'ML' },
                            { id: 6, company_id: 3, name: 'Shift Pagi', start: '08:00', end: '16:00', code: 'PG' },
                            { id: 7, company_id: 3, name: 'Shift Sore', start: '16:00', end: '00:00', code: 'SR' },
                            { id: 8, company_id: 3, name: 'Shift Malam', start: '00:00', end: '08:00', code: 'ML' }
                        ];
                        self.selectedShiftId = null;
                        self.loadDummyData();
                    });
            },

            loadDummyData: function () {
                var employees = getDummyEmployees();
                this.allData = employees.map(function (emp) {
                    return {
                        id: emp.id,
                        nama: emp.nama,
                        outlet_id: emp.outlet_id,
                        shift_id: emp.shift_id,
                        total_absen: emp.history ? emp.history.length : 0,
                        status: emp.status
                    };
                });
                this.applyFilter();
                this.loading = false;
            },

            // ── Filter ──
            applyFilter: function () {
                var filtered = this.allData ? this.allData.slice() : [];

                // Filter berdasarkan outlet (jika ada outlet yang dipilih)
                if (this.selectedOutletId !== null && this.selectedOutletId !== '' && this.selectedOutletId !== undefined) {
                    var outletId = parseInt(this.selectedOutletId);
                    filtered = filtered.filter(function (e) {
                        return e.outlet_id === outletId;
                    }.bind(this));
                }

                // Filter berdasarkan shift (jika ada shift yang dipilih)
                if (this.selectedShiftId !== null && this.selectedShiftId !== '' && this.selectedShiftId !== undefined) {
                    var shiftId = parseInt(this.selectedShiftId);
                    filtered = filtered.filter(function (e) {
                        return e.shift_id === shiftId;
                    }.bind(this));
                }

                this.filteredData = filtered;
            },

            onOutletChange: function () {
                // Reset shift saat outlet berubah
                this.selectedShiftId = null;
                this.applyFilter();
            },

            // ── Helpers ──
            getOutletName: function (outletId) {
                var found = this.outlets.find(function (o) { return o.id === parseInt(outletId); });
                return found ? found.name : '-';
            },

            getShiftName: function (shiftId) {
                var found = this.shifts.find(function (s) { return s.id === parseInt(shiftId); });
                return found ? found.name : '-';
            },

            getRowClass: function (status) {
                if (status === 'on-time') return 'table-success';
                if (status === 'late-5-15') return 'table-warning';
                if (status === 'late-30-60') return 'table-danger';
                return '';
            },

            getBadgeClass: function (status) {
                if (status === 'on-time') return 'bg-success';
                if (status === 'late-5-15') return 'bg-warning text-dark';
                if (status === 'late-30-60') return 'bg-danger';
                return 'bg-secondary';
            },

            getStatusLabel: function (status) {
                if (status === 'on-time') return 'On Time';
                if (status === 'late-5-15') return 'Late 5-15 min';
                if (status === 'late-30-60') return 'Late 30-60 min';
                return '-';
            },

            viewDetail: function (id) {
                var params = new URLSearchParams();
                params.append('id', id);
                if (this.selectedOutletId) params.append('outlet', this.selectedOutletId);
                if (this.selectedShiftId) params.append('shift', this.selectedShiftId);
                window.location.href = 'detail.html?' + params.toString();
            },

            goHome: function () {
                window.location.assign('home');
            }
        };
    });

    // ================================================================
    // 3. DETAIL APP
    // ================================================================
    Alpine.data('detailApp', function () {
        return {
            currentTime: '--:--:--',
            clockInterval: null,
            employee: null,
            loading: true,
            employeeId: null,
            outletId: null,
            shiftId: null,
            outlets: [],
            shifts: [],
            currentDate: null,
            calendarDays: [],
            dayStatusMap: {},
            minDate: null,
            maxDate: null,

            get canPrev() {
                if (!this.currentDate || !this.minDate) return false;
                var currentMonth = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
                var minMonth = new Date(this.minDate.getFullYear(), this.minDate.getMonth(), 1);
                return currentMonth > minMonth;
            },

            get canNext() {
                if (!this.currentDate || !this.maxDate) return false;
                var currentMonth = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
                var maxMonth = new Date(this.maxDate.getFullYear(), this.maxDate.getMonth(), 1);
                return currentMonth < maxMonth;
            },

            init: function () {
                this.updateClock();
                var self = this;
                this.clockInterval = setInterval(function () { self.updateClock(); }, 1000);

                var params = new URLSearchParams(window.location.search);
                var idParam = params.get('id');
                if (!idParam) {
                    this.loading = false;
                    this.employee = null;
                    return;
                }
                this.employeeId = parseInt(idParam);
                if (isNaN(this.employeeId)) {
                    this.loading = false;
                    this.employee = null;
                    return;
                }
                this.outletId = parseInt(params.get('outlet')) || null;
                this.shiftId = parseInt(params.get('shift')) || null;

                this.loadOutletsAndShifts().then(function () {
                    self.loadDummyDetail();
                }).catch(function () {
                    self.loadDummyDetail();
                });
            },

            updateClock: function () {
                var now = new Date();
                this.currentTime = String(now.getHours()).padStart(2, '0') + ':' +
                    String(now.getMinutes()).padStart(2, '0') + ':' +
                    String(now.getSeconds()).padStart(2, '0');
            },

            loadOutletsAndShifts: function () {
                var self = this;
                return Promise.all([
                    apiAjax('/companies'),
                    apiAjax('/shifts')
                ]).then(function (results) {
                    var companiesData = results[0];
                    var shiftsData = results[1];

                    self.outlets = companiesData.map(function (item) {
                        return { id: item.id, name: item.name };
                    });

                    self.shifts = shiftsData.map(function (item) {
                        return {
                            id: item.id,
                            company_id: item.company_id,
                            name: item.name,
                            start: item.start,
                            end: item.end,
                            code: item.code
                        };
                    });
                }).catch(function (error) {
                    console.warn('Failed to load outlets or shifts:', error);
                    self.outlets = [
                        { id: 1, name: 'My Fried Chicken' },
                        { id: 2, name: 'Raja Kepiting' },
                        { id: 3, name: 'Ayam Bebek Ganza' }
                    ];
                    self.shifts = [
                        { id: 1, name: 'Pagi' },
                        { id: 2, name: 'Sore' },
                        { id: 3, name: 'Malam' }
                    ];
                });
            },

            loadDummyDetail: function () {
                var empData = getDummyEmployeeById(this.employeeId);
                if (!empData) {
                    this.loading = false;
                    this.employee = null;
                    return;
                }

                var outlet = this.outlets.find(function (o) { return o.id === empData.outlet_id; });
                var shift = this.shifts.find(function (s) { return s.id === empData.shift_id; });

                this.employee = {
                    id: empData.id,
                    nama: empData.nama,
                    outlet_id: empData.outlet_id,
                    shift_id: empData.shift_id,
                    total_absen: empData.history ? empData.history.length : 0,
                    status: empData.status,
                    outlet_name: outlet ? outlet.name : 'Unknown',
                    shift_name: shift ? shift.name : 'Unknown',
                    history: empData.history || []
                };

                this.dayStatusMap = {};
                if (empData.history) {
                    empData.history.forEach(function (log) {
                        this.dayStatusMap[log.date] = log.status;
                    }.bind(this));
                }

                var history = empData.history || [];
                if (history.length > 0) {
                    var firstDate = new Date(history[0].date);
                    var lastDate = new Date(history[history.length - 1].date);
                    this.minDate = new Date(firstDate.getFullYear(), firstDate.getMonth(), 1);
                    this.maxDate = new Date(lastDate.getFullYear(), lastDate.getMonth(), 1);
                } else {
                    this.minDate = new Date(2026, 4, 1);
                    this.maxDate = new Date(2026, 5, 1);
                }

                if (!this.currentDate || this.currentDate < this.minDate) {
                    this.currentDate = new Date(this.minDate);
                }
                this.renderCalendar();
                this.loading = false;
            },

            renderCalendar: function () {
                if (!this.currentDate) return;
                var year = this.currentDate.getFullYear();
                var month = this.currentDate.getMonth();
                var firstDay = new Date(year, month, 1).getDay();
                var daysInMonth = new Date(year, month + 1, 0).getDate();
                var today = new Date();
                today.setHours(0, 0, 0, 0);
                var days = [];
                for (var i = 0; i < firstDay; i++) {
                    days.push(null);
                }
                for (var d = 1; d <= daysInMonth; d++) {
                    var dateObj = new Date(year, month, d);
                    var dateStr = dateObj.toISOString().split('T')[0];
                    var status = this.dayStatusMap[dateStr] || null;
                    var isPast = dateObj <= today;
                    days.push({
                        date: d,
                        dateStr: dateStr,
                        status: isPast ? status : null,
                        isToday: dateObj.toDateString() === today.toDateString(),
                        isPast: isPast
                    });
                }
                while (days.length < 42) {
                    days.push(null);
                }
                this.calendarDays = days;
            },

            prevMonth: function () {
                var newDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
                if (newDate >= this.minDate) {
                    this.currentDate = newDate;
                    this.renderCalendar();
                }
            },

            nextMonth: function () {
                var newDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
                if (newDate <= this.maxDate) {
                    this.currentDate = newDate;
                    this.renderCalendar();
                }
            },

            get currentMonthYear() {
                if (!this.currentDate) return '';
                var months = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];
                return months[this.currentDate.getMonth()] + ' ' + this.currentDate.getFullYear();
            },

            getDayClass: function (day) {
                if (!day) return 'empty-cell';
                if (!day.isPast) return 'future-date';
                if (!day.status) return 'bg-calendar-no-data';
                if (day.status === 'on-time') return 'bg-calendar-on-time';
                if (day.status === 'late-5-15') return 'bg-calendar-late-5-15';
                if (day.status === 'late-30-60') return 'bg-calendar-late-30-60';
                return 'bg-calendar-no-data';
            },

            getBadgeClass: function (status) {
                if (status === 'on-time') return 'bg-success';
                if (status === 'late-5-15') return 'bg-warning text-dark';
                if (status === 'late-30-60') return 'bg-danger';
                return 'bg-secondary';
            },

            getStatusLabel: function (status) {
                if (status === 'on-time') return 'On Time';
                if (status === 'late-5-15') return 'Late 5-15';
                if (status === 'late-30-60') return 'Late 30-60';
                return '-';
            },

            goHome: function () {
                window.location.assign('home');
            }
        };
    });

}); // end alpine:init