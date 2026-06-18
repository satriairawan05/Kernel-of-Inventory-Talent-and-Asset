<!-- ===============================================-->
<!--    JavaScripts-->
<!-- ===============================================-->
<script src="{{ asset('vendors/popper/popper.min.js') }}"></script>
<script src="{{ asset('vendors/bootstrap/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendors/anchorjs/anchor.min.js') }}"></script>
<script src="{{ asset('vendors/is/is.min.js') }}"></script>
<script src="{{ asset('vendors/fontawesome/all.min.js') }}"></script>
<script src="{{ asset('vendors/lodash/lodash.min.js') }}"></script>
<script src="{{ asset('vendors/list.js/list.min.js') }}"></script>
<script src="{{ asset('vendors/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('vendors/dayjs/dayjs.min.js') }}"></script>
<script src="{{ asset('assets/js/phoenix.js') }}"></script>

@stack('js')

<!-- Select2 JS (letakkan setelah jQuery dan Bootstrap) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.js">
</script>

<script>
    $(document).ready(function() {
        // Inisialisasi semua select dengan class 'select2'
        $('select.select2').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih opsi...',
            allowClear: true,
            width: '100%'
        });

        // Jika ada select yang di-render ulang (misal via AJAX, modal), jalankan ulang
        // Menggunakan observer untuk menangani elemen yang muncul dinamis
        $(document).on('select2:open', function() {
            // Jika perlu, bisa handle event lain
        });
    });

    // Fungsi untuk re-init Select2 pada elemen baru (misal setelah modal muncul)
    function reinitSelect2(container) {
        container = container || document;
        $('select.select2', container).each(function() {
            if (!$(this).data('select2')) {
                $(this).select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih opsi...',
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        setTimeout(() => {

            document.querySelectorAll('.alert').forEach(alert => {

                bootstrap.Alert.getOrCreateInstance(alert)
                    .close();

            });

        }, 5000);

    });
</script>
</body>


<!-- Mirrored from prium.github.io/phoenix/v1.24.0/pages/starter.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 Jun 2026 07:08:50 GMT -->

</html>
