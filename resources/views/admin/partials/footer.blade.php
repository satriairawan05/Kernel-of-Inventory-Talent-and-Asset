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