@if (session('success'))

    <script>
        new Noty({
            type: 'success',
            layout: 'topRight',
            text: "{{ session('success') }}",
            timeout: 2000,
            killer: true
        }).show();
    </script>
@elseif (session('fail'))
    <script>
        new Noty({
            type: 'error',
            layout: 'topRight',
            text: "{{ session('fail') }}",
            timeout: 2000,
            killer: true
        }).show();
    </script>
@endif
